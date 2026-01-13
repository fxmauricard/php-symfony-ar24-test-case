<?php

namespace App\Infrastructure\Ar24\Http\User\Model;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute\JsonField;
use App\Infrastructure\Ar24\Http\User\Enum\UserStatut;

/**
 * Model representing a user for the AR24 API.
 */
final class User
{
    public function __construct(
        #[JsonField]
        public ?int $id = null,

        #[JsonField]
        public ?string $firstname = null,

        #[JsonField]
        public ?string $lastname = null,

        #[JsonField]
        public ?string $name = null,

        #[JsonField]
        public ?string $email = null,

        #[JsonField]
        public ?string $gender = null,

        #[JsonField]
        public ?UserStatut $statut = null,

        #[JsonField]
        public ?string $company = null,

        #[JsonField(name: 'company_siret')]
        public ?string $companySiret = null,

        #[JsonField(name: 'company_tva')]
        public ?string $companyTva = null,

        #[JsonField]
        public ?string $country = null,

        #[JsonField]
        public ?string $address1 = null,

        #[JsonField]
        public ?string $address2 = null,

        #[JsonField]
        public ?string $zipcode = null,

        #[JsonField]
        public ?string $city = null,

        #[JsonField(name: 'notif_billing')]
        public ?bool $notifBilling = null,

        #[JsonField(name: 'billing_email')]
        public ?string $billingEmail = null,

        #[JsonField]
        public ?bool $confirmed = null,

        #[JsonField]
        public ?bool $cgu = null,

        #[JsonField(name: 'notify_ev')]
        public ?bool $notifyEv = null,

        #[JsonField(name: 'notify_ar')]
        public ?bool $notifyAr = null,

        #[JsonField(name: 'notify_rf')]
        public ?bool $notifyRf = null,

        #[JsonField(name: 'notify_ng')]
        public ?bool $notifyNg = null,

        #[JsonField(name: 'notify_bc')]
        public ?bool $notifyBc = null,

        #[JsonField(name: 'notify_consent')]
        public ?bool $notifyConsent = null,

        #[JsonField(name: 'notify_eidas_to_valid')]
        public ?bool $notifyEidasToValid = null,

        #[JsonField(name: 'notify_recipient_update')]
        public ?bool $notifyRecipientUpdate = null,

        #[JsonField(name: 'notify_waiting_ar_answer')]
        public ?bool $notifyWaitingArAnswer = null,

        #[JsonField(name: 'is_legal_entity')]
        public ?bool $isLegalEntity = null,

        #[JsonField(name: 'custom_ref')]
        public ?string $customRef = null,

        #[JsonField(name: 'company_activity')]
        public ?string $companyActivity = null,

        #[JsonField(name: 'c_phone')]
        public ?string $cPhone = null,

        #[JsonField(name: 'c_mobile')]
        public ?string $cMobile = null,

        #[JsonField(name: 'notify_dp')]
        public ?bool $notifyDp = null,

        #[JsonField(name: 'payment_active')]
        public ?bool $paymentActive = null,

        #[JsonField(name: 'billing_tva')]
        public ?string $billingTva = null,

        #[JsonField]
        public ?\DateTimeImmutable $creation = null,

        #[JsonField(name: 'notif_cgu')]
        public ?bool $notifCgu = null,

        #[JsonField(name: 'notif_profil_uncomplete')]
        public ?bool $notifProfilUncomplete = null,

        #[JsonField(name: 'is_payment_possible')]
        public ?bool $isPaymentPossible = null,
    ) {
    }
}
