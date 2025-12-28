<?php

namespace App\Tests\Infrastructure\Security;

use App\Infrastructure\Security\Ar24ResponseDecrypter;
use Exception;
use PHPUnit\Framework\TestCase;

final class Ar24ResponseDecrypterTest extends TestCase
{
    private const PRIVATE_KEY = '7X9gx9E3Qx4EiUdB63nc';
    private const DATE = '2021-05-26 14:00:00';
    private const ENCRYPTED_RESPONSE = 'WwBOU6s8DaMWmYdctBJwfuoujFgVygBUjhsbdf8eWqQ=';
    private const EXPECTED_DECRYPTED = '{status: "SUCCESS"}';

    public function testDecryptResponseMatchesDocumentationExample(): void
    {
        $decrypter = new Ar24ResponseDecrypter(self::PRIVATE_KEY);
        $decrypted = $decrypter->decryptResponse(self::ENCRYPTED_RESPONSE, self::DATE);

        $this->assertSame(self::EXPECTED_DECRYPTED, $decrypted);
    }

    public function testDecryptResponseThrowsExceptionOnFailure(): void
    {
        $decrypter = new Ar24ResponseDecrypter('wrong-key');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to decrypt AR24 response.');

        $decrypter->decryptResponse(self::ENCRYPTED_RESPONSE, self::DATE);
    }
}
