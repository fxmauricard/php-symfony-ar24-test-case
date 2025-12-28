<?php

namespace App\Infrastructure\Security;

use App\Infrastructure\Http\Client\Exception\Ar24ApiException;
use Symfony\Component\String\UnicodeString;

/**
 * Decrypts responses from the AR24 API.
 */
final readonly class Ar24ResponseDecrypter
{
    /**
     * Constructor.
     *
     * @param string $privateKey The private key used for decryption.
     */
    public function __construct(
        private string $privateKey,
    ) {}

    /**
     * Decrypts the AR24 response.
     *
     * @param string $encrypted Base64-encoded encrypted value.
     * @param string $date      Date provided by the API used to derive the key.
     *
     * @return string Decrypted plaintext.
     *
     * @throws Ar24ApiException If decryption fails.
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
            throw new Ar24ApiException('invalid_response', 'Unable to decrypt AR24 response.');
        }

        return $decrypted;
    }
}
