<?php

namespace App\Infrastructure\Http\User\DataTransformer;

use App\Infrastructure\Http\User\Model\Ar24User;
use App\Infrastructure\Http\User\Enum\Ar24UserStatut;
use DateTimeImmutable;

/**
 * DataTransformer to convert Ar24User model to array and vice versa.
 */
final class Ar24UserDataTransformer
{
    /**
     * Transforms an Ar24User object into an array (for API request).
     * Statut is sent as string|null because API expects those types.
     */
    public function transform(Ar24User $user): array
    {
        $statut = $user->getStatut();

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'gender' => $user->getGender(),
            'statut' => $statut instanceof Ar24UserStatut ? $statut->value : Ar24UserStatut::INDIVIDUAL->value,
            'company' => $user->getCompany(),
            'company_activity' => $user->getCompanyActivity(),
            'company_siret' => $user->getCompanySiret(),
            'company_tva' => $user->getCompanyTva(),
            'country' => $user->getCountry(),
            'address1' => $user->getAddress1(),
            'address2' => $user->getAddress2(),
            'zipcode' => $user->getZipcode(),
            'city' => $user->getCity(),
            'c_phone' => $user->getCPhone(),
            'c_mobile' => $user->getCMobile(),
            'notif_billing' => null === $user->isNotifBilling() ? null : (int) $user->isNotifBilling(),
            'billing_email' => $user->getBillingEmail(),
            'billing_tva' => $user->getBillingTva(),
            'confirmed' => null === $user->isConfirmed() ? null : (int) $user->isConfirmed(),
            'cgu' => null === $user->hasCgu() ? null : (int) $user->hasCgu(),
            'notify_dp' => null === $user->isNotifyDp() ? null : (int) $user->isNotifyDp(),
            'notify_ev' => null === $user->isNotifyEv() ? null : (int) $user->isNotifyEv(),
            'notify_ar' => null === $user->isNotifyAr() ? null : (int) $user->isNotifyAr(),
            'notify_rf' => null === $user->isNotifyRf() ? null : (int) $user->isNotifyRf(),
            'notify_ng' => null === $user->isNotifyNg() ? null : (int) $user->isNotifyNg(),
            'notify_bc' => null === $user->isNotifyBc() ? null : (int) $user->isNotifyBc(),
            'notify_consent' => null === $user->isNotifyConsent() ? null : (int) $user->isNotifyConsent(),
            'notify_eidas_to_valid' => null === $user->isNotifyEidasToValid() ? null : (int) $user->isNotifyEidasToValid(),
            'notify_recipient_update' => null === $user->isNotifyRecipientUpdate() ? null : (int) $user->isNotifyRecipientUpdate(),
            'notify_waiting_ar_answer' => null === $user->isNotifyWaitingArAnswer() ? null : (int) $user->isNotifyWaitingArAnswer(),
            'notif_cgu' => null === $user->isNotifCgu() ? null : (int) $user->isNotifCgu(),
            'notif_profil_uncomplete' => null === $user->isNotifProfilUncomplete() ? null : (int) $user->isNotifProfilUncomplete(),
            'payment_active' => null === $user->isPaymentActive() ? null : (int) $user->isPaymentActive(),
            'is_payment_possible' => null === $user->isPaymentPossible() ? null : (int) $user->isPaymentPossible(),
            'is_legal_entity' => null === $user->isLegalEntity() ? null : (int) $user->isLegalEntity(),
            'custom_ref' => $user->getCustomRef(),
            'creation' => $user->getCreation()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Transforms an array into an Ar24User object (from API response).
     * Assumption: API provides string|null for 'statut'. Defaults to 'particulier'.
     */
    public function reverseTransform(array $data): Ar24User
    {
        $user = new Ar24User();

        // Handle statut specially because the model uses an enum and API sends string|null
        $statutValue = $data['statut'] ?? null;
        if (array_key_exists('statut', $data)) {
            unset($data['statut']);
        }
        $enumStatut = is_string($statutValue) ? Ar24UserStatut::tryFrom($statutValue) : null;
        $user->setStatut($enumStatut ?? Ar24UserStatut::INDIVIDUAL);

        // Handle creation (string -> DateTimeImmutable)
        if (array_key_exists('creation', $data)) {
            $creation = $data['creation'];
            unset($data['creation']);
            if (is_string($creation)) {
                $parsed = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $creation);
                $user->setCreation($parsed ?: null);
            } else {
                $user->setCreation(null);
            }
        }

        $mapping = [
            'id' => 'setId',
            'name' => 'setName',
            'firstname' => 'setFirstname',
            'lastname' => 'setLastname',
            'email' => 'setEmail',
            'gender' => 'setGender',
            'company' => 'setCompany',
            'company_activity' => 'setCompanyActivity',
            'company_siret' => 'setCompanySiret',
            'company_tva' => 'setCompanyTva',
            'country' => 'setCountry',
            'address1' => 'setAddress1',
            'address2' => 'setAddress2',
            'zipcode' => 'setZipcode',
            'city' => 'setCity',
            'c_phone' => 'setCPhone',
            'c_mobile' => 'setCMobile',
            'notif_billing' => 'setNotifBilling',
            'billing_email' => 'setBillingEmail',
            'billing_tva' => 'setBillingTva',
            'confirmed' => 'setConfirmed',
            'cgu' => 'setCgu',
            'notify_dp' => 'setNotifyDp',
            'notify_ev' => 'setNotifyEv',
            'notify_ar' => 'setNotifyAr',
            'notify_rf' => 'setNotifyRf',
            'notify_ng' => 'setNotifyNg',
            'notify_bc' => 'setNotifyBc',
            'notify_consent' => 'setNotifyConsent',
            'notify_eidas_to_valid' => 'setNotifyEidasToValid',
            'notify_recipient_update' => 'setNotifyRecipientUpdate',
            'notify_waiting_ar_answer' => 'setNotifyWaitingArAnswer',
            'notif_cgu' => 'setNotifCgu',
            'notif_profil_uncomplete' => 'setNotifProfilUncomplete',
            'payment_active' => 'setPaymentActive',
            'is_payment_possible' => 'setPaymentPossible',
            'is_legal_entity' => 'setIsLegalEntity',
            'custom_ref' => 'setCustomRef',
        ];

        $boolFields = [
            'notif_billing',
            'confirmed',
            'cgu',
            'notify_dp',
            'notify_ev',
            'notify_ar',
            'notify_rf',
            'notify_ng',
            'notify_bc',
            'notify_consent',
            'notify_eidas_to_valid',
            'notify_recipient_update',
            'notify_waiting_ar_answer',
            'notif_cgu',
            'notif_profil_uncomplete',
            'payment_active',
            'is_payment_possible',
            'is_legal_entity',
        ];

        foreach ($mapping as $apiKey => $setter) {
            if (array_key_exists($apiKey, $data)) {
                $value = $data[$apiKey];
                if ('id' === $apiKey && null !== $value) {
                    $value = (int) $value;
                }
                if (in_array($apiKey, $boolFields, true) && null !== $value) {
                    $value = (bool) $value;
                }
                $user->$setter($value);
            }
        }

        return $user;
    }
}
