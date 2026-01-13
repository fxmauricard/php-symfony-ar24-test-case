<?php

namespace App\Infrastructure\Ar24\Http\RegisteredMail\DataTransformer;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\AttachmentDetail;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RecipientUpdate;
use App\Infrastructure\Ar24\Http\RegisteredMail\Model\RegisteredMail;

/**
 * DataTransformer to convert Ar24RegisteredMail model to array and vice versa.
 */
final readonly class RegisteredMailDataTransformer
{
    public function __construct(
        private AutomaticTransformer $automaticTransformer,
    ) {
    }

    /**
     * Transforms an Ar24RegisteredMail object into an array (for API request).
     */
    public function transform(RegisteredMail $mail, $includeNullValues = true): array
    {
        $data = $this->automaticTransformer->transform($mail);

        // Handle nested objects (Ar24AttachmentDetail and Ar24RecipientUpdate)
        if (isset($data['attachments_details']) && is_array($data['attachments_details'])) {
            $data['attachments_details'] = array_map(function ($attachmentDetail) {
                if (!$attachmentDetail instanceof AttachmentDetail) {
                    return $attachmentDetail;
                }

                return $this->automaticTransformer->transform($attachmentDetail);
            }, $data['attachments_details']);
        }

        if (isset($data['update']) && is_array($data['update'])) {
            $data['update'] = array_map(function ($upd) {
                if (!$upd instanceof RecipientUpdate) {
                    return $upd;
                }
                $transformed = $this->automaticTransformer->transform($upd);
                // Rename id field to id_recipient_update for API compatibility
                if (isset($transformed['id'])) {
                    $transformed['id_recipient_update'] = $transformed['id'];
                    unset($transformed['id']);
                }

                return $transformed;
            }, $data['update']);
        }

        // Remove null values
        if (!$includeNullValues) {
            foreach ($data as $key => $value) {
                if (null === $value) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Transforms an array into an Ar24RegisteredMail object (from API response).
     */
    public function reverseTransform(array $data): RegisteredMail
    {
        // Convert nested object data to model instances
        if (isset($data['attachments_details']) && is_array($data['attachments_details'])) {
            $data['attachments_details'] = array_map(function ($raw) {
                return $this->automaticTransformer->reverseTransform($raw, AttachmentDetail::class);
            }, $data['attachments_details']);
        }

        if (isset($data['update']) && is_array($data['update'])) {
            $data['update'] = array_map(function ($raw) {
                return $this->automaticTransformer->reverseTransform($raw, RecipientUpdate::class);
            }, $data['update']);
        }

        return $this->automaticTransformer->reverseTransform($data, RegisteredMail::class);
    }
}
