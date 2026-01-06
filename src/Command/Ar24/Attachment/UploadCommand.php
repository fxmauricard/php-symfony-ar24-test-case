<?php

namespace App\Command\Ar24\Attachment;

use App\Infrastructure\Ar24\Http\Attachment\AttachmentClient;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ar24:attachment:upload',
    description: 'Upload an AR24 attachment from a file',
)]
/**
 * Command to upland an AR24 attachment.
 */
readonly class UploadCommand
{
    public function __construct(
        private AttachmentClient $client,
    ) {
    }

    public function __invoke(
        InputInterface                                                  $input,
        OutputInterface                                                 $output,
        #[Argument(description: 'User ID')] int                         $userId,
        #[Argument(description: 'Path to the file to upload')] string   $filePath,
        #[Argument(description: 'Filename')] ?string                    $fileName = null,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $io->title('Upload AR24 attachment');

        if (!is_file($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }

        try {
            $attachmentId = $this->client->upload($userId, $filePath, $fileName);

            $io->success('Attachment uploaded successfully');
            $io->table(
                ['Field', 'Value'],
                [
                    ['Attachment ID', $attachmentId],
                ]
            );
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
