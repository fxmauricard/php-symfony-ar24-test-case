<?php

namespace App\Infrastructure\Ar24\Http\RegisteredMail\Model;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute\JsonField;
use DateTimeImmutable;

final class RegisteredMail
{
    public function __construct(
        #[JsonField]
        public ?int $id = null,

        #[JsonField]
        public ?string $type = null,

        #[JsonField]
        public ?string $status = null,

        #[JsonField(name: 'custom_ref')]
        public ?string $customRef = null,

        #[JsonField]
        public ?DateTimeImmutable $creation = null,

        #[JsonField]
        public ?array $recipients = null,

        #[JsonField]
        public ?array $attachments = null,

        #[JsonField]
        public ?array $payload = null,

        #[JsonField(name: 'from_name')]
        public ?string $fromName = null,

        #[JsonField(name: 'from_email')]
        public ?string $fromEmail = null,

        #[JsonField]
        public ?string $address1 = null,

        #[JsonField]
        public ?string $address2 = null,

        #[JsonField]
        public ?string $city = null,

        #[JsonField]
        public ?string $zipcode = null,

        #[JsonField(name: 'to_name')]
        public ?string $toName = null,

        #[JsonField(name: 'to_firstname')]
        public ?string $toFirstname = null,

        #[JsonField(name: 'to_lastname')]
        public ?string $toLastname = null,

        #[JsonField(name: 'to_company')]
        public ?string $toCompany = null,

        #[JsonField(name: 'to_email')]
        public ?string $toEmail = null,

        #[JsonField(name: 'dest_statut')]
        public ?string $destStatut = null,

        #[JsonField(name: 'id_sender')]
        public ?int $idSender = null,

        #[JsonField(name: 'id_creator')]
        public ?int $idCreator = null,

        #[JsonField(name: 'price_ht')]
        public ?int $priceHt = null,

        #[JsonField(name: 'payment_slug')]
        public ?string $paymentSlug = null,

        #[JsonField(name: 'ref_dossier')]
        public ?string $refDossier = null,

        #[JsonField(name: 'ref_client')]
        public ?string $refClient = null,

        #[JsonField(name: 'ref_facturation')]
        public ?string $refFacturation = null,

        #[JsonField]
        public ?DateTimeImmutable $date = null,

        #[JsonField(name: 'full_hash_sha256')]
        public ?string $fullHashSha256 = null,

        #[JsonField(name: 'send_fail')]
        public ?bool $sendFail = null,

        #[JsonField(name: 'is_eidas')]
        public ?bool $isEidas = null,

        #[JsonField(name: 'proof_ev_url')]
        public ?string $proofEvUrl = null,

        #[JsonField(name: 'ts_ev_date')]
        public ?DateTimeImmutable $tsEvDate = null,

        #[JsonField(name: 'proof_ar_url')]
        public ?string $proofArUrl = null,

        #[JsonField(name: 'view_date')]
        public ?DateTimeImmutable $viewDate = null,

        #[JsonField(name: 'proof_ng_url')]
        public ?string $proofNgUrl = null,

        #[JsonField(name: 'negligence_date')]
        public ?DateTimeImmutable $negligenceDate = null,

        #[JsonField(name: 'proof_rf_url')]
        public ?string $proofRfUrl = null,

        #[JsonField(name: 'refused_date')]
        public ?DateTimeImmutable $refusedDate = null,

        #[JsonField(name: 'proof_bc_url')]
        public ?string $proofBcUrl = null,

        #[JsonField(name: 'bounced_date')]
        public ?DateTimeImmutable $bouncedDate = null,

        #[JsonField(name: 'pdf_content')]
        public ?string $pdfContent = null,

        #[JsonField]
        public ?string $zip = null,

        #[JsonField(name: 'req_notify_ev')]
        public ?bool $reqNotifyEv = null,

        #[JsonField(name: 'req_notify_ar')]
        public ?bool $reqNotifyAr = null,

        #[JsonField(name: 'req_notify_rf')]
        public ?bool $reqNotifyRf = null,

        #[JsonField(name: 'req_notify_ng')]
        public ?bool $reqNotifyNg = null,

        #[JsonField(name: 'attachments_details')]
        /** @var AttachmentDetail[]|null */
        public ?array $attachmentsDetails = null,

        #[JsonField(name: 'update')]
        /** @var RecipientUpdate[]|null */
        public ?array $updates = null,
    ) {}
}
