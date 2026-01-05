<?php

namespace App\Command\Ar24\User;

use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Http\User\Ar24UserClient;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:user:list',
    description: 'List all AR24 users',
)]
/**
 * Command to list all AR24 users.
 */
readonly class ListCommand
{
    public function __construct(
        private Ar24UserClient          $client,
        private AutomaticTransformer    $transformer,
    ) {
    }

    public function __invoke(
        InputInterface  $input,
        OutputInterface $output
    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Listing AR24 users');

        try {
            $users = $this->client->list();

            if (empty($users)) {
                $io->warning('No users found.');
                return Command::SUCCESS;
            }

            $io->success(sprintf('Found %d user(s).', count($users)));

            $usersData = array_map(fn($user) => $this->transformer->transform($user), $users);

            $headers = array_keys($usersData[0]);
            $rows = array_map(function($user) {
                return array_map(fn($v) => is_array($v) ? json_encode($v) : $v, array_values($user));
            }, $usersData);

            $io->table($headers, $rows);
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
