<?php

namespace App\Tests\Infrastructure\Http\RegisteredMail;

use App\Infrastructure\Http\Client\Ar24ApiClient;
use App\Infrastructure\Http\RegisteredMail\Ar24RegisteredMailClient;
use App\Infrastructure\Http\RegisteredMail\DataTransformer\Ar24RegisteredMailDataTransformer;
use App\Infrastructure\Http\RegisteredMail\Exception\Ar24AuthenticationException;
use App\Infrastructure\Http\RegisteredMail\Model\Ar24RegisteredMail;
use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class Ar24RegisteredMailClientTest extends TestCase
{
    private Ar24ApiClient|MockObject $api;
    private Ar24RegisteredMailDataTransformer $transformer;
    private Ar24RegisteredMailClient $client;

    protected function setUp(): void
    {
        $this->api = $this->createMock(Ar24ApiClient::class);
        $this->transformer = new Ar24RegisteredMailDataTransformer(new AutomaticTransformer());
        $this->client = new Ar24RegisteredMailClient($this->api, $this->transformer);
    }

    public function testSend(): void
    {
        $mail = new Ar24RegisteredMail(
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
        $mail = new Ar24RegisteredMail(toEmail: 'test@example.com');

        $this->api->expects($this->once())
            ->method('post')
            ->willThrowException(new Ar24AuthenticationException('authentication_missing', 'Invalid eidas identification'));

        $this->expectException(Ar24AuthenticationException::class);
        $this->client->send(123, $mail);
    }

    public function testGetByIdMapsErrorToException(): void
    {
        $this->api->expects($this->once())
            ->method('get')
            ->willThrowException(new Ar24AuthenticationException('missing_erm_id', 'Please provide a valid mail ID'));

        $this->expectException(Ar24AuthenticationException::class);
        $this->client->getById(99);
    }
}
