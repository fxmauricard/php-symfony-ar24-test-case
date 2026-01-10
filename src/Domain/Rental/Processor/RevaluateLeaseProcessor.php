<?php

namespace App\Domain\Rental\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\Rental\Entity\Lease;
use App\Domain\Rental\Input\RevaluateLeaseInput;
use App\Domain\Rental\Repository\LeaseRepositoryInterface;
use App\Domain\Rental\Service\LeaseService;
use Psr\Log\LoggerInterface;

class RevaluateLeaseProcessor implements ProcessorInterface
{
    private LeaseRepositoryInterface $leaseRepository;

    private LeaseService $leaseService;

    private LoggerInterface $logger;

    public function __construct(LeaseRepositoryInterface $leaseRepository, LeaseService $leaseService, LoggerInterface $logger)
    {
        $this->leaseRepository = $leaseRepository;
        $this->leaseService = $leaseService;
        $this->logger = $logger;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Lease
    {
        $this->logger->debug(sprintf('RevaluateLeaseProcessor called with id: %s, indice: %s.', $uriVariables['id'], $data->indice));

        /**
         * @var Lease $lease
         */
        $lease = $this->leaseRepository->findById($uriVariables['id']);

        /**
         * @var RevaluateLeaseInput $data
         */
        $indice = $data->indice;

        // Call the domain service.
        return $this->leaseService->reevaluate($lease, $indice);
    }
}
