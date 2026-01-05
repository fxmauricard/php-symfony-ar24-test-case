<?php

namespace App\Tests\Infrastructure\Http\Common\DataTransformer;

use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use App\Infrastructure\Http\User\Enum\Ar24UserStatut;
use App\Infrastructure\Http\User\Model\Ar24User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Ar24UserDataTransformer.
 */
final class AutomaticTransformerAr24UserTest extends TestCase
{
    private AutomaticTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new AutomaticTransformer();
    }

    public function testTransformWithAllProperties(): void
    {
        $creation = new DateTimeImmutable('2019-01-09 10:48:49');
        $user = new Ar24User(
            id: 50956,
            firstname: 'Hugo',
            lastname: 'Dupont',
            name: 'Hugo Dupont',
            email: 'example@example.com',
            gender: 'F',
            statut: Ar24UserStatut::BUSINESS,
            company: 'ABC SAS',
            companySiret: '123456',
            companyTva: '123456',
            country: 'FR',
            address1: '1 rue de la république',
            address2: 'Batiment B',
            zipcode: '75000',
            city: 'Paris',
            notifBilling: true,
            billingEmail: 'facturation@example.com',
            confirmed: true,
            cgu: false,
            notifyEv: true,
            notifyAr: false,
            notifyRf: true,
            notifyNg: false,
            notifyBc: true,
            notifyConsent: false,
            notifyEidasToValid: true,
            notifyRecipientUpdate: false,
            notifyWaitingArAnswer: true,
            isLegalEntity: false,
            customRef: 'REF-001',
            companyActivity: '',
            cPhone: '0123456789',
            cMobile: '0612345678',
            notifyDp: true,
            paymentActive: false,
            billingTva: '20',
            creation: $creation,
            notifCgu: false,
            notifProfilUncomplete: false,
            isPaymentPossible: false
        );

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
        $this->assertSame(1, $result['confirmed']);
        $this->assertSame(0, $result['cgu']);
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

        $user = $this->transformer->reverseTransform($data, Ar24User::class);

        $this->assertSame('Hugo', $user->firstname);
        $this->assertSame('Dupont', $user->lastname);
        $this->assertSame('example@example.com', $user->email);
        $this->assertSame('F', $user->gender);
        $this->assertSame(Ar24UserStatut::BUSINESS, $user->statut);
        $this->assertSame('ABC SAS', $user->company);
        $this->assertSame('123456', $user->companySiret);
        $this->assertSame('123456', $user->companyTva);
        $this->assertSame('FR', $user->country);
        $this->assertSame('1 rue de la république', $user->address1);
        $this->assertSame('Batiment B', $user->address2);
        $this->assertSame('75000', $user->zipcode);
        $this->assertSame('Paris', $user->city);
        $this->assertTrue($user->notifBilling);
        $this->assertSame('facturation@example.com', $user->billingEmail);
        $this->assertFalse($user->confirmed);
        $this->assertTrue($user->cgu);
        $this->assertTrue($user->notifyEv);
        $this->assertFalse($user->notifyAr);
        $this->assertTrue($user->notifyRf);
        $this->assertFalse($user->notifyNg);
        $this->assertTrue($user->notifyBc);
        $this->assertFalse($user->notifyConsent);
        $this->assertTrue($user->notifyEidasToValid);
        $this->assertFalse($user->notifyRecipientUpdate);
        $this->assertTrue($user->notifyWaitingArAnswer);
        $this->assertFalse($user->isLegalEntity);
        $this->assertSame('REF-001', $user->customRef);
        $this->assertSame(50956, $user->id);
        $this->assertSame('Hugo Dupont', $user->name);
        $this->assertSame('', $user->companyActivity);
        $this->assertSame('0123456789', $user->cPhone);
        $this->assertSame('0612345678', $user->cMobile);
        $this->assertTrue($user->notifyDp);
        $this->assertFalse($user->paymentActive);
        $this->assertSame('20', $user->billingTva);
        $this->assertInstanceOf(DateTimeImmutable::class, $user->creation);
        $this->assertSame('2019-01-09 10:48:49', $user->creation?->format('Y-m-d H:i:s'));
        $this->assertFalse($user->notifCgu);
        $this->assertFalse($user->notifProfilUncomplete);
        $this->assertFalse($user->isPaymentPossible);
    }
}
