<?php

namespace App\Infrastructure\Rental\Persistence;

use App\Domain\Rental\Entity\RentRevaluationNotification;
use App\Domain\Rental\Repository\RentRevaluationNotificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RentRevaluationNotification>
 */
class RentRevaluationNotificationRepository extends ServiceEntityRepository implements RentRevaluationNotificationRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, RentRevaluationNotification::class);
        $this->em = $entityManager;
    }

    public function save(RentRevaluationNotification $rentRevaluationNotification): void
    {
        $this->em->persist($rentRevaluationNotification);
        $this->em->flush();
    }

    public function findById(int $id): ?RentRevaluationNotification
    {
        return $this->find($id);
    }
}
