<?php

namespace App\Infrastructure\Http\User;

use App\Infrastructure\Http\Client\Ar24ApiClient;
use App\Infrastructure\Http\Client\Enum\Ar24Sort;
use App\Infrastructure\Http\Client\Exception\Ar24ApiException;
use App\Infrastructure\Http\User\DataTransformer\Ar24UserDataTransformer;
use App\Infrastructure\Http\User\Exception\Ar24UserException;
use App\Infrastructure\Http\User\Model\Ar24User;

/**
 * Client for interacting with AR24 User API.
 */
final readonly class Ar24UserClient
{
    /**
     * Constructor.
     */
    public function __construct(
        private Ar24ApiClient $client,
        private Ar24UserDataTransformer $transformer,
    )
    {
    }

    /**
     * Get user info (providing the User ID).
     *
     * @param int $id User ID
     *
     * @return Ar24User
     *
     * @throws Ar24ApiException
     */
    public function getById(int $id): Ar24User
    {
        $data = $this->client->get( '/user', [
            'query' => [
                'id_user' => $id,
            ],
        ], [
            'user_not_exist' => [Ar24UserException::class, 'There is not a user with this address on AR24'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     *  Get user info (providing the User's email).
     *
     * @param string $email User's email
     *
     * @return Ar24User
     *
     * @throws Ar24ApiException
     */
    public function getByEmail(string $email): Ar24User
    {
        $data = $this->client->get( '/user', [
            'query' => [
                'email' => $email,
            ],
        ], [
            'user_not_exist' => [Ar24UserException::class, 'There is not a user with this address on AR24'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     * List all users.
     *
     * @param int $max Number of results returned
     * @param int $start Return result from the defined start index
     * @param Ar24Sort $sort Sort by ID
     *
     * @return Ar24User[]
     *
     * @throws Ar24ApiException
     */
    public function list(int $max = 10, int $start = 0, Ar24Sort $sort = Ar24Sort::ASC): array
    {
        $data = $this->client->get( '/user/list', [
            'query' => [
                'max' => $max,
                'start' => $start,
                'sort' => $sort->value,
            ],
        ]);
        $usersData = $data['result']['users'] ?? [];

        return array_map(
            fn(array $userData) => $this->transformer->reverseTransform($userData),
            $usersData
        );
    }

    /**
     * Create user.
     *
     * @param Ar24User $user
     *
     * @return Ar24User
     *
     * @throws Ar24ApiException
     */
    public function create(Ar24User $user): Ar24User
    {
        $data = $this->client->post('/user', [
            'body' => $this->transformer->transform($user)
        ], [
            'missing_firstname' => [Ar24UserException::class, 'Please specify a firstname'],
            'missing_lastname' => [Ar24UserException::class, 'Please specify a lastname'],
            'missing_email' => [Ar24UserException::class, 'Please specify an email address'],
            'email_wrong_format' => [Ar24UserException::class, 'Incorrect email address format'],
            'missing_address' => [Ar24UserException::class, 'Please specify an address'],
            'missing_city' => [Ar24UserException::class, 'Please specify a city'],
            'missing_zipcode' => [Ar24UserException::class, 'Please specify a zipcode'],
            'missing_country' => [Ar24UserException::class, 'Please specify a country'],
            'error_country' => [Ar24UserException::class, 'Please specify a valid country'],
            'error_gender' => [Ar24UserException::class, 'Please specify a valid gender'],
            'missing_company_siret' => [Ar24UserException::class, 'Please specify an company_siret (Required for a user in FR)'],
            'missing_company_tva' => [Ar24UserException::class, ' specify an company_tva (Required for a user in EU)'],
            'error_company_siret' => [Ar24UserException::class, 'Please specify a valid company_siret (No company has been found with this company_siret)'],
            'user_not_created' => [Ar24UserException::class, 'An error occurred'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }
}
