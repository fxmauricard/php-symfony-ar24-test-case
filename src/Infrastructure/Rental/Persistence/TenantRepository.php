<?php

namespace App\Infrastructure\Rental\Persistence;

use App\Domain\Rental\Entity\Tenant;
use App\Domain\Rental\Repository\TenantRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tenant>
 */
class TenantRepository extends ServiceEntityRepository implements TenantRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Tenant::class);
        $this->em = $entityManager;
    }

    public function save(Tenant $tenant): void
    {
        $this->em->persist($tenant);
        $this->em->flush();
    }

    public function findById(int $id): ?Tenant
    {
        return $this->find($id);
    }
}
