<?php

namespace App\Infrastructure\Http\RegisteredMail;

use App\Infrastructure\Http\Attachment\Exception\Ar24AttachmentException;
use App\Infrastructure\Http\Client\Ar24ApiClient;
use App\Infrastructure\Http\Client\Exception\Ar24ApiException;
use App\Infrastructure\Http\RegisteredMail\DataTransformer\Ar24RegisteredMailDataTransformer;
use App\Infrastructure\Http\RegisteredMail\Exception\Ar24ContentException;
use App\Infrastructure\Http\RegisteredMail\Exception\Ar24RecipientException;
use App\Infrastructure\Http\RegisteredMail\Model\Ar24RegisteredMail;
use App\Infrastructure\Http\RegisteredMail\Exception\Ar24AuthenticationException;
use App\Infrastructure\Http\User\Exception\Ar24UserException;

/**
 * Client to interact with AR24 Registered Mail API.
 */
final readonly class Ar24RegisteredMailClient
{
    public function __construct(
        private Ar24ApiClient $client,
        private Ar24RegisteredMailDataTransformer $transformer,
    ) {
    }

    /**
     * Send a simple or eIDAS Registered Mail.
     *
     * @param int $userId User ID
     * @param Ar24RegisteredMail $registeredMail
     *
     * @return Ar24RegisteredMail
     *
     * @throws Ar24ApiException
     */
    public function send(int $userId, Ar24RegisteredMail $registeredMail): Ar24RegisteredMail
    {
        $data = $this->client->post('/mail', [
            'body' => $this->transformer->transform($registeredMail, false)
                + [
                    'id_user' => $userId,
                    'attachment' => $registeredMail->attachments ?? [] // Fix to ensure attachments are sent under the 'attachment' key as API is not consistent.
            ],
        ], [
            'missing_email' => [Ar24RecipientException::class, 'Please specify an email address'],
            'same_sender_recipients_emails' => [Ar24RecipientException::class, 'Recipient email and sender email must be different'],
            'invalid_recipient' => [Ar24RecipientException::class, 'Recipient email is invalid'],
            'invalid_email' => [Ar24RecipientException::class, 'Recipient\'s email address is incorrect, the domain does not exist'],
            'group_not_exist' => [Ar24RecipientException::class, 'Group ID provided does not exist'],
            'user_not_exist' => [Ar24UserException::class, 'There is no user with this address on AR24'],
            'user_account_not_confirmed' => [Ar24UserException::class, 'User has to confirm its email address first'],
            'user_eula_not_accepted' => [Ar24UserException::class, 'Sender must accept AR24 EULA first'],
            'user_name_empty' => [Ar24UserException::class, 'Sender name (firstname or lastname) cannot be empty'],
            'user_no_payment' => [Ar24UserException::class, 'User or Master has no payment method'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
            'attachment_not_exists' => [Ar24AttachmentException::class, 'At least one of the attachment ID\'s you proviced doesn\'t exist'],
            'attachment_unavailable' => [Ar24AttachmentException::class, 'One of the attachment ID\'s you provided doesn\'t exist'],
            'attachment_too_big' => [Ar24AttachmentException::class, 'File exceeds size limit'],
            'content_exceeds_limit' => [Ar24ContentException::class, 'Content parameters is too long'],
            'forbidden_html' => [Ar24ContentException::class, 'The content has some forbidden html tag into it, please clean your input'],
            'error_no_content_no_attachment' => [Ar24ContentException::class, 'Empty mail ; content is empty and there are no attachments'],
            'authentication_otp_hash_invalid' => [Ar24AuthenticationException::class, 'OTP hash is required (from 1h authentification method) and the one you provided is not correct'],
            'authentication_otp_invalid' => [Ar24AuthenticationException::class, 'Invalid otp code'],
            'authentication_missing' => [Ar24AuthenticationException::class, 'Invalid eidas identification (ssl or otp)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     * Get registered mail info by ID.
     *
     * @param int $id Registered Mail ID
     *
     * @throws Ar24ApiException
     */
    public function getById(int $id): Ar24RegisteredMail
    {
        $data = $this->client->get('/mail', [
            'query' => [
                'id' => $id,
            ],
        ], [
            'missing_erm_id' => [Ar24AuthenticationException::class, 'Please provide a valid mail ID'],
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);

        return $this->transformer->reverseTransform($data['result'] ?? []);
    }

    /**
     * List all mails from a specific user.
     * (implemented only for testing purposes, so only User ID parameter handled - should be in Ar24UserClient eventually as it's a 'user' endpoint call).
     *
     * @param int $userId User ID
     *
     * @return Ar24RegisteredMail[]
     *
     * @throws Ar24ApiException
     */
    public function list(int $userId): array
    {
        $data = $this->client->get( '/user/mail', [
            'query' => [
                'id_user' => $userId,
            ],
        ], [
            'user_unavailable' => [Ar24UserException::class, 'You tried to access a resource that is not related to your API (user has not granted API access)'],
        ]);
        $registeredMailsData = $data['result'] ?? [];

        return array_map(
            fn(array $registeredMailData) => $this->transformer->reverseTransform($registeredMailData),
            $registeredMailsData
        );
    }
}
