<?php

namespace App\Infrastructure\Ar24\Security;

use Symfony\Component\String\UnicodeString;

/**
 * Factory for generating AR24 API request headers.
 */
final readonly class HeadersFactory
{
    /**
     * Constructor.
     *
     * @param string $privateKey the private key used for signature generation
     */
    public function __construct(
        private string $privateKey,
    ) {
    }

    /**
     * Generates the necessary headers for each AR24 API request.
     *
     * @return array<string, string>
     */
    public function buildHeaders(string $date): array
    {
        $signature = $this->generateSignature($date);

        return [
            'Accept-Language' => 'en',
            'signature' => $signature,
        ];
    }

    /**
     * Generates a cryptographic signature for the provided date using AES-256-CBC encryption.
     *
     * The method hashes the private key using SHA-256, extracts an Initialization Vector (IV)
     * from the first 16 characters of the resulting hash, and encrypts the given date with
     * the hashed key and IV.
     *
     * @param string $date the date to be encrypted and signed
     *
     * @return string the encrypted signature for the provided date
     */
    private function generateSignature(string $date): string
    {
        // Private key hash.
        $hashedPrivateKey = hash('sha256', $this->privateKey);

        // Initialization Vector: The first 16 characters of the hash of the hash.
        $doubleHash = hash('sha256', hash('sha256', $this->privateKey));
        $iv = new UnicodeString($doubleHash)->slice(0, 16)->toString();

        // AES-256-CBC encryption.
        return openssl_encrypt($date, 'aes-256-cbc', $hashedPrivateKey, false, $iv);
    }
}
