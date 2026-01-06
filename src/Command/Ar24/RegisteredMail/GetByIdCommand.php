<?php

namespace App\Command\Ar24\RegisteredMail;

use App\Infrastructure\Ar24\Http\RegisteredMail\RegisteredMailClient;
use App\Infrastructure\Ar24\Http\RegisteredMail\DataTransformer\RegisteredMailDataTransformer;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:registered-mail:get-by-id',
    description: 'Get an AR24 registered mail by ID',
)]
/**
 * Command to retrieve AR24 registered mail by his ID.
 */
readonly class GetByIdCommand
{
    public function __construct(
        private RegisteredMailClient          $client,
        private RegisteredMailDataTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface                                                                  $input,
        OutputInterface                                                                 $output,
        #[Argument(description: 'The ID of the registered mail to retrieve')] int       $id
    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('Retrieving AR24 registered mail for ID: %s', $id));

        try {
            $registeredMail = $this->client->getById($id);
            $registeredMailData = $this->transformer->transform($registeredMail);

            $io->success('Registered mail found!');
            $io->table(
                ['Field', 'Value'],
                array_map(fn($k, $v) => [$k, is_array($v) ? json_encode($v) : $v], array_keys($registeredMailData), $registeredMailData)
            );
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
