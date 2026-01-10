<?php

namespace App\Domain\Lre\Client;

use App\Infrastructure\Ar24\Http\Client\Enum\Sort;
use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\User\Model\User;

interface UserClientInterface
{
    /**
     * @throws ApiException
     */
    public function getById(int $id): User;

    /**
     * @throws ApiException
     */
    public function getByEmail(string $email): User;

    /**
     * @return User[]
     * @throws ApiException
     */
    public function list(int $max = 10, int $start = 0, Sort $sort = Sort::ASC): array;

    /**
     * @throws ApiException
     */
    public function create(User $user): User;
}
