<?php

namespace App\Infrastructure\Http\Client;

use App\Infrastructure\Http\Client\Enum\Ar24ResponseStatus;
use App\Infrastructure\Security\Ar24HeadersFactory;
use App\Infrastructure\Security\Ar24ResponseDecrypter;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Ar24ApiClient
{
    private string $baseUrl;
    private string $token;

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

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
     * @param string $url The endpoint URL for the API request.
     * @param array $options Optional parameters to customize the request, including headers, query, and timeout settings.
     *
     * @return array The decoded response content from the API. If the response is encrypted, it will be decrypted before returning.
     *
     * @throws ClientException If the HTTP status code is not 200.
     */
    private function request(string $method, string $url, array $options = []): array
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
        $this->logger->info('AR24 API Request', [
            'method' => $method,
            'url' => $url,
            'options' => $requestOptions,
        ]);
        $response = $this->client->request($method, $url, $requestOptions);

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
            if (null === $content) {
                // If JSON decoding fails, we assume the response is encrypted and proceed to decrypt it.
                $decrypted = $this->responseDecrypter->decryptResponse($rawContent, $date);
                $content = json_decode($decrypted, true);
            }

            $status = $content['status'] ?? null;
            $result = $content['result'] ?? '';

            if (Ar24ResponseStatus::SUCCESS === $status) {
                // Log decrypted response for successful requests.
                $this->logger->info('AR24 API Response (Decrypted)', [
                    'status' => $status,
                    'result' => $result,
                ]);
            }

            return $content;
        // In other cases, we throw an exception.
        } else {
            throw new ClientException($response);
        }
    }

    /**
     * Sends a GET request to the specified URL with the provided options.
     *
     * @param string $url The URL to send the GET request to.
     * @param array $options An array of options to customize the request.
     *
     * @return array The response from the request.
     */
    public function get(string $url, array $options = []): array
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * Sends a POST request to the specified URL with the given options.
     *
     * @param string $url The URL to send the POST request to.
     * @param array $options Optional parameters to customize the request.
     *
     * @return array The response resulting from the POST request.
     */
    public function post(string $url, array $options = []): array
    {
        return $this->request('POST', $url, $options);
    }
}
