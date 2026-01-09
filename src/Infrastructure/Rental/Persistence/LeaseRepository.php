<?php

namespace App\Infrastructure\Rental\Persistence;

use App\Domain\Rental\Entity\Lease;
use App\Domain\Rental\Repository\LeaseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lease>
 */
class LeaseRepository extends ServiceEntityRepository implements LeaseRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Lease::class);
        $this->em = $entityManager;
    }

    public function save(Lease $lease): void
    {
        $this->em->persist($lease);
        $this->em->flush();
    }

    public function findById(int $id): ?Lease
    {
        return $this->find($id);
    }
}
