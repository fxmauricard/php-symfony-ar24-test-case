<?php

namespace App\Domain\Rental\Service;

use App\Domain\Lre\Client\RegisteredMailClientInterface;
use App\Domain\Lre\Client\UserClientInterface;
use App\Domain\Rental\Entity\Lease;
use App\Domain\Rental\Entity\RentRevaluationNotification;
use App\Domain\Rental\Repository\LeaseRepositoryInterface;
use App\Domain\Rental\Repository\RentRevaluationNotificationRepositoryInterface;
use App\Infrastructure\Ar24\Http\Attachment\AttachmentClient;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RegisteredMail;
use App\Infrastructure\Ar24\Http\User\Enum\UserStatut;
use DateTime;
use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

final class LeaseService
{
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @var LeaseRepositoryInterface
     */
    private LeaseRepositoryInterface $leaseRepository;

    /**
     * @var UserClientInterface
     */
    private UserClientInterface $userClient;

    /**
     * @var RegisteredMailClientInterface
     */
    private RegisteredMailClientInterface $registeredMailClient;
    private AttachmentClient $attachmentClient;

    /**
     * @param KernelInterface $kernel
     * @param LeaseRepositoryInterface $leaseRepository
     * @param UserClientInterface $userClient
     * @param RegisteredMailClientInterface $registeredMailClient
     * @param AttachmentClient $attachmentClient
     */
    public function __construct(
        KernelInterface                                $kernel,
        LeaseRepositoryInterface                       $leaseRepository,
        UserClientInterface                            $userClient,
        RegisteredMailClientInterface                  $registeredMailClient, AttachmentClient $attachmentClient
    ) {
        $this->kernel = $kernel;
        $this->leaseRepository = $leaseRepository;
        $this->userClient = $userClient;
        $this->registeredMailClient = $registeredMailClient;
        $this->attachmentClient = $attachmentClient;
    }

    public function reevaluate(Lease $lease, float $indice): Lease
    {
        $date = new DateTime();
        $currentRent = $lease->getCurrentRent();
        $newRent = $currentRent * (1 + $indice);

        $rentRevaluationNotification = new RentRevaluationNotification()
            ->setOldRent($currentRent)
            ->setNewRent($newRent)
            ->setIndexUsed($indice);

        $lease->setCurrentRent($newRent);
        $lease->setLastRevaluationDate($date);
        $lease->addRentRevaluationNotification($rentRevaluationNotification);
        $this->leaseRepository->save($lease);

        $registeredMail = new RegisteredMail();
        $registeredMail->destStatut = UserStatut::INDIVIDUAL->value;
        $registeredMail->toEmail = $lease->getTenant()->getEmail();
        $registeredMail->toFirstname = $lease->getTenant()->getFirstName();
        $registeredMail->toLastname = $lease->getTenant()->getLastName();
        $registeredMail->toName = $lease->getTenant()->getFirstName() . ' ' . $lease->getTenant()->getLastName();
        $registeredMail->address1 = $lease->getTenant()->getAddress1();
        $registeredMail->address2 = $lease->getTenant()->getAddress2();
        $registeredMail->city = $lease->getTenant()->getCity();
        $registeredMail->zipcode = $lease->getTenant()->getZipCode();

        // Retrieving the user ID to use for sending the registered mail.
        $userList = $this->userClient->list(1);
        $firstUserId = $userList[0]->id ?? null;
        if ($firstUserId === null) {
            throw new RuntimeException('No AR24 user available to send mail');
        }

        //TODO: Kernel dependency due to example file, should be removed in a real implementation that could generate a real PDF.
        $filePath = $this->kernel->getProjectDir() . '/data/examples/attachment/upload.pdf';

        // Uploading the attachment and sending the registered mail.
        $attachmentId = $this->attachmentClient->upload($firstUserId, $filePath);
        $registeredMail->attachments[] = $attachmentId;
        $this->registeredMailClient->send($firstUserId, $registeredMail);

        return $lease;
    }
}
