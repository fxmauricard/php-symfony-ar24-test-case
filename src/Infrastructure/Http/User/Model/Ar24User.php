<?php

namespace App\Infrastructure\Http\User\Model;

use App\Infrastructure\Http\User\Enum\Ar24UserStatut;
use DateTimeImmutable;

/**
 * Model representing a user for the AR24 API.
 */
class Ar24User
{
    private ?int $id = null;
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $gender = null;
    private ?Ar24UserStatut $statut = null;
    private ?string $company = null;
    private ?string $companySiret = null;
    private ?string $companyTva = null;
    private ?string $country = null;
    private ?string $address1 = null;
    private ?string $address2 = null;
    private ?string $zipcode = null;
    private ?string $city = null;
    private ?bool $notifBilling = null;
    private ?string $billingEmail = null;
    private ?bool $confirmed = null;
    private ?bool $cgu = null;
    private ?bool $notifyEv = null;
    private ?bool $notifyAr = null;
    private ?bool $notifyRf = null;
    private ?bool $notifyNg = null;
    private ?bool $notifyBc = null;
    private ?bool $notifyConsent = null;
    private ?bool $notifyEidasToValid = null;
    private ?bool $notifyRecipientUpdate = null;
    private ?bool $notifyWaitingArAnswer = null;
    private ?bool $isLegalEntity = null;
    private ?string $customRef = null;
    private ?string $companyActivity = null;
    private ?string $cPhone = null;
    private ?string $cMobile = null;
    private ?bool $notifyDp = null;
    private ?bool $paymentActive = null;
    private ?string $billingTva = null;
    private ?DateTimeImmutable $creation = null;
    private ?bool $notifCgu = null;
    private ?bool $notifProfilUncomplete = null;
    private ?bool $isPaymentPossible = null;

    /**
     * Get the user's first name.
     * AR24 field: firstname (string).
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set the user's first name.
     * AR24 field: firstname (string).
     *
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get the user's last name.
     * AR24 field: lastname (string).
     *
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set the user's last name.
     * AR24 field: lastname (string).
     *
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get the user's email.
     * AR24 field: email (string) — used for notifications and confirmations.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the user's email.
     * AR24 field: email (string).
     *
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the user's gender.
     * AR24 field: gender (string). Free-form according to AR24 implementation.
     *
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * Set the user's gender.
     * AR24 field: gender (string).
     *
     * @param string|null $gender
     * @return $this
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get the user's statut (individual or business).
     * AR24 field: statut (string). Example: "particulier" or "professionnel".
     *
     * @return Ar24UserStatut|null
     */
    public function getStatut(): ?Ar24UserStatut
    {
        return $this->statut;
    }

