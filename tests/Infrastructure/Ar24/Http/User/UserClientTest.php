<?php

namespace App\Tests\Infrastructure\Ar24\Http\User;

use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Client\Enum\Sort;
use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\User\Enum\UserStatut;
use App\Infrastructure\Ar24\Http\User\Model\User;
use App\Infrastructure\Ar24\Http\User\UserClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Ar24UserClient.
 */
final class UserClientTest extends TestCase
{
    private ApiClient|MockObject $api;
    private AutomaticTransformer $transformer;
    private UserClient $client;

    protected function setUp(): void
    {
        $this->api = $this->createMock(ApiClient::class);
        $this->transformer = new AutomaticTransformer();
        $this->client = new UserClient($this->api, $this->transformer);
    }

    public function testGetById(): void
    {
        $apiResult = ['result' => ['firstname' => 'John']];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user', ['query' => ['id_user' => 42]], $this->anything())
            ->willReturn($apiResult);

        $result = $this->client->getById(42);
        $this->assertSame('John', $result->firstname);
    }

    public function testGetByEmail(): void
    {
        $apiResult = ['result' => ['email' => 'john@example.com']];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user', ['query' => ['email' => 'john@example.com']], $this->anything())
            ->willReturn($apiResult);

        $result = $this->client->getByEmail('john@example.com');
        $this->assertSame('john@example.com', $result->email);
    }

    public function testList(): void
    {
        $apiResult = ['result' => ['users' => [
            ['firstname' => 'A', 'statut' => 'particulier'],
            ['firstname' => 'B', 'statut' => 'professionnel'],
        ]]];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/user/list', ['query' => ['max' => 5, 'start' => 10, 'sort' => Sort::DESC->value]])
            ->willReturn($apiResult);

        $result = $this->client->list(5, 10, Sort::DESC);
        $this->assertCount(2, $result);
        $this->assertSame('A', $result[0]->firstname);
        $this->assertSame('B', $result[1]->firstname);
    }

    public function testCreate(): void
    {
        $user = new User(
            firstname: 'John',
            statut: UserStatut::INDIVIDUAL,
        );

        $payload = $this->transformer->transform($user);
        $apiResult = ['result' => $payload];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/user', ['body' => $payload], $this->anything())
            ->willReturn($apiResult);

        $created = $this->client->create($user);
        $this->assertSame('John', $created->firstname);
        $this->assertSame(UserStatut::INDIVIDUAL, $created->statut);
    }
}
