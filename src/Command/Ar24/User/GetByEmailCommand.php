<?php

namespace App\Command\Ar24\User;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\User\UserClient;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:user:get-by-email',
    description: 'Get an AR24 user by email',
)]
/**
 * Command to retrieve an AR24 user by his email.
 */
readonly class GetByEmailCommand
{
    public function __construct(
        private UserClient           $client,
        private AutomaticTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface                                                       $input,
        OutputInterface                                                      $output,
        #[Argument(description: 'The email of the user to retrieve')] string $email
    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('Retrieving AR24 user for email: %s', $email));

        try {
            $user = $this->client->getByEmail($email);
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
