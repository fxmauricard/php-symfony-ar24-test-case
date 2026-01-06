<?php

namespace App\Tests\Infrastructure\Ar24\Http\Client;

use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\Client\Exception\DateException;
use App\Infrastructure\Ar24\Http\Client\Exception\TokenException;
use App\Infrastructure\Ar24\Security\HeadersFactory;
use App\Infrastructure\Ar24\Security\ResponseDecrypter;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversNothing]
class ApiClientTest extends TestCase
{
    public function test_success_plain_json_and_masks_token_in_logs(): void
    {
        $baseUrl = 'https://api.example.com';
        $token = 'secret-token';

        $httpClient = $this->createMock(HttpClientInterface::class);
        $headersFactory = new HeadersFactory('private-key');
        $decrypter = new ResponseDecrypter('private-key');
        $logger = $this->createMock(LoggerInterface::class);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContent')->willReturn(json_encode(['status' => 'SUCCESS', 'result' => ['ok' => true]]));

        $logger->expects($this->atLeastOnce())
            ->method('info')
            ->willReturnCallback(function (string $message, array $context = []) {
                if ('AR24 API Request' === $message) {
                    self::assertEquals('***', $context['options']['query']['token'] ?? null);
                }
            });

        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $baseUrl . '/foo', $this->callback(function ($options) use ($token) {
                return ($options['query']['token'] ?? null) === $token;
            }))
            ->willReturn($response);

        $client = new ApiClient($httpClient, $headersFactory, $decrypter, $logger, $baseUrl, $token);
        $result = $client->get('/foo');

        $this->assertEquals('SUCCESS', $result['status']);
        $this->assertTrue($result['result']['ok']);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function test_decrypts_encrypted_payload(): void
    {
        $baseUrl = 'https://api.example.com';
        $token = 'tkn';
        $privateKey = 'private-key';

        $headersFactory = new HeadersFactory($privateKey);
        $decrypter = new ResponseDecrypter($privateKey);
        $logger = $this->createStub(LoggerInterface::class);

        $encryptedContent = null;
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('request')->willReturnCallback(function ($method, $url, $options) use (&$encryptedContent, $privateKey) {
            $date = $options['query']['date'];
            $payload = json_encode(['status' => 'SUCCESS', 'result' => ['a' => 1]]);
            $key = hash('sha256', $date . $privateKey);
            $doubleHash = hash('sha256', hash('sha256', $privateKey));
            $iv = new UnicodeString($doubleHash)->slice(0, 16)->toString();
            $encryptedContent = openssl_encrypt($payload, 'aes-256-cbc', $key, 0, $iv);
            return new StubResponse(200, $encryptedContent);
        });

        $client = new ApiClient($httpClient, $headersFactory, $decrypter, $logger, $baseUrl, $token);
        $result = $client->get('/foo');

        $this->assertEquals('SUCCESS', $result['status']);
        $this->assertSame(1, $result['result']['a']);
        $this->assertNotEmpty($encryptedContent);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function test_throws_on_non_200_status(): void
    {
        $baseUrl = 'https://api.example.com';
        $token = 'tkn';

        $httpClient = $this->createStub(HttpClientInterface::class);
        $headersFactory = new HeadersFactory('private-key');
        $decrypter = new ResponseDecrypter('private-key');
        $logger = $this->createStub(LoggerInterface::class);

        $response = new StubResponse(500, 'Server error');
        $httpClient->method('request')->willReturn($response);

        $client = new ApiClient($httpClient, $headersFactory, $decrypter, $logger, $baseUrl, $token);

        $this->expectException(ApiException::class);
        $client->get('/foo');
    }

    public function test_handle_error_token_missing_maps_to_token_exception(): void
    {
        $client = $this->createClientWithErrorSlug('token_missing');
        $this->expectException(TokenException::class);
        $client->get('/foo');
    }

    public function test_handle_error_unknown_code_maps_to_generic_exception(): void
    {
        $client = $this->createClientWithErrorSlug('some_unknown_code');
        $this->expectException(ApiException::class);
        $client->get('/foo');
    }

    public function test_handle_error_invalid_date_maps_to_date_exception(): void
    {
        $client = $this->createClientWithErrorSlug('invalid_date');
        $this->expectException(DateException::class);
        $client->get('/foo');
    }

    #[AllowMockObjectsWithoutExpectations]
    private function createClientWithErrorSlug(string $slug): ApiClient
    {
        $baseUrl = 'https://api.example.com';
        $token = 'tkn';

        $httpClient = $this->createStub(HttpClientInterface::class);
        $headersFactory = new HeadersFactory('private-key');
        $decrypter = new ResponseDecrypter('private-key');
        $logger = $this->createStub(LoggerInterface::class);

        $response = new StubResponse(200, json_encode(['status' => 'ERROR', 'slug' => $slug]));
        $httpClient->method('request')->willReturn($response);

        return new ApiClient($httpClient, $headersFactory, $decrypter, $logger, $baseUrl, $token);
    }
}

/**
 * Minimal ResponseInterface stub for tests.
 */
final class StubResponse implements ResponseInterface
{
    public function __construct(private int $statusCode, private string $content)
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return [];
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        return json_decode($this->content, true) ?? [];
    }

    public function cancel(): void
    {
    }

    public function getInfo(?string $type = null): mixed
    {
        if (null === $type) {
            return [];
        }
        return null;
    }
}
