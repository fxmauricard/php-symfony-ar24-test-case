<?php

namespace App\Infrastructure\Ar24\Http\Attachment\Model;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute\JsonField;
use DateTimeImmutable;

final class Attachment
{
    public function __construct(
        #[JsonField(name: 'id_api_attachment')]
        public ?int $id = null,

        #[JsonField]
        public ?string $hash = null,

        #[JsonField]
        public ?int $filesize = null,

        #[JsonField]
        public ?string $filename = null,

        #[JsonField(name: 'id_user')]
        public ?int $userId = null,

        #[JsonField(name: 'upload_date')]
        public ?DateTimeImmutable $uploadDate = null,

        #[JsonField(name: 'api_id')]
        public ?string $apiId = null,
    ) {}
}
