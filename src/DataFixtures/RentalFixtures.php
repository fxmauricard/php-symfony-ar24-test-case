<?php

namespace App\DataFixtures;

use App\Domain\Rental\Entity\Lease;
use App\Domain\Rental\Entity\RentRevaluationNotification;
use App\Domain\Rental\Entity\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RentalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tenants = [
            $this->createTenant('Doe', 'John', 'john.doe@example.com', '1 Main St', 'Apt 1', '75001', 'Paris'),
            $this->createTenant('Smith', 'Jane', 'jane.smith@example.com', '2 High St', null, '69002', 'Lyon'),
            $this->createTenant('Brown', 'Charlie', 'charlie.brown@example.com', '3 Low St', 'Building B', '31000', 'Toulouse'),
        ];

        $leases = [
            $this->createLease($tenants[0], '2022-01-01', 900, 900, 1.05, '2023-01-01', [
                ['old' => 900, 'new' => 945, 'index' => 0.05, 'deposit' => 'https://example.com/proof/1', 'receipt' => 'https://example.com/receipt/1'],
            ]),
            $this->createLease($tenants[1], '2021-06-15', 1200, 1260, 1.08, '2023-06-15', [
                ['old' => 1200, 'new' => 1236, 'index' => 0.03, 'deposit' => null, 'receipt' => 'https://example.com/receipt/2'],
                ['old' => 1236, 'new' => 1260, 'index' => 0.02, 'deposit' => 'https://example.com/proof/2', 'receipt' => null],
            ]),
            $this->createLease($tenants[2], '2020-09-01', 750, 780, 1.04, '2023-09-01', [
                ['old' => 750, 'new' => 765, 'index' => 0.02, 'deposit' => null, 'receipt' => null],
            ]),
        ];

        foreach ($tenants as $tenant) {
            $manager->persist($tenant);
        }

        foreach ($leases as $lease) {
            foreach ($lease->getRentRevaluationNotifications() as $notification) {
                $manager->persist($notification);
            }
            $manager->persist($lease);
        }

        $manager->flush();
    }

    private function createTenant(string $last, string $first, string $email, string $address1, ?string $address2, string $zip, string $city): Tenant
    {
        return new Tenant()
            ->setLastName($last)
            ->setFirstName($first)
            ->setEmail($email)
            ->setAddress1($address1)
            ->setAddress2($address2)
            ->setZipCode($zip)
            ->setCity($city);
    }

    private function createLease(Tenant $tenant, string $start, float $initial, float $current, float $index, string $lastRevaluation, array $notifications): Lease
    {
        $lease = new Lease()
            ->setTenant($tenant)
            ->setStartDate(new \DateTime($start))
            ->setInitialRent(number_format($initial, 2, '.', ''))
            ->setCurrentRent(number_format($current, 2, '.', ''))
            ->setReferenceIndex(number_format($index, 2, '.', ''))
            ->setLastRevaluationDate(new \DateTime($lastRevaluation));

        foreach ($notifications as $data) {
            $notification = new RentRevaluationNotification()
                ->setOldRent(number_format($data['old'], 2, '.', ''))
                ->setNewRent(number_format($data['new'], 2, '.', ''))
                ->setIndexUsed(number_format($data['index'], 2, '.', ''))
                ->setDepositProofUrl($data['deposit'])
                ->setReceiptProofUrl($data['receipt']);

            $lease->addRentRevaluationNotification($notification);
        }

        return $lease;
    }
}
