<?php

namespace App\Command\Ar24\Attachment;

use App\Infrastructure\Http\Attachment\Ar24AttachmentClient;
use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:attachment:list-by-user-id',
    description: 'List all AR24 attachments for a user',
)]
/**
 * Command to list AR24 attachments for a user.
 */
readonly class ListByUserIdCommand
{
    public function __construct(
        private Ar24AttachmentClient    $client,
        private AutomaticTransformer    $transformer,
    ) {
    }

    public function __invoke(
        InputInterface                                                                                  $input,
        OutputInterface                                                                                 $output,
        #[Argument(description: 'The ID of the user for which we want to retrieve attachments')] int    $userId,
    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Listing AR24 attachments for a user');

        try {
            $attachments = $this->client->listByUserId($userId);
            var_dump($attachments);

            if (empty($attachments)) {
                $io->warning('No attachments found.');
                return Command::SUCCESS;
            }

            $io->success(sprintf('Found %d attachment(s).', count($attachments)));

            $attachmentsData = array_map(fn($attachment) => $this->transformer->transform($attachment), $attachments);

            $headers = array_keys($attachmentsData[0]);
            $rows = array_map(function($attachment) {
                return array_map(fn($v) => is_array($v) ? json_encode($v) : $v, array_values($attachment));
            }, $attachmentsData);

            $io->table($headers, $rows);
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
