<?php

namespace App\Infrastructure\Security;

use Exception;
use Symfony\Component\String\UnicodeString;

final readonly class Ar24ResponseDecrypter
{
    public function __construct(
        private string $privateKey,
    ) {}

    /**
     * @throws Exception
     */
    public function decryptResponse(string $encrypted, string $date): string
    {
        // Hash of the derived key (date + private key).
        $key = hash('sha256', $date . $this->privateKey);

        // Initialization Vector: The first 16 characters of the hash of the hash.
        $doubleHash = hash('sha256', hash('sha256', $this->privateKey));
        $iv = new UnicodeString($doubleHash)->slice(0, 16)->toString();

        // Decrypt the response using AES-256-CBC encryption.
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, false, $iv);

        if (false === $decrypted) {
            throw new Exception('Unable to decrypt AR24 response.');
        }

        return $decrypted;
    }
}
