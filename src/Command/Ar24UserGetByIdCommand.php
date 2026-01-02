<?php

namespace App\Command;

use App\Infrastructure\Http\User\Ar24UserClient;
use App\Infrastructure\Http\User\DataTransformer\Ar24UserDataTransformer;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:user:get-by-id',
    description: 'Get an AR24 user by ID',
)]
/**
 * Command to retrieve an AR24 user by his ID.
 */
readonly class Ar24UserGetByIdCommand
{
    public function __construct(
        private Ar24UserClient          $userClient,
        private Ar24UserDataTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface                                                    $input,
        OutputInterface                                                   $output,
        #[Argument(description: 'The ID of the user to retrieve')] string $id
    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('Retrieving AR24 user for ID: %s', $id));

        try {
            $user = $this->userClient->getById($id);
            $userData = $this->transformer->transform($user);

            $io->success('User found!');
            $io->table(
                ['Field', 'Value'],
                array_map(fn($k, $v) => [$k, is_array($v) ? json_encode($v) : $v], array_keys($userData), $userData)
            );
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
