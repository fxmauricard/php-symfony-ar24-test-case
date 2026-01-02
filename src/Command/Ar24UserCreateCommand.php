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
    name: 'ar24:user:create',
    description: 'Create an AR24 user from a JSON file',
)]
readonly class Ar24UserCreateCommand
{
    public function __construct(
        private Ar24UserClient          $userClient,
        private Ar24UserDataTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface                                                       $input,
        OutputInterface                                                      $output,
        #[Argument(description: 'Path to a JSON file describing the user')] string $jsonPath,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create AR24 user');

        if (!is_file($jsonPath)) {
            $io->error(sprintf('File not found: %s', $jsonPath));
            return Command::FAILURE;
        }

        $content = file_get_contents($jsonPath);
        if (false === $content) {
            $io->error(sprintf('Unable to read file: %s', $jsonPath));
            return Command::FAILURE;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            $io->error('Invalid JSON content (expecting an object)');
            return Command::FAILURE;
        }

        try {
            $user = $this->transformer->reverseTransform($data);
            $createdUser = $this->userClient->create($user);
            $createdUserData = $this->transformer->transform($createdUser);

            $io->success('User created successfully');
            $io->table(
                ['Field', 'Value'],
                array_map(fn($k, $v) => [$k, is_array($v) ? json_encode($v) : $v], array_keys($createdUserData), $createdUserData)
            );
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

