<?php

namespace App\Infrastructure\Http\Attachment;

use App\Infrastructure\Http\Attachment\Exception\Ar24AttachmentException;
use App\Infrastructure\Http\Attachment\Model\Ar24Attachment;
use App\Infrastructure\Http\Client\Ar24ApiClient;
use App\Infrastructure\Http\Client\Enum\Ar24Sort;
use App\Infrastructure\Http\Client\Exception\Ar24ApiException;
use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Http\User\Exception\Ar24UserException;

/**
 * Client to interact with AR24 Attachment API.
 */
final readonly class Ar24AttachmentClient
{
    public function __construct(
        private Ar24ApiClient $client,
        private AutomaticTransformer $transformer,
    ) {
    }

    /**
     * Upload an attachment.
     *
     * @param int $userId User ID
     * @param string $filePath Path to the file to upload
     * @param string|null $fileName Optional file name to use for the uploaded file
     *
     * @return int|null  The ID of the uploaded attachment
     *
     * @throws Ar24ApiException
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
            'attachment_empty_name' => [Ar24AttachmentException::class, 'We can\'t extract a file name for the uploaded file (file_name parameter or name parameter in the file object are empty or wrong encoded)'],
            'attachment_too_big' => [Ar24AttachmentException::class, 'File is empty (0B) or too big'],
            'attachment_missing_file' => [Ar24AttachmentException::class, 'You didn\'t fill the file parameter correctly'],
            'missing_user_id' => [Ar24UserException::class, 'Please specify a valid user ID'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $data['result']['file_id'] ?? null;
    }

    /**
     * List attachments for a user.
     *
     * @param int $userId User ID
     * @param int $max Number of results returned
     * @param int $start Return result from the defined start index
     * @param Ar24Sort $sort Sort by ID
     *
     * @return Ar24Attachment[]
     *
     * @throws Ar24ApiException
     */
    public function listByUserId(int $userId, int $max = 10, int $start = 0, Ar24Sort $sort = Ar24Sort::ASC): array
    {
        $data = $this->client->get( '/user/attachment', [
            'query' => [
                'id_user' => $userId,
                'max' => $max,
                'start' => $start,
                'sort' => $sort->value,
            ],
        ], [
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $attachmentsData = $data['result']['attachments'] ?? [];

        return array_map(
            fn(array $attachmentData) => $this->transformer->reverseTransform($attachmentData, Ar24Attachment::class),
            $attachmentsData
        );
    }

    /**
     * List attachments from a mail.
     *
     * @param int $registeredMailId Registered Mail ID
     *
     * @return Ar24Attachment[]
     *
     * @throws Ar24ApiException
     */
    public function listByRegisteredMailId(int $registeredMailId): array
    {
        $data = $this->client->get( '/attachment', [
            'query' => [
                'id' => $registeredMailId,
            ],
        ], [
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $attachmentsData = $data['result']['attachments'] ?? [];

        return array_map(
            fn(array $attachmentData) => $this->transformer->reverseTransform($attachmentData, Ar24Attachment::class),
            $attachmentsData
        );
    }
}
