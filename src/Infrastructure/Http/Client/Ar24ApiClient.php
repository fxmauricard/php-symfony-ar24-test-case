<?php

namespace App\Infrastructure\Http\Client;

use App\Infrastructure\Http\Client\Enum\Ar24ResponseStatus;
use App\Infrastructure\Http\Client\Exception\Ar24ApiException;
use App\Infrastructure\Http\Client\Exception\Ar24DateException;
use App\Infrastructure\Http\Client\Exception\Ar24TokenException;
use App\Infrastructure\Security\Ar24HeadersFactory;
use App\Infrastructure\Security\Ar24ResponseDecrypter;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Client for interacting with the AR24 API.
 */
final class Ar24ApiClient
{
    /**
     * Default error map for handling common AR24 API errors.
     *
     * @var array<string, array{0: class-string<Ar24ApiException>, 1: string}>
     */
    private const array DEFAULT_ERROR_MAP = [
        'unknown_error' => [Ar24ApiException::class, 'An unknown error occurred'],
        'status_invalid' => [Ar24ApiException::class, 'The status parameter is not valid'],
        'token_invalid' => [Ar24TokenException::class, 'Your token is not valid'],
        'token_missing' => [Ar24TokenException::class, 'The token is missing in your request'],
        'empty_date' => [Ar24DateException::class, 'No date parameter found in your request'],
        'invalid_date' => [Ar24DateException::class, 'Wrong date format (must be YYYY-MM-DD HH:mm:ss)'],
        'expired_date' => [Ar24DateException::class, 'Given datetime is older than current datetime'],
        'date_in_future' => [Ar24DateException::class, 'Given datetime must be set between call submission and +10 minutes'],
    ];

    /**
     * @var string Base URL for the AR24 API.
     */
    private string $baseUrl;
    /**
     * @var string AR24 API token.
     */
    private string $token;

