<?php

namespace App\Infrastructure\Ar24\Http\Attachment;

use App\Infrastructure\Ar24\Http\Attachment\Exception\AttachmentException;
use App\Infrastructure\Ar24\Http\Attachment\Model\Attachment;
use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Client\Enum\Sort;
use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\User\Exception\UserException;

/**
 * Client to interact with AR24 Attachment API.
 */
final readonly class AttachmentClient
{
    public function __construct(
        private ApiClient $client,
        private AutomaticTransformer $transformer,
    ) {
    }

    /**
     * Upload an attachment.
     *
     * @param int         $userId   User ID
     * @param string      $filePath Path to the file to upload
     * @param string|null $fileName Optional file name to use for the uploaded file
     *
     * @return int|null The ID of the uploaded attachment
     *
     * @throws ApiException
     */
    public function upload(int $userId, string $filePath, ?string $fileName = null): ?int
    {
        $fileHandle = fopen($filePath, 'r');

        $data = $this->client->post('/attachment', [
            'body' => [
                'file' => $fileHandle,
                'file_name' => $fileName,
                'id_user' => $userId,
            ],
        ], [
            'attachment_empty_name' => [AttachmentException::class, 'We can\'t extract a file name for the uploaded file (file_name parameter or name parameter in the file object are empty or wrong encoded)'],
            'attachment_too_big' => [AttachmentException::class, 'File is empty (0B) or too big'],
            'attachment_missing_file' => [AttachmentException::class, 'You didn\'t fill the file parameter correctly'],
            'missing_user_id' => [UserException::class, 'Please specify a valid user ID'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $data['result']['file_id'] ?? null;
    }

    /**
     * List attachments for a user.
     *
     * @param int  $userId User ID
     * @param int  $max    Number of results returned
     * @param int  $start  Return result from the defined start index
     * @param Sort $sort   Sort by ID
     *
     * @return Attachment[]
     *
     * @throws ApiException
     */
    public function listByUserId(int $userId, int $max = 10, int $start = 0, Sort $sort = Sort::ASC): array
    {
        $data = $this->client->get('/user/attachment', [
            'query' => [
                'id_user' => $userId,
                'max' => $max,
                'start' => $start,
                'sort' => $sort->value,
            ],
        ], [
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $attachmentsData = $data['result']['attachments'] ?? [];

        return array_map(
            fn (array $attachmentData) => $this->transformer->reverseTransform($attachmentData, Attachment::class),
            $attachmentsData
        );
    }

    /**
     * List attachments from a mail.
     *
     * @param int $registeredMailId Registered Mail ID
     *
     * @return Attachment[]
     *
     * @throws ApiException
     */
    public function listByRegisteredMailId(int $registeredMailId): array
    {
        $data = $this->client->get('/attachment', [
            'query' => [
                'id' => $registeredMailId,
            ],
        ], [
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $attachmentsData = $data['result']['attachments'] ?? [];

        return array_map(
            fn (array $attachmentData) => $this->transformer->reverseTransform($attachmentData, Attachment::class),
            $attachmentsData
        );
    }
}
