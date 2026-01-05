<?php

namespace App\Tests\Infrastructure\Http\RegisteredMail\DataTransformer;

use App\Infrastructure\Http\RegisteredMail\DataTransformer\Ar24RegisteredMailDataTransformer;
use App\Infrastructure\Http\RegisteredMail\Model\Ar24AttachmentDetail;
use App\Infrastructure\Http\RegisteredMail\Model\Ar24RecipientUpdate;
use App\Infrastructure\Http\RegisteredMail\Model\Ar24RegisteredMail;
use App\Infrastructure\Http\Common\DataTransformer\AutomaticTransformer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class Ar24RegisteredMailDataTransformerTest extends TestCase
{
    private Ar24RegisteredMailDataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new Ar24RegisteredMailDataTransformer(new AutomaticTransformer());
    }

    public function testTransformAndReverse(): void
    {
        $attachment = new Ar24AttachmentDetail(
            id: 51,
            name: 'file.pdf',
            hashSha1: 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
            fileSize: 8515845,
            humanFileSize: '8.5 Mb',
            downloadUrl: 'https://sandbox.ar24.fr//get/att/a94a...'
        );

        $update = new Ar24RecipientUpdate(
            id: 5,
            date: new DateTimeImmutable('2017-11-18 18:13:01'),
            lastname: 'Dupont',
            firstname: 'Marie',
            company: 'Marie Corp'
        );

        $mail = new Ar24RegisteredMail(
            id: 123,
            type: 'lre',
            status: 'waiting',
            attachments: [51],
            fromName: 'Dupont Marie',
            fromEmail: 'marie.dupont@example.com',
            address1: '13 rue du Moulin',
            address2: '',
            city: 'PARIS',
            zipcode: '75000',
            toName: 'Doe Corp John Doe',
            toFirstname: 'John',
            toLastname: 'Doe',
            toCompany: 'Doe Corp',
            toEmail: 'john.doe@example.com',
            destStatut: 'professionnel',
            idSender: 123,
            idCreator: 123,
            priceHt: 0,
            paymentSlug: 'stripe-1234',
            refDossier: 'AAAA',
            refClient: '111',
            refFacturation: 'BBB',
            date: new DateTimeImmutable('2017-11-15 18:13:01'),
            fullHashSha256: '9F86D081884C7D659A2FEAA0C55AD015A3BF4F1B2B0B822CD15D6C15B0F00A08',
            sendFail: false,
            isEidas: false,
            proofEvUrl: 'https://sandbox.ar24.fr/get/proof/ev-123?token=1111111111111111',
            tsEvDate: new DateTimeImmutable('2017-11-15 18:15:01'),
            proofArUrl: 'https://sandbox.ar24.fr/get/proof/ar-123?token=1111111111111111',
            viewDate: new DateTimeImmutable('2017-11-15 19:15:01'),
            proofNgUrl: 'https://sandbox.ar24.fr/get/proof/ng-123?token=1111111111111111',
            negligenceDate: new DateTimeImmutable('2017-11-30 19:15:01'),
            proofRfUrl: 'https://sandbox.ar24.fr/get/proof/rf-123?token=1111111111111111',
            refusedDate: new DateTimeImmutable('2017-11-18 19:15:01'),
            proofBcUrl: 'https://sandbox.ar24.fr/get/proof/bc-123?token=1111111111111111',
            bouncedDate: new DateTimeImmutable('2017-11-15 18:15:01'),
            pdfContent: 'https://sandbox.ar24.fr/get/content/123?token=1111111111111111',
            zip: 'https://sandbox.ar24.fr/get/zip/123?token=1111111111111111',
            reqNotifyEv: true,
            reqNotifyAr: true,
            reqNotifyRf: true,
            reqNotifyNg: true,
            attachmentsDetails: [$attachment],
            updates: [$update],
        );

        $array = $this->transformer->transform($mail);

        $this->assertSame(123, $array['id']);
        $this->assertSame('lre', $array['type']);
        $this->assertSame('waiting', $array['status']);
        $this->assertSame('Dupont Marie', $array['from_name']);
        $this->assertSame('marie.dupont@example.com', $array['from_email']);
        $this->assertSame('13 rue du Moulin', $array['address1']);
        $this->assertSame('', $array['address2']);
        $this->assertSame('PARIS', $array['city']);
        $this->assertSame('75000', $array['zipcode']);
        $this->assertSame('Doe Corp John Doe', $array['to_name']);
        $this->assertSame('John', $array['to_firstname']);
        $this->assertSame('Doe', $array['to_lastname']);
        $this->assertSame('Doe Corp', $array['to_company']);
        $this->assertSame('john.doe@example.com', $array['to_email']);
        $this->assertSame('professionnel', $array['dest_statut']);
        $this->assertSame(123, $array['id_sender']);
        $this->assertSame(123, $array['id_creator']);
        $this->assertSame(0, $array['price_ht']);
        $this->assertSame('stripe-1234', $array['payment_slug']);
        $this->assertSame('AAAA', $array['ref_dossier']);
        $this->assertSame('111', $array['ref_client']);
        $this->assertSame('BBB', $array['ref_facturation']);
        $this->assertSame('2017-11-15 18:13:01', $array['date']);
        $this->assertSame('9F86D081884C7D659A2FEAA0C55AD015A3BF4F1B2B0B822CD15D6C15B0F00A08', $array['full_hash_sha256']);
        $this->assertSame(0, $array['send_fail']);
        $this->assertSame(0, $array['is_eidas']);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ev-123?token=1111111111111111', $array['proof_ev_url']);
        $this->assertSame('2017-11-15 18:15:01', $array['ts_ev_date']);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ar-123?token=1111111111111111', $array['proof_ar_url']);
        $this->assertSame('2017-11-15 19:15:01', $array['view_date']);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ng-123?token=1111111111111111', $array['proof_ng_url']);
        $this->assertSame('2017-11-30 19:15:01', $array['negligence_date']);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/rf-123?token=1111111111111111', $array['proof_rf_url']);
        $this->assertSame('2017-11-18 19:15:01', $array['refused_date']);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/bc-123?token=1111111111111111', $array['proof_bc_url']);
        $this->assertSame('2017-11-15 18:15:01', $array['bounced_date']);
        $this->assertSame('https://sandbox.ar24.fr/get/content/123?token=1111111111111111', $array['pdf_content']);
        $this->assertSame('https://sandbox.ar24.fr/get/zip/123?token=1111111111111111', $array['zip']);
        $this->assertSame(1, $array['req_notify_ev']);
        $this->assertSame(1, $array['req_notify_ar']);
        $this->assertSame(1, $array['req_notify_rf']);
        $this->assertSame(1, $array['req_notify_ng']);
        $this->assertSame([51], $array['attachments']);
        $this->assertCount(1, $array['attachments_details']);
        $this->assertCount(1, $array['update']);

        $rebuilt = $this->transformer->reverseTransform($array);

        $this->assertSame(123, $rebuilt->id);
        $this->assertSame('lre', $rebuilt->type);
        $this->assertSame('waiting', $rebuilt->status);
        $this->assertSame('Dupont Marie', $rebuilt->fromName);
        $this->assertSame('marie.dupont@example.com', $rebuilt->fromEmail);
        $this->assertSame('13 rue du Moulin', $rebuilt->address1);
        $this->assertSame('', $rebuilt->address2);
        $this->assertSame('PARIS', $rebuilt->city);
        $this->assertSame('75000', $rebuilt->zipcode);
        $this->assertSame('Doe Corp John Doe', $rebuilt->toName);
        $this->assertSame('John', $rebuilt->toFirstname);
        $this->assertSame('Doe', $rebuilt->toLastname);
        $this->assertSame('Doe Corp', $rebuilt->toCompany);
        $this->assertSame('john.doe@example.com', $rebuilt->toEmail);
        $this->assertSame('professionnel', $rebuilt->destStatut);
        $this->assertSame(123, $rebuilt->idSender);
        $this->assertSame(123, $rebuilt->idCreator);
        $this->assertSame(0, $rebuilt->priceHt);
        $this->assertSame('stripe-1234', $rebuilt->paymentSlug);
        $this->assertSame('AAAA', $rebuilt->refDossier);
        $this->assertSame('111', $rebuilt->refClient);
        $this->assertSame('BBB', $rebuilt->refFacturation);
        $this->assertSame('2017-11-15 18:13:01', $rebuilt->date?->format('Y-m-d H:i:s'));
        $this->assertSame('9F86D081884C7D659A2FEAA0C55AD015A3BF4F1B2B0B822CD15D6C15B0F00A08', $rebuilt->fullHashSha256);
        $this->assertFalse($rebuilt->sendFail);
        $this->assertFalse($rebuilt->isEidas);
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ev-123?token=1111111111111111', $rebuilt->proofEvUrl);
        $this->assertSame('2017-11-15 18:15:01', $rebuilt->tsEvDate?->format('Y-m-d H:i:s'));
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ar-123?token=1111111111111111', $rebuilt->proofArUrl);
        $this->assertSame('2017-11-15 19:15:01', $rebuilt->viewDate?->format('Y-m-d H:i:s'));
        $this->assertSame('https://sandbox.ar24.fr/get/proof/ng-123?token=1111111111111111', $rebuilt->proofNgUrl);
        $this->assertSame('2017-11-30 19:15:01', $rebuilt->negligenceDate?->format('Y-m-d H:i:s'));
        $this->assertSame('https://sandbox.ar24.fr/get/proof/rf-123?token=1111111111111111', $rebuilt->proofRfUrl);
        $this->assertSame('2017-11-18 19:15:01', $rebuilt->refusedDate?->format('Y-m-d H:i:s'));
        $this->assertSame('https://sandbox.ar24.fr/get/proof/bc-123?token=1111111111111111', $rebuilt->proofBcUrl);
        $this->assertSame('2017-11-15 18:15:01', $rebuilt->bouncedDate?->format('Y-m-d H:i:s'));
        $this->assertSame('https://sandbox.ar24.fr/get/content/123?token=1111111111111111', $rebuilt->pdfContent);
        $this->assertSame('https://sandbox.ar24.fr/get/zip/123?token=1111111111111111', $rebuilt->zip);
        $this->assertTrue($rebuilt->reqNotifyEv);
        $this->assertTrue($rebuilt->reqNotifyAr);
        $this->assertTrue($rebuilt->reqNotifyRf);
        $this->assertTrue($rebuilt->reqNotifyNg);
        $this->assertNotNull($rebuilt->attachmentsDetails);
        $this->assertSame(51, $rebuilt->attachmentsDetails[0]->id);
        $this->assertNotNull($rebuilt->updates);
        $this->assertSame(5, $rebuilt->updates[0]->id);
    }
}
