<?php

namespace App\Tests\Infrastructure\Http\User\DataTransformer;

use App\Infrastructure\Http\User\DataTransformer\Ar24UserDataTransformer;
use App\Infrastructure\Http\User\Model\Ar24User;
use App\Infrastructure\Http\User\Enum\Ar24UserStatut;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Ar24UserDataTransformer.
 */
final class Ar24UserDataTransformerTest extends TestCase
{
    private Ar24UserDataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new Ar24UserDataTransformer();
    }

    public function testTransformWithAllProperties(): void
    {
        $user = new Ar24User()
            ->setFirstname('Hugo')
            ->setLastname('Dupont')
            ->setEmail('example@example.com')
            ->setGender('F')
            ->setStatut(Ar24UserStatut::BUSINESS)
            ->setCompany('ABC SAS')
            ->setCompanySiret('123456')
            ->setCompanyTva('123456')
            ->setCountry('FR')
            ->setAddress1('1 rue de la république')
            ->setAddress2('Batiment B')
            ->setZipcode('75000')
            ->setCity('Paris')
            ->setNotifBilling(true)
            ->setBillingEmail('facturation@example.com')
            ->setConfirmed(false)
            ->setCgu(true)
            ->setNotifyEv(true)
            ->setNotifyAr(false)
            ->setNotifyRf(true)
            ->setNotifyNg(false)
            ->setNotifyBc(true)
            ->setNotifyConsent(false)
            ->setNotifyEidasToValid(true)
            ->setNotifyRecipientUpdate(false)
            ->setNotifyWaitingArAnswer(true)
            ->setIsLegalEntity(false)
            ->setCustomRef('REF-001')
            ->setId(50956)
            ->setName('Hugo Dupont')
            ->setCompanyActivity('')
            ->setCPhone('0123456789')
            ->setCMobile('0612345678')
            ->setNotifyDp(true)
            ->setPaymentActive(false)
            ->setBillingTva('20')
            ->setCreation(new DateTimeImmutable('2019-01-09 10:48:49'))
            ->setNotifCgu(false)
            ->setNotifProfilUncomplete(false)
            ->setPaymentPossible(false);

        $result = $this->transformer->transform($user);

        $this->assertSame('Hugo', $result['firstname']);
        $this->assertSame('Dupont', $result['lastname']);
        $this->assertSame('example@example.com', $result['email']);
        $this->assertSame('F', $result['gender']);
        $this->assertSame(Ar24UserStatut::BUSINESS->value, $result['statut']);
        $this->assertSame('ABC SAS', $result['company']);
        $this->assertSame('123456', $result['company_siret']);
        $this->assertSame('123456', $result['company_tva']);
        $this->assertSame('FR', $result['country']);
        $this->assertSame('1 rue de la république', $result['address1']);
        $this->assertSame('Batiment B', $result['address2']);
        $this->assertSame('75000', $result['zipcode']);
        $this->assertSame('Paris', $result['city']);
        $this->assertSame(1, $result['notif_billing']);
        $this->assertSame('facturation@example.com', $result['billing_email']);
        $this->assertSame(0, $result['confirmed']);
        $this->assertSame(1, $result['cgu']);
        $this->assertSame(1, $result['notify_ev']);
        $this->assertSame(0, $result['notify_ar']);
        $this->assertSame(1, $result['notify_rf']);
        $this->assertSame(0, $result['notify_ng']);
        $this->assertSame(1, $result['notify_bc']);
        $this->assertSame(0, $result['notify_consent']);
        $this->assertSame(1, $result['notify_eidas_to_valid']);
        $this->assertSame(0, $result['notify_recipient_update']);
        $this->assertSame(1, $result['notify_waiting_ar_answer']);
        $this->assertSame(0, $result['is_legal_entity']);
        $this->assertSame('REF-001', $result['custom_ref']);
        $this->assertSame(50956, $result['id']);
        $this->assertSame('Hugo Dupont', $result['name']);
        $this->assertSame('', $result['company_activity']);
        $this->assertSame('0123456789', $result['c_phone']);
        $this->assertSame('0612345678', $result['c_mobile']);
        $this->assertSame(1, $result['notify_dp']);
        $this->assertSame(0, $result['payment_active']);
        $this->assertSame('20', $result['billing_tva']);
        $this->assertSame('2019-01-09 10:48:49', $result['creation']);
        $this->assertSame(0, $result['notif_cgu']);
        $this->assertSame(0, $result['notif_profil_uncomplete']);
        $this->assertSame(0, $result['is_payment_possible']);
    }

    public function testReverseTransformWithAllProperties(): void
    {
        $data = [
            'firstname' => 'Hugo',
            'lastname' => 'Dupont',
            'email' => 'example@example.com',
            'gender' => 'F',
            'statut' => Ar24UserStatut::BUSINESS->value,
            'company' => 'ABC SAS',
            'company_siret' => '123456',
            'company_tva' => '123456',
            'country' => 'FR',
            'address1' => '1 rue de la république',
            'address2' => 'Batiment B',
            'zipcode' => '75000',
            'city' => 'Paris',
            'notif_billing' => 1,
            'billing_email' => 'facturation@example.com',
            'confirmed' => 0,
            'cgu' => 1,
            'notify_ev' => 1,
            'notify_ar' => 0,
            'notify_rf' => 1,
            'notify_ng' => 0,
            'notify_bc' => 1,
            'notify_consent' => 0,
            'notify_eidas_to_valid' => 1,
            'notify_recipient_update' => 0,
            'notify_waiting_ar_answer' => 1,
            'is_legal_entity' => 0,
            'custom_ref' => 'REF-001',
            'id' => 50956,
            'name' => 'Hugo Dupont',
            'company_activity' => '',
            'c_phone' => '0123456789',
            'c_mobile' => '0612345678',
            'notify_dp' => '1',
            'payment_active' => '0',
            'billing_tva' => '20',
            'creation' => '2019-01-09 10:48:49',
            'notif_cgu' => '0',
            'notif_profil_uncomplete' => '0',
            'is_payment_possible' => false,
        ];

        $user = $this->transformer->reverseTransform($data);

        $this->assertSame('Hugo', $user->getFirstname());
        $this->assertSame('Dupont', $user->getLastname());
        $this->assertSame('example@example.com', $user->getEmail());
        $this->assertSame('F', $user->getGender());
        $this->assertSame(Ar24UserStatut::BUSINESS, $user->getStatut());
        $this->assertSame('ABC SAS', $user->getCompany());
        $this->assertSame('123456', $user->getCompanySiret());
        $this->assertSame('123456', $user->getCompanyTva());
        $this->assertSame('FR', $user->getCountry());
        $this->assertSame('1 rue de la république', $user->getAddress1());
        $this->assertSame('Batiment B', $user->getAddress2());
        $this->assertSame('75000', $user->getZipcode());
        $this->assertSame('Paris', $user->getCity());
        $this->assertTrue($user->isNotifBilling());
        $this->assertSame('facturation@example.com', $user->getBillingEmail());
        $this->assertFalse($user->isConfirmed());
        $this->assertTrue($user->hasCgu());
        $this->assertTrue($user->isNotifyEv());
        $this->assertFalse($user->isNotifyAr());
        $this->assertTrue($user->isNotifyRf());
        $this->assertFalse($user->isNotifyNg());
        $this->assertTrue($user->isNotifyBc());
        $this->assertFalse($user->isNotifyConsent());
        $this->assertTrue($user->isNotifyEidasToValid());
        $this->assertFalse($user->isNotifyRecipientUpdate());
        $this->assertTrue($user->isNotifyWaitingArAnswer());
        $this->assertFalse($user->isLegalEntity());
        $this->assertSame('REF-001', $user->getCustomRef());
        $this->assertSame(50956, $user->getId());
        $this->assertSame('Hugo Dupont', $user->getName());
        $this->assertSame('', $user->getCompanyActivity());
        $this->assertSame('0123456789', $user->getCPhone());
        $this->assertSame('0612345678', $user->getCMobile());
        $this->assertTrue($user->isNotifyDp());
        $this->assertFalse($user->isPaymentActive());
        $this->assertSame('20', $user->getBillingTva());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreation());
        $this->assertSame('2019-01-09 10:48:49', $user->getCreation()?->format('Y-m-d H:i:s'));
        $this->assertFalse($user->isNotifCgu());
        $this->assertFalse($user->isNotifProfilUncomplete());
        $this->assertFalse($user->isPaymentPossible());
    }
}
