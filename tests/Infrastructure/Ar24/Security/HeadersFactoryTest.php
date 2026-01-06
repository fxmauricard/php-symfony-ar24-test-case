<?php

namespace App\Tests\Infrastructure\Ar24\Security;

use App\Infrastructure\Ar24\Security\HeadersFactory;
use PHPUnit\Framework\TestCase;

final class HeadersFactoryTest extends TestCase
{
    private const PRIVATE_KEY = '7X9gx9E3Qx4EiUdB63nc';
    private const DATE = '2021-05-26 14:00:00';
    private const EXPECTED_SIGNATURE = 'bDop0cbjKpkySlpvnNGvBMg7PuYFFgPPqTTS2RAHoY0=';

    public function testBuildHeadersReturnsCorrectStructure(): void
    {
        $factory = new HeadersFactory(self::PRIVATE_KEY);
        $headers = $factory->buildHeaders(self::DATE);

        $this->assertArrayHasKey('Accept-Language', $headers);
        $this->assertArrayHasKey('signature', $headers);
        $this->assertSame('en', $headers['Accept-Language']);
        $this->assertSame(self::EXPECTED_SIGNATURE, $headers['signature']);
    }

    public function testSignatureGenerationMatchesDocumentationExample(): void
    {
        $factory = new HeadersFactory(self::PRIVATE_KEY);
        
        // Since generateSignature is private, we test it through buildHeaders
        $headers = $factory->buildHeaders(self::DATE);
        
        $this->assertSame(self::EXPECTED_SIGNATURE, $headers['signature']);
    }
}
