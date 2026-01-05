<?php

namespace App\Infrastructure\Http\RegisteredMail\Model;

use App\Infrastructure\Http\Common\DataTransformer\Attribute\JsonField;

/**
 * Model representing details of an attachment in a registered mail.
 */
final class Ar24AttachmentDetail
{
    public function __construct(
        #[JsonField]
        public ?int $id = null,

        #[JsonField]
        public ?string $name = null,

        #[JsonField(name: 'hash_sha1')]
        public ?string $hashSha1 = null,

        #[JsonField(name: 'file_size')]
        public ?int $fileSize = null,

        #[JsonField(name: 'human_file_size')]
        public ?string $humanFileSize = null,

        #[JsonField(name: 'download_url')]
        public ?string $downloadUrl = null,
    ) {}
}
