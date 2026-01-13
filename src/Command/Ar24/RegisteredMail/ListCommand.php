<?php

namespace App\Command\Ar24\RegisteredMail;

use App\Infrastructure\Ar24\Http\RegisteredMail\DataTransformer\RegisteredMailDataTransformer;
use App\Infrastructure\Ar24\Http\RegisteredMail\RegisteredMailClient;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:registered-mail:list',
    description: 'List all AR24 registered mails for a user',
)]
/**
 * Command to list all AR24 registered mails.
 */
readonly class ListCommand
{
    public function __construct(
        private RegisteredMailClient $client,
        private RegisteredMailDataTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'The ID of the user for which we want to retrieve registered mails')] int $userId,
    ): int {
        $io = new SymfonyStyle($input, $output);

        $io->title('Listing AR24 registered mails');

        try {
            $registeredMails = $this->client->list($userId);

            if (empty($registeredMails)) {
                $io->warning('No registered mails found.');

                return Command::SUCCESS;
            }

            $io->success(sprintf('Found %d registered mail(s).', count($registeredMails)));

            $registeredMailsData = array_map(fn ($user) => $this->transformer->transform($user), $registeredMails);

            $headers = array_keys($registeredMailsData[0]);
            $rows = array_map(function ($registeredMail) {
                return array_map(fn ($v) => is_array($v) ? json_encode($v) : $v, array_values($registeredMail));
            }, $registeredMailsData);

            $io->table($headers, $rows);
        } catch (\Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
