<?php

namespace App\Tests\Infrastructure\Http\User;

use App\Infrastructure\Http\Client\Ar24ApiClient;
use App\Infrastructure\Http\Client\Enum\Ar24Sort;
use App\Infrastructure\Http\User\Ar24UserClient;
use App\Infrastructure\Http\User\DataTransformer\Ar24UserDataTransformer;
use App\Infrastructure\Http\User\Enum\Ar24UserStatut;
use App\Infrastructure\Http\User\Model\Ar24User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Ar24UserClient.
 */
final class Ar24UserClientTest extends TestCase
{
    private Ar24ApiClient|MockObject $api;
    private Ar24UserDataTransformer $transformer;
    private Ar24UserClient $client;

    protected function setUp(): void
    {
        $this->api = $this->createMock(Ar24ApiClient::class);
        $this->transformer = new Ar24UserDataTransformer();
        $this->client = new Ar24UserClient($this->api, $this->transformer);
    }

    public function testGetById(): void
    {
        $apiResult = ['result' => ['firstname' => 'John']];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user', ['query' => ['id_user' => 42]], $this->anything())
            ->willReturn($apiResult);

        $result = $this->client->getById(42);
        $this->assertSame('John', $result->getFirstname());
    }

    public function testGetByEmail(): void
    {
        $apiResult = ['result' => ['email' => 'john@example.com']];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user', ['query' => ['email' => 'john@example.com']], $this->anything())
            ->willReturn($apiResult);

        $result = $this->client->getByEmail('john@example.com');
        $this->assertSame('john@example.com', $result->getEmail());
    }

    public function testList(): void
    {
        $apiResult = ['result' => ['users' => [
            ['firstname' => 'A', 'statut' => 'particulier'],
            ['firstname' => 'B', 'statut' => 'professionnel'],
        ]]];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user/list', ['query' => ['max' => 5, 'start' => 10, 'sort' => Ar24Sort::DESC->value]])
            ->willReturn($apiResult);

        $result = $this->client->list(5, 10, Ar24Sort::DESC);
        $this->assertCount(2, $result);
        $this->assertSame('A', $result[0]->getFirstname());
        $this->assertSame('B', $result[1]->getFirstname());
    }

    public function testCreate(): void
    {
        $user = new Ar24User()
            ->setFirstname('John')
            ->setStatut(Ar24UserStatut::INDIVIDUAL);

        $payload = $this->transformer->transform($user);
        $apiResult = ['result' => $payload];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/user', ['body' => $payload], $this->anything())
            ->willReturn($apiResult);

        $created = $this->client->create($user);
        $this->assertSame('John', $created->getFirstname());
        $this->assertSame(Ar24UserStatut::INDIVIDUAL, $created->getStatut());
    }
}
