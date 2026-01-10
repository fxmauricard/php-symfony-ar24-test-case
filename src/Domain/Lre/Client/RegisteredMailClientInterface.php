<?php

namespace App\Domain\Lre\Client;

use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RegisteredMail;

interface RegisteredMailClientInterface
{
    /**
     * @throws ApiException
     */
    public function send(int $userId, RegisteredMail $registeredMail): RegisteredMail;

    /**
     * @throws ApiException
     */
    public function getById(int $id): RegisteredMail;

    /**
     * @return RegisteredMail[]
     * @throws ApiException
     */
    public function list(int $userId): array;
}
