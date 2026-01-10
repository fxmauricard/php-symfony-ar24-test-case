<?php

namespace App\Domain\Rental\Repository;

use App\Domain\Rental\Entity\Tenant;

interface TenantRepositoryInterface
{
    public function save(Tenant $tenant): void;

    public function findById(int $id): ?Tenant;
}