    /**
     * Set the user's statut.
     * AR24 field: statut (string).
     *
     * @param Ar24UserStatut|null $statut
     * @return $this
     */
    public function setStatut(?Ar24UserStatut $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    /**
     * Get the company name.
     * AR24 field: company (string).
     *
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Set the company name.
     * AR24 field: company (string).
     *
     * @param string|null $company
     * @return $this
     */
    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Get the company's SIRET number.
     * AR24 field: company_siret (string).
     *
     * @return string|null
     */
    public function getCompanySiret(): ?string
    {
        return $this->companySiret;
    }

    /**
     * Set the company's SIRET number.
     * AR24 field: company_siret (string).
     *
     * @param string|null $companySiret
     * @return $this
     */
    public function setCompanySiret(?string $companySiret): self
    {
        $this->companySiret = $companySiret;
        return $this;
    }

    /**
     * Get the VAT number.
     * AR24 field: company_tva (string).
     *
     * @return string|null
     */
    public function getCompanyTva(): ?string
    {
        return $this->companyTva;
    }

    /**
     * Set the VAT number.
     * AR24 field: company_tva (string).
     *
     * @param string|null $companyTva
     * @return $this
     */
    public function setCompanyTva(?string $companyTva): self
    {
        $this->companyTva = $companyTva;
        return $this;
    }

    /**
     * Get the country code (ISO 3166-1 alpha-2).
     * AR24 field: country (string) — e.g. "FR", "DE".
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Set the country code (ISO 3166-1 alpha-2).
     * AR24 field: country (string).
     *
     * @param string|null $country
     * @return $this
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get the first address line.
     * AR24 field: address1 (string).
     *
     * @return string|null
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * Set the first address line.
     * AR24 field: address1 (string).
     *
     * @param string|null $address1
     * @return $this
     */
    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get the second address line.
     * AR24 field: address2 (string).
     *
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * Set the second address line.
     * AR24 field: address2 (string).
     *
     * @param string|null $address2
     * @return $this
     */
    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get the postal code.
     * AR24 field: zipcode (string).
     *
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * Set the postal code.
     * AR24 field: zipcode (string).
     *
     * @param string|null $zipcode
     * @return $this
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * Get the city.
     * AR24 field: city (string).
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Set the city.
     * AR24 field: city (string).
     *
     * @param string|null $city
     * @return $this
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Whether the user wants billing notifications.
     * AR24 field: notif_billing (bool).
     *
     * @return bool|null
     */
    public function isNotifBilling(): ?bool
    {
        return $this->notifBilling;
    }

    /**
     * Enable or disable billing notifications for the user.
     * AR24 field: notif_billing (bool).
     *
     * @param bool|null $notifBilling
     * @return $this
     */
    public function setNotifBilling(?bool $notifBilling): self
    {
        $this->notifBilling = $notifBilling;
        return $this;
    }

    /**
     * Get the billing email.
     * AR24 field: billing_email (string).
     *
     * @return string|null
     */
    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    /**
     * Set the billing email.
     * AR24 field: billing_email (string).
     *
     * @param string|null $billingEmail
     * @return $this
     */
    public function setBillingEmail(?string $billingEmail): self
    {
        $this->billingEmail = $billingEmail;
        return $this;
    }

    /**
     * Whether the user's email is confirmed.
     * AR24 field: confirmed (bool). 0 = not confirmed (default), 1 = confirmed.
     *
     * @return bool|null
     */
    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    /**
     * Set the email confirmation state.
     * AR24 field: confirmed (bool).
     *
     * @param bool|null $confirmed
     * @return $this
     */
    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * Whether the user already accepted the Terms and Conditions on your side.
     * AR24 field: cgu (bool). If not provided or 0, AR24 will send an email asking the user to accept the T&C.
     *
     * @return bool|null
     */
    public function hasCgu(): ?bool
    {
        return $this->cgu;
    }

    /**
     * Set whether the user already accepted the T&C on your side.
     * AR24 field: cgu (bool).
     *
     * @param bool|null $cgu
     * @return $this
     */
    public function setCgu(?bool $cgu): self
    {
        $this->cgu = $cgu;
        return $this;
    }

    /**
     * Whether the user receives "submission and initial presentation" notifications.
     * AR24 field: notify_ev (bool).
     *
     * @return bool|null
     */
    public function isNotifyEv(): ?bool
    {
        return $this->notifyEv;
    }

    /**
     * Enable/disable "submission and initial presentation" notifications.
     * AR24 field: notify_ev (bool).
     *
     * @param bool|null $notifyEv
     * @return $this
     */
    public function setNotifyEv(?bool $notifyEv): self
    {
        $this->notifyEv = $notifyEv;
        return $this;
    }

    /**
     * Whether the user receives reception notifications.
     * AR24 field: notify_ar (bool).
     *
     * @return bool|null
     */
    public function isNotifyAr(): ?bool
    {
        return $this->notifyAr;
    }

    /**
     * Enable/disable reception notifications.
     * AR24 field: notify_ar (bool).
     *
     * @param bool|null $notifyAr
     * @return $this
     */
    public function setNotifyAr(?bool $notifyAr): self
    {
        $this->notifyAr = $notifyAr;
        return $this;
    }

    /**
     * Whether the user receives refusal notifications.
     * AR24 field: notify_rf (bool).
     *
     * @return bool|null
     */
    public function isNotifyRf(): ?bool
    {
        return $this->notifyRf;
    }

    /**
     * Enable/disable refusal notifications.
     * AR24 field: notify_rf (bool).
     *
     * @param bool|null $notifyRf
     * @return $this
     */
    public function setNotifyRf(?bool $notifyRf): self
    {
        $this->notifyRf = $notifyRf;
        return $this;
    }

    /**
     * Whether the user receives negligence notifications.
     * AR24 field: notify_ng (bool).
     *
     * @return bool|null
     */
    public function isNotifyNg(): ?bool
    {
        return $this->notifyNg;
    }

    /**
     * Enable/disable negligence notifications.
     * AR24 field: notify_ng (bool).
     *
     * @param bool|null $notifyNg
     * @return $this
     */
    public function setNotifyNg(?bool $notifyNg): self
    {
        $this->notifyNg = $notifyNg;
        return $this;
    }

    /**
     * Whether the user receives bounce notifications.
     * AR24 field: notify_bc (bool).
     *
     * @return bool|null
     */
    public function isNotifyBc(): ?bool
    {
        return $this->notifyBc;
    }

    /**
     * Enable/disable bounce notifications.
     * AR24 field: notify_bc (bool).
     *
     * @param bool|null $notifyBc
     * @return $this
     */
    public function setNotifyBc(?bool $notifyBc): self
    {
        $this->notifyBc = $notifyBc;
        return $this;
    }

    /**
     * Whether the user receives consent notifications.
     * AR24 field: notify_consent (bool).
     *
     * @return bool|null
     */
    public function isNotifyConsent(): ?bool
    {
        return $this->notifyConsent;
    }

    /**
     * Enable/disable consent notifications.
     * AR24 field: notify_consent (bool).
     *
     * @param bool|null $notifyConsent
     * @return $this
     */
    public function setNotifyConsent(?bool $notifyConsent): self
    {
        $this->notifyConsent = $notifyConsent;
        return $this;
    }

    /**
     * Whether the user receives notifications sent to a validated eIDAS address.
     * AR24 field: notify_eidas_to_valid (bool).
     *
     * @return bool|null
     */
    public function isNotifyEidasToValid(): ?bool
    {
        return $this->notifyEidasToValid;
    }

    /**
     * Enable/disable notifications to validated eIDAS.
     * AR24 field: notify_eidas_to_valid (bool).
     *
     * @param bool|null $notifyEidasToValid
     * @return $this
     */
    public function setNotifyEidasToValid(?bool $notifyEidasToValid): self
    {
        $this->notifyEidasToValid = $notifyEidasToValid;
        return $this;
    }

    /**
     * Whether the user receives recipient update notifications.
     * AR24 field: notify_recipient_update (bool).
     *
     * @return bool|null
     */
    public function isNotifyRecipientUpdate(): ?bool
    {
        return $this->notifyRecipientUpdate;
    }

    /**
     * Enable/disable recipient update notifications.
     * AR24 field: notify_recipient_update (bool).
     *
     * @param bool|null $notifyRecipientUpdate
     * @return $this
     */
    public function setNotifyRecipientUpdate(?bool $notifyRecipientUpdate): self
    {
        $this->notifyRecipientUpdate = $notifyRecipientUpdate;
        return $this;
    }

    /**
     * Whether the user receives a twice-weekly list of waiting submissions.
     * AR24 field: notify_waiting_ar_answer (bool).
     *
     * @return bool|null
     */
    public function isNotifyWaitingArAnswer(): ?bool
    {
        return $this->notifyWaitingArAnswer;
    }

    /**
     * Enable/disable the twice-weekly waiting submissions list.
     * AR24 field: notify_waiting_ar_answer (bool).
     *
     * @param bool|null $notifyWaitingArAnswer
     * @return $this
     */
    public function setNotifyWaitingArAnswer(?bool $notifyWaitingArAnswer): self
    {
        $this->notifyWaitingArAnswer = $notifyWaitingArAnswer;
        return $this;
    }

    /**
     * Whether the entity is a legal entity (use company name as sender for printed letters).
     * AR24 field: is_legal_entity (bool).
     *
     * @return bool|null
     */
    public function isLegalEntity(): ?bool
    {
        return $this->isLegalEntity;
    }

    /**
     * Set whether the entity is a legal entity.
     * AR24 field: is_legal_entity (bool).
     *
     * @param bool|null $isLegalEntity
     * @return $this
     */
    public function setIsLegalEntity(?bool $isLegalEntity): self
    {
        $this->isLegalEntity = $isLegalEntity;
        return $this;
    }

    /**
     * Get the custom reference related to the API account.
     * AR24 field: custom_ref (string).
     *
     * @return string|null
     */
    public function getCustomRef(): ?string
    {
        return $this->customRef;
    }

    /**
     * Set the custom reference related to the API account.
     * AR24 field: custom_ref (string).
     *
     * @param string|null $customRef
     * @return $this
     */
    public function setCustomRef(?string $customRef): self
    {
        $this->customRef = $customRef;
        return $this;
    }

    /**
     * Get the user's ID.
     * AR24 field: id (int).
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the user's ID.
     * AR24 field: id (int).
     *
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the user's full name.
     * AR24 field: name (string).
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the user's full name.
     * AR24 field: name (string).
     *
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the company activity type.
     * AR24 field: company_activity (string).
     *
     * @return string|null
     */
    public function getCompanyActivity(): ?string
    {
        return $this->companyActivity;
    }

    /**
     * Set the company activity type.
     * AR24 field: company_activity (string).
     *
     * @param string|null $companyActivity
     * @return $this
     */
    public function setCompanyActivity(?string $companyActivity): self
    {
        $this->companyActivity = $companyActivity;
        return $this;
    }

    /**
     * Get the contact phone number.
     * AR24 field: c_phone (string).
     *
     * @return string|null
     */
    public function getCPhone(): ?string
    {
        return $this->cPhone;
    }

    /**
     * Set the contact phone number.
     * AR24 field: c_phone (string).
     *
     * @param string|null $cPhone
     * @return $this
     */
    public function setCPhone(?string $cPhone): self
    {
        $this->cPhone = $cPhone;
        return $this;
    }

    /**
     * Get the contact mobile number.
     * AR24 field: c_mobile (string).
     *
     * @return string|null
     */
    public function getCMobile(): ?string
    {
        return $this->cMobile;
    }

    /**
     * Set the contact mobile number.
     * AR24 field: c_mobile (string).
     *
     * @param string|null $cMobile
     * @return $this
     */
    public function setCMobile(?string $cMobile): self
    {
        $this->cMobile = $cMobile;
        return $this;
    }

    /**
     * Whether the user wants document notifications.
     * AR24 field: notify_dp (bool).
     *
     * @return bool|null
     */
    public function isNotifyDp(): ?bool
    {
        return $this->notifyDp;
    }

    /**
     * Enable or disable document notifications for the user.
     * AR24 field: notify_dp (bool).
     *
     * @param bool|null $notifyDp
     * @return $this
     */
    public function setNotifyDp(?bool $notifyDp): self
    {
        $this->notifyDp = $notifyDp;
        return $this;
    }

    /**
     * Whether the payment is active for the user.
     * AR24 field: payment_active (bool).
     *
     * @return bool|null
     */
    public function isPaymentActive(): ?bool
    {
        return $this->paymentActive;
    }

    /**
     * Set the payment active state for the user.
     * AR24 field: payment_active (bool).
     *
     * @param bool|null $paymentActive
     * @return $this
     */
    public function setPaymentActive(?bool $paymentActive): self
    {
        $this->paymentActive = $paymentActive;
        return $this;
    }

    /**
     * Get the billing VAT number.
     * AR24 field: billing_tva (string).
     *
     * @return string|null
     */
    public function getBillingTva(): ?string
    {
        return $this->billingTva;
    }

    /**
     * Set the billing VAT number.
     * AR24 field: billing_tva (string).
     *
     * @param string|null $billingTva
     * @return $this
     */
    public function setBillingTva(?string $billingTva): self
    {
        $this->billingTva = $billingTva;
        return $this;
    }

    /**
     * Get the account creation date.
     * AR24 field: creation (datetime).
     *
     * @return DateTimeImmutable|null
     */
    public function getCreation(): ?DateTimeImmutable
    {
        return $this->creation;
    }

    /**
     * Set the account creation date.
     * AR24 field: creation (datetime).
     *
     * @param DateTimeImmutable|null $creation
     * @return $this
     */
    public function setCreation(?DateTimeImmutable $creation): self
    {
        $this->creation = $creation;
        return $this;
    }

    /**
     * Whether the user wants to be notified about T&C updates.
     * AR24 field: notif_cgu (bool).
     *
     * @return bool|null
     */
    public function isNotifCgu(): ?bool
    {
        return $this->notifCgu;
    }

    /**
     * Enable or disable notifications for T&C updates.
     * AR24 field: notif_cgu (bool).
     *
     * @param bool|null $notifCgu
     * @return $this
     */
    public function setNotifCgu(?bool $notifCgu): self
    {
        $this->notifCgu = $notifCgu;
        return $this;
    }

    /**
     * Whether the user wants to be notified when their profile is incomplete.
     * AR24 field: notif_profil_uncomplete (bool).
     *
     * @return bool|null
     */
    public function isNotifProfilUncomplete(): ?bool
    {
        return $this->notifProfilUncomplete;
    }

    /**
     * Enable or disable notifications for incomplete profile.
     * AR24 field: notif_profil_uncomplete (bool).
     *
     * @param bool|null $notifProfilUncomplete
     * @return $this
     */
    public function setNotifProfilUncomplete(?bool $notifProfilUncomplete): self
    {
        $this->notifProfilUncomplete = $notifProfilUncomplete;
        return $this;
    }

    /**
     * Whether payment is possible for the user.
     * AR24 field: is_payment_possible (bool).
     *
     * @return bool|null
     */
    public function isPaymentPossible(): ?bool
    {
        return $this->isPaymentPossible;
    }

    /**
     * Set whether payment is possible for the user.
     * AR24 field: is_payment_possible (bool).
     *
     * @param bool|null $isPaymentPossible
     * @return $this
     */
    public function setPaymentPossible(?bool $isPaymentPossible): self
    {
        $this->isPaymentPossible = $isPaymentPossible;
        return $this;
    }
}
