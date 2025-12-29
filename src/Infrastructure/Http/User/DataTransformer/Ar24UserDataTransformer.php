<?php

namespace App\Infrastructure\Http\User\DataTransformer;

use App\Infrastructure\Http\User\Model\Ar24User;
use App\Infrastructure\Http\User\Enum\Ar24UserStatut;

/**
 * DataTransformer to convert Ar24User model to array and vice versa.
 */
final class Ar24UserDataTransformer
{
    /**
     * Transforms an Ar24User object into an array (for API request).
     */
    public function transform(Ar24User $user): array
    {
        $statut = $user->getStatut();

        $data = [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'gender' => $user->getGender(),
            'statut' => $statut instanceof Ar24UserStatut ? $statut->value : null,
            'company' => $user->getCompany(),
            'company_siret' => $user->getCompanySiret(),
            'company_tva' => $user->getCompanyTva(),
            'country' => $user->getCountry(),
            'address1' => $user->getAddress1(),
            'address2' => $user->getAddress2(),
            'zipcode' => $user->getZipcode(),
            'city' => $user->getCity(),
            'notif_billing' => null === $user->isNotifBilling() ? null : (int) $user->isNotifBilling(),
            'billing_email' => $user->getBillingEmail(),
            'confirmed' => null === $user->isConfirmed() ? null : (int) $user->isConfirmed(),
            'cgu' => null === $user->hasCgu() ? null : (int) $user->hasCgu(),
            'notify_ev' => null === $user->isNotifyEv() ? null : (int) $user->isNotifyEv(),
            'notify_ar' => null === $user->isNotifyAr() ? null : (int) $user->isNotifyAr(),
            'notify_rf' => null === $user->isNotifyRf() ? null : (int) $user->isNotifyRf(),
            'notify_ng' => null === $user->isNotifyNg() ? null : (int) $user->isNotifyNg(),
            'notify_bc' => null === $user->isNotifyBc() ? null : (int) $user->isNotifyBc(),
            'notify_consent' => null === $user->isNotifyConsent() ? null : (int) $user->isNotifyConsent(),
            'notify_eidas_to_valid' => null === $user->isNotifyEidasToValid() ? null : (int) $user->isNotifyEidasToValid(),
            'notify_recipient_update' => null === $user->isNotifyRecipientUpdate() ? null : (int) $user->isNotifyRecipientUpdate(),
            'notify_waiting_ar_answer' => null === $user->isNotifyWaitingArAnswer() ? null : (int) $user->isNotifyWaitingArAnswer(),
            'is_legal_entity' => null === $user->isLegalEntity() ? null : (int) $user->isLegalEntity(),
            'custom_ref' => $user->getCustomRef(),
        ];

        return array_filter($data, static fn($value) => null !== $value);
    }

    /**
     * Transforms an array into an Ar24User object (from API response).
     * Assumption: API provides string|null for 'statut'.
     */
    public function reverseTransform(array $data): Ar24User
    {
        $user = new Ar24User();

        // Handle statut specially because the model uses an enum and API sends string|null
        if (array_key_exists('statut', $data)) {
            $val = $data['statut'];
            $user->setStatut(is_string($val) ? Ar24UserStatut::tryFrom($val) : null);

            // remove to avoid being set again by the generic mapping below
            unset($data['statut']);
        }

        $mapping = [
            'firstname' => 'setFirstname',
            'lastname' => 'setLastname',
            'email' => 'setEmail',
            'gender' => 'setGender',
            'company' => 'setCompany',
            'company_siret' => 'setCompanySiret',
            'company_tva' => 'setCompanyTva',
            'country' => 'setCountry',
            'address1' => 'setAddress1',
            'address2' => 'setAddress2',
            'zipcode' => 'setZipcode',
            'city' => 'setCity',
            'notif_billing' => 'setNotifBilling',
            'billing_email' => 'setBillingEmail',
            'confirmed' => 'setConfirmed',
            'cgu' => 'setCgu',
            'notify_ev' => 'setNotifyEv',
            'notify_ar' => 'setNotifyAr',
            'notify_rf' => 'setNotifyRf',
            'notify_ng' => 'setNotifyNg',
            'notify_bc' => 'setNotifyBc',
            'notify_consent' => 'setNotifyConsent',
            'notify_eidas_to_valid' => 'setNotifyEidasToValid',
            'notify_recipient_update' => 'setNotifyRecipientUpdate',
            'notify_waiting_ar_answer' => 'setNotifyWaitingArAnswer',
            'is_legal_entity' => 'setIsLegalEntity',
            'custom_ref' => 'setCustomRef',
        ];

        $boolFields = [
            'notif_billing',
            'confirmed',
            'cgu',
            'notify_ev',
            'notify_ar',
            'notify_rf',
            'notify_ng',
            'notify_bc',
            'notify_consent',
            'notify_eidas_to_valid',
            'notify_recipient_update',
            'notify_waiting_ar_answer',
            'is_legal_entity',
        ];

        foreach ($mapping as $apiKey => $setter) {
            if (array_key_exists($apiKey, $data)) {
                $value = $data[$apiKey];
                if (in_array($apiKey, $boolFields) && null !== $value) {
                    $value = (bool) $value;
                }
                $user->$setter($value);
            }
        }

        return $user;
    }
}
