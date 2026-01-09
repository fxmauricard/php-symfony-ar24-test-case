<?php

namespace App\Domain\Rental\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Infrastructure\Rental\Persistence\LeaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaseRepository::class)]
#[ApiResource]
class Lease
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tenant $tenant = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $initialRent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $currentRent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $referenceIndex = null;

    #[ORM\Column]
    private ?\DateTime $lastRevaluationDate = null;

    /**
     * @var Collection<int, RentRevaluationNotification>
     */
    #[ORM\OneToMany(targetEntity: RentRevaluationNotification::class, mappedBy: 'lease')]
    private Collection $rentRevaluationNotifications;

    public function __construct()
    {
        $this->rentRevaluationNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getInitialRent(): ?string
    {
        return $this->initialRent;
    }

    public function setInitialRent(string $initialRent): static
    {
        $this->initialRent = $initialRent;

        return $this;
    }

    public function getCurrentRent(): ?string
    {
        return $this->currentRent;
    }

    public function setCurrentRent(string $currentRent): static
    {
        $this->currentRent = $currentRent;

        return $this;
    }

    public function getReferenceIndex(): ?string
    {
        return $this->referenceIndex;
    }

    public function setReferenceIndex(string $referenceIndex): static
    {
        $this->referenceIndex = $referenceIndex;

        return $this;
    }

    public function getLastRevaluationDate(): ?\DateTime
    {
        return $this->lastRevaluationDate;
    }

    public function setLastRevaluationDate(\DateTime $lastRevaluationDate): static
    {
        $this->lastRevaluationDate = $lastRevaluationDate;

        return $this;
    }

    /**
     * @return Collection<int, RentRevaluationNotification>
     */
    public function getRentRevaluationNotifications(): Collection
    {
        return $this->rentRevaluationNotifications;
    }

    public function addRentRevaluationNotification(RentRevaluationNotification $rentRevaluationNotification): static
    {
        if (!$this->rentRevaluationNotifications->contains($rentRevaluationNotification)) {
            $this->rentRevaluationNotifications->add($rentRevaluationNotification);
            $rentRevaluationNotification->setLease($this);
        }

        return $this;
    }

    public function removeRentRevaluationNotification(RentRevaluationNotification $rentRevaluationNotification): static
    {
        if ($this->rentRevaluationNotifications->removeElement($rentRevaluationNotification)) {
            // set the owning side to null (unless already changed)
            if ($rentRevaluationNotification->getLease() === $this) {
                $rentRevaluationNotification->setLease(null);
            }
        }

        return $this;
    }
}
