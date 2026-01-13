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
    name: 'ar24:registered-mail:send',
    description: 'Send an AR24 registered mail from a JSON file',
)]
/**
 * Command to send AR24 registered mail.
 */
readonly class SendCommand
{
    public function __construct(
        private RegisteredMailClient $client,
        private RegisteredMailDataTransformer $transformer,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'User ID')] int $userId,
        #[Argument(description: 'Path to a JSON file describing the registered mail')] string $jsonPath,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Send AR24 registered mail');

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
            $registeredMail = $this->transformer->reverseTransform($data);
            $createdRegisteredMail = $this->client->send($userId, $registeredMail);
            $createdRegisteredMailData = $this->transformer->transform($createdRegisteredMail);

            $io->success('Registered mail sent successfully');
            $io->table(
                ['Field', 'Value'],
                array_map(fn ($k, $v) => [$k, is_array($v) ? json_encode($v) : $v], array_keys($createdRegisteredMailData), $createdRegisteredMailData)
            );
        } catch (\Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
