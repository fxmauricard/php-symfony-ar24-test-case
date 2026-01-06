<?php

namespace App\Tests\Infrastructure\Ar24\Security;

use App\Infrastructure\Ar24\Security\ResponseDecrypter;
use Exception;
use PHPUnit\Framework\TestCase;

final class ResponseDecrypterTest extends TestCase
{
    private const PRIVATE_KEY = '7X9gx9E3Qx4EiUdB63nc';
    private const DATE = '2021-05-26 14:00:00';
    private const ENCRYPTED_RESPONSE = 'WwBOU6s8DaMWmYdctBJwfuoujFgVygBUjhsbdf8eWqQ=';
    private const EXPECTED_DECRYPTED = '{status: "SUCCESS"}';

    public function testDecryptResponseMatchesDocumentationExample(): void
    {
        $decrypter = new ResponseDecrypter(self::PRIVATE_KEY);
        $decrypted = $decrypter->decryptResponse(self::ENCRYPTED_RESPONSE, self::DATE);

        $this->assertSame(self::EXPECTED_DECRYPTED, $decrypted);
    }

    public function testDecryptResponseThrowsExceptionOnFailure(): void
    {
        $decrypter = new ResponseDecrypter('wrong-key');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to decrypt AR24 response.');

        $decrypter->decryptResponse(self::ENCRYPTED_RESPONSE, self::DATE);
    }
}
