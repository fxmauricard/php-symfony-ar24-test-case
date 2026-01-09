<?php

namespace App\Domain\Rental\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Infrastructure\Rental\Persistence\RentRevaluationNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentRevaluationNotificationRepository::class)]
#[ApiResource]
class RentRevaluationNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rentRevaluationNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lease $lease = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $oldRent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $newRent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $indexUsed = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $depositProofUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receiptProofUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLease(): ?Lease
    {
        return $this->lease;
    }

    public function setLease(?Lease $lease): static
    {
        $this->lease = $lease;

        return $this;
    }

    public function getOldRent(): ?string
    {
        return $this->oldRent;
    }

    public function setOldRent(string $oldRent): static
    {
        $this->oldRent = $oldRent;

        return $this;
    }

    public function getNewRent(): ?string
    {
        return $this->newRent;
    }

    public function setNewRent(string $newRent): static
    {
        $this->newRent = $newRent;

        return $this;
    }

    public function getIndexUsed(): ?string
    {
        return $this->indexUsed;
    }

    public function setIndexUsed(string $indexUsed): static
    {
        $this->indexUsed = $indexUsed;

        return $this;
    }

    public function getDepositProofUrl(): ?string
    {
        return $this->depositProofUrl;
    }

    public function setDepositProofUrl(?string $depositProofUrl): static
    {
        $this->depositProofUrl = $depositProofUrl;

        return $this;
    }

    public function getReceiptProofUrl(): ?string
    {
        return $this->receiptProofUrl;
    }

    public function setReceiptProofUrl(?string $receiptProofUrl): static
    {
        $this->receiptProofUrl = $receiptProofUrl;

        return $this;
    }
}
