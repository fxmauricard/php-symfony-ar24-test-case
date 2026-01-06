<?php

namespace App\Infrastructure\Ar24\Http\RegisteredMail;

use App\Infrastructure\Ar24\Http\Attachment\Exception\AttachmentException;
use App\Infrastructure\Ar24\Http\Client\ApiClient;
use App\Infrastructure\Ar24\Http\Client\Exception\ApiException;
use App\Infrastructure\Ar24\Http\RegisteredMail\DataTransformer\RegisteredMailDataTransformer;
use App\Infrastructure\Ar24\Http\RegisteredMail\Exception\AuthenticationException;
use App\Infrastructure\Ar24\Http\RegisteredMail\Exception\ContentException;
use App\Infrastructure\Ar24\Http\RegisteredMail\Exception\RecipientException;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RegisteredMail;
use App\Infrastructure\Ar24\Http\User\Exception\UserException;

/**
 * Client to interact with AR24 Registered Mail API.
 */
final readonly class RegisteredMailClient
{
    public function __construct(
        private ApiClient                     $client,
        private RegisteredMailDataTransformer $transformer,
    ) {
    }

    /**
     * Send a simple or eIDAS Registered Mail.
     *
     * @param int $userId User ID
     * @param RegisteredMail $registeredMail
     *
     * @return RegisteredMail
     *
     * @throws ApiException
     */
    public function send(int $userId, RegisteredMail $registeredMail): RegisteredMail
    {
        $data = $this->client->post('/mail', [
            'body' => $this->transformer->transform($registeredMail, false)
                + [
                    'id_user' => $userId,
                    'attachment' => $registeredMail->attachments ?? [] // Fix to ensure attachments are sent under the 'attachment' key as API is not consistent.
            ],
        ], [
            'missing_email' => [RecipientException::class, 'Please specify an email address'],
            'same_sender_recipients_emails' => [RecipientException::class, 'Recipient email and sender email must be different'],
            'invalid_recipient' => [RecipientException::class, 'Recipient email is invalid'],
            'invalid_email' => [RecipientException::class, 'Recipient\'s email address is incorrect, the domain does not exist'],
            'group_not_exist' => [RecipientException::class, 'Group ID provided does not exist'],
            'user_not_exist' => [UserException::class, 'There is no user with this address on AR24'],
            'user_account_not_confirmed' => [UserException::class, 'User has to confirm its email address first'],
            'user_eula_not_accepted' => [UserException::class, 'Sender must accept AR24 EULA first'],
            'user_name_empty' => [UserException::class, 'Sender name (firstname or lastname) cannot be empty'],
            'user_no_payment' => [UserException::class, 'User or Master has no payment method'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
            'attachment_not_exists' => [AttachmentException::class, 'At least one of the attachment ID\'s you proviced doesn\'t exist'],
            'attachment_unavailable' => [AttachmentException::class, 'One of the attachment ID\'s you provided doesn\'t exist'],
            'attachment_too_big' => [AttachmentException::class, 'File exceeds size limit'],
            'content_exceeds_limit' => [ContentException::class, 'Content parameters is too long'],
            'forbidden_html' => [ContentException::class, 'The content has some forbidden html tag into it, please clean your input'],
            'error_no_content_no_attachment' => [ContentException::class, 'Empty mail ; content is empty and there are no attachments'],
            'authentication_otp_hash_invalid' => [AuthenticationException::class, 'OTP hash is required (from 1h authentification method) and the one you provided is not correct'],
            'authentication_otp_invalid' => [AuthenticationException::class, 'Invalid otp code'],
            'authentication_missing' => [AuthenticationException::class, 'Invalid eidas identification (ssl or otp)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     * Get registered mail info by ID.
     *
     * @param int $id Registered Mail ID
     *
     * @throws ApiException
     */
    public function getById(int $id): RegisteredMail
    {
        $data = $this->client->get('/mail', [
            'query' => [
                'id' => $id,
            ],
        ], [
            'missing_erm_id' => [AuthenticationException::class, 'Please provide a valid mail ID'],
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     * List all mails from a specific user.
     * (implemented only for testing purposes, so only User ID parameter handled - should be in Ar24UserClient eventually as it's a 'user' endpoint call).
     *
     * @param int $userId User ID
     *
     * @return RegisteredMail[]
     *
     * @throws ApiException
     */
    public function list(int $userId): array
    {
        $data = $this->client->get( '/user/mail', [
            'query' => [
                'id_user' => $userId,
            ],
        ], [
            'user_unavailable' => [UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $registeredMailsData = $data['result'] ?? [];

        return array_map(
            fn(array $registeredMailData) => $this->transformer->reverseTransform($registeredMailData),
            $registeredMailsData
        );
    }
}
