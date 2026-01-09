<?php

namespace App\Domain\Rental\Repository;

use App\Domain\Rental\Entity\Lease;

interface LeaseRepositoryInterface
{
    public function save(Lease $lease): void;

    public function findById(int $id): ?Lease;
}