    /**
     * Constructor for the Ar24ApiClient.
     *
     * @param HttpClientInterface $client The HTTP client to use for requests.
     * @param Ar24HeadersFactory $headersFactory Factory for creating AR24-specific headers.
     * @param Ar24ResponseDecrypter $responseDecrypter Service for decrypting AR24 API responses.
     * @param LoggerInterface $logger Logger for logging requests and responses.
     * @param string $baseUrl The base URL for the AR24 API.
     * @param string $token The AR24 API token.
     */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly Ar24HeadersFactory $headersFactory,
        private readonly Ar24ResponseDecrypter $responseDecrypter,
        private readonly LoggerInterface $logger,
        string $baseUrl,
        string $token
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }

    /**
     * Sends an HTTP request to the AR24 API with the specified method, URL, and options.
     * Handles encryption, decryption, logging, and error handling for requests and responses.
     *
     * @param string $method The HTTP method to use for the request (e.g., 'GET', 'POST').
     * @param string $endpoint The endpoint path for the API request.
     * @param array $options Optional parameters to customize the request, including headers, query, and timeout settings.
     * @param array $errorMap A map of error codes to [ExceptionClass, Message].
     *
     * @return array The decoded response content from the API. If the response is encrypted, it will be decrypted before returning.
     *
     * @throws ClientException If the HTTP status code is not 200.
     * @throws Ar24ApiException If the API returns an error status.
     */
    private function request(string $method, string $endpoint, array $options = [], array $errorMap = []): array
    {
        $date = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'))->format('Y-m-d H:i:s');

        // Build headers and request options.
        $headers = $this->headersFactory->buildHeaders($date);
        $requestOptions = [
            ...$options,
            'headers' => array_merge($headers, $options['headers'] ?? []),
            'query' => array_merge([
                'token' => $this->token, // AR24 API token.
                'date' => $date, // Date needed for decrypting API response.
            ], $options['query'] ?? []),
            'timeout' => $options['timeout'] ?? 60, // AR24 API timeout is 60 seconds.
        ];

        // Log and send the request.
        $loggedOptions = $requestOptions;
        if (isset($loggedOptions['query']['token'])) {
            $loggedOptions['query']['token'] = '***'; // Mask sensitive info for logging.
        }
        $this->logger->info('AR24 API Request', [
            'method' => $method,
            'url' => $this->baseUrl . $endpoint,
            'options' => $loggedOptions,
        ]);
        $response = $this->client->request($method, $this->baseUrl . $endpoint, $requestOptions);

        // Log the response content.
        $statusCode = $response->getStatusCode();
        $rawContent = $response->getContent(false);
        $this->logger->info('AR24 API Response (Raw)', [
            'statusCode' => $statusCode,
            'content' => $rawContent,
        ]);

        // API's success HTTP response code is always 200.
        if (200 === $statusCode) {
            // Successful response content is always encrypted, trying to decode JSON to check if it's encrypted or not.
            $content = json_decode($rawContent, true);
            if (JSON_ERROR_NONE !== json_last_error() || !is_array($content)) {
                // If JSON decoding fails or the result isn't an array, try decrypting the response.
                $decrypted = $this->responseDecrypter->decryptResponse($rawContent, $date);
                $content = json_decode($decrypted, true);
            }

            // After the decryption attempt, ensure we finally have a decoded array.
            if (JSON_ERROR_NONE !== json_last_error() || !is_array($content)) {
                throw new Ar24ApiException('invalid_response', 'Invalid or non-JSON response from AR24 API');
            }

            $status = $content['status'] ?? null;
            $result = $content['result'] ?? '';

            // Handle response based on status, if status is SUCCESS, return the decrypted content.
            if (Ar24ResponseStatus::SUCCESS->value === $status) {
                // Log decrypted response for successful requests.
                $this->logger->debug('AR24 API Response (Decrypted)', [
                    'status' => $status,
                    'result' => $result,
                ]);

                return $content;
            // If status is ERROR, handle the error.
            } elseif (Ar24ResponseStatus::ERROR->value === $status) {
                $this->handleError($content, $errorMap);
            // If status is neither SUCCESS nor ERROR, throw an unknown error exception.
            } else {
                throw new Ar24ApiException('status_unknown', sprintf('The status parameter is not valid: %s', $status));
            }
        // In other cases, we throw an exception.
        } else {
            // Wrap non-200 responses into a domain exception containing status and body for easier handling.
            throw new Ar24ApiException('http_' . $statusCode, sprintf('Unexpected HTTP status: %d. Body: %s', $statusCode, $rawContent));
        }
    }

    /**
     * Handles API errors by throwing specific exceptions based on the error code.
     * Allows for a custom error map to be used for call-specific handling.
     *
     * @param array $content The decoded API response content.
     * @param array $errorMap A map of error codes to [ExceptionClass, Message].
     *
     * @throws Ar24ApiException
     */
    private function handleError(array $content, array $errorMap = []): void
    {
        $fullErrorMap = array_merge(self::DEFAULT_ERROR_MAP, $errorMap);
        $errorCode = $fullErrorMap[$content['slug'] ?? 'unknown_error'] ?? 'unknown_error';

        [$exceptionClass, $message] = $fullErrorMap[$errorCode];
        throw new $exceptionClass($errorCode, $message);
    }

    /**
     * Sends a GET request to the specified URL with the provided options.
     *
     * @param string $url The URL to send the GET request to.
     * @param array $options An array of options to customize the request.
     * @param array $errorMap A map of error codes to [ExceptionClass, Message].
     *
     * @return array The response from the request.
     *
     * @throws Ar24ApiException
     */
    public function get(string $url, array $options = [], array $errorMap = []): array
    {
        return $this->request('GET', $url, $options, $errorMap);
    }

    /**
     * Sends a POST request to the specified URL with the given options.
     *
     * @param string $url The URL to send the POST request to.
     * @param array $options Optional parameters to customize the request.
     * @param array $errorMap A map of error codes to [ExceptionClass, Message].
     *
     * @return array The response resulting from the POST request.
     *
     * @throws Ar24ApiException
     */
    public function post(string $url, array $options = [], array $errorMap = []): array
    {
        return $this->request('POST', $url, $options, $errorMap);
    }
}
