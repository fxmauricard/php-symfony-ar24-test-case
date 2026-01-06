<?php

namespace App\Infrastructure\Ar24\Http\User;

use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Client\Enum\Sort;
use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\User\Exception\UserException;
use App\Infrastructure\Ar24\Http\User\Model\User;

/**
 * Client for interacting with AR24 User API.
 */
final readonly class UserClient
{
    /**
     * Constructor.
     */
    public function __construct(
        private ApiClient            $client,
        private AutomaticTransformer $transformer,
    )
    {
    }

    /**
     * Get user info (providing the User ID).
     *
     * @param int $id User ID
     *
     * @return User
     *
     * @throws ApiException
     */
    public function getById(int $id): User
    {
        $data = $this->client->get( '/user', [
            'query' => [
                'id_user' => $id,
            ],
        ], [
            'user_not_exist' => [UserException::class, 'There is not a user with this address on AR24'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? [], User::class);
    }

    /**
     *  Get user info (providing the User's email).
     *
     * @param string $email User's email
     *
     * @return User
     *
     * @throws ApiException
     */
    public function getByEmail(string $email): User
    {
        $data = $this->client->get( '/user', [
            'query' => [
                'email' => $email,
            ],
        ], [
            'user_not_exist' => [UserException::class, 'There is not a user with this address on AR24'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? [], User::class);
    }

    /**
     * List all users.
     *
     * @param int $max Number of results returned
     * @param int $start Return result from the defined start index
     * @param Sort $sort Sort by ID
     *
     * @return User[]
     *
     * @throws ApiException
     */
    public function list(int $max = 10, int $start = 0, Sort $sort = Sort::ASC): array
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
            fn(array $userData) => $this->transformer->reverseTransform($userData, User::class),
            $usersData
        );
    }

    /**
     * Create user.
     *
     * @param User $user
     *
     * @return User
     *
     * @throws ApiException
     */
    public function create(User $user): User
    {
        $data = $this->client->post('/user', [
            'body' => $this->transformer->transform($user)
        ], [
            'missing_firstname' => [UserException::class, 'Please specify a firstname'],
            'missing_lastname' => [UserException::class, 'Please specify a lastname'],
            'missing_email' => [UserException::class, 'Please specify an email address'],
            'email_wrong_format' => [UserException::class, 'Incorrect email address format'],
            'missing_address' => [UserException::class, 'Please specify an address'],
            'missing_city' => [UserException::class, 'Please specify a city'],
            'missing_zipcode' => [UserException::class, 'Please specify a zipcode'],
            'missing_country' => [UserException::class, 'Please specify a country'],
            'error_country' => [UserException::class, 'Please specify a valid country'],
            'error_gender' => [UserException::class, 'Please specify a valid gender'],
            'missing_company_siret' => [UserException::class, 'Please specify an company_siret (Required for a user in FR)'],
            'missing_company_tva' => [UserException::class, ' specify an company_tva (Required for a user in EU)'],
            'error_company_siret' => [UserException::class, 'Please specify a valid company_siret (No company has been found with this company_siret)'],
            'user_not_created' => [UserException::class, 'An error occurred'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? [], User::class);
    }
}
