<?php

namespace App\Domain\Rental\Repository;

use App\Domain\Rental\Entity\RentRevaluationNotification;

interface RentRevaluationNotificationRepositoryInterface
{
    public function save(RentRevaluationNotification $rentRevaluationNotification): void;

    public function findById(int $id): ?RentRevaluationNotification;
}
