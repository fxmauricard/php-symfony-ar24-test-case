<?php

namespace App\Infrastructure\Http\RegisteredMail\Model;

use App\Infrastructure\Http\Common\DataTransformer\Attribute\JsonField;
use DateTimeImmutable;

/**
 * Model representing an update to a recipient's information in a registered mail.
 */
final class Ar24RecipientUpdate
{
    public function __construct(
        #[JsonField(name: 'id_recipient_update')]
        public ?int $id = null,

        #[JsonField]
        public ?DateTimeImmutable $date = null,

        #[JsonField]
        public ?string $lastname = null,

        #[JsonField]
        public ?string $firstname = null,

        #[JsonField]
        public ?string $company = null,
    ) {}
}
