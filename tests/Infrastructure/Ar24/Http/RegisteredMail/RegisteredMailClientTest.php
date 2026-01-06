<?php

namespace App\Tests\Infrastructure\Ar24\Http\RegisteredMail;

use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\RegisteredMail\RegisteredMailClient;
use App\Infrastructure\Ar24\Http\RegisteredMail\DataTransformer\RegisteredMailDataTransformer;
use App\Infrastructure\Ar24\Http\RegisteredMail\Exception\AuthenticationException;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RegisteredMail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegisteredMailClientTest extends TestCase
{
    private ApiClient|MockObject $api;
    private RegisteredMailClient $client;

    protected function setUp(): void
    {
        $this->api = $this->createMock(ApiClient::class);
        $transformer = new RegisteredMailDataTransformer(new AutomaticTransformer());
        $this->client = new RegisteredMailClient($this->api, $transformer);
    }

    public function testSend(): void
    {
        $mail = new RegisteredMail(
            fromEmail: 'sender@example.com',
            toEmail: 'test@example.com',
        );

        $apiResult = ['result' => ['id' => 10, 'status' => 'sent']];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/mail', $this->callback(fn($options) => isset($options['body']) && is_array($options['body'])))
            ->willReturn($apiResult);

        $result = $this->client->send(123, $mail);
        $this->assertSame(10, $result->id);
        $this->assertSame('sent', $result->status);
    }

    public function testGetById(): void
    {
        $apiResult = ['result' => ['id' => 11, 'status' => 'delivered']];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/mail', ['query' => ['id' => 11]])
            ->willReturn($apiResult);

        $mail = $this->client->getById(11);
        $this->assertSame(11, $mail->id);
        $this->assertSame('delivered', $mail->status);
    }

    public function testSendMapsErrorToException(): void
    {
        $mail = new RegisteredMail(toEmail: 'test@example.com');

        $this->api->expects($this->once())
            ->method('post')
            ->willThrowException(new AuthenticationException('authentication_missing', 'Invalid eidas identification'));

        $this->expectException(AuthenticationException::class);
        $this->client->send(123, $mail);
    }

    public function testGetByIdMapsErrorToException(): void
    {
        $this->api->expects($this->once())
            ->method('get')
            ->willThrowException(new AuthenticationException('missing_erm_id', 'Please provide a valid mail ID'));

        $this->expectException(AuthenticationException::class);
        $this->client->getById(99);
    }
}
