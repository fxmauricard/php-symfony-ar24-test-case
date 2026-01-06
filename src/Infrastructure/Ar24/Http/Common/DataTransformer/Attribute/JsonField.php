<?php

namespace App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute;

use Attribute;

/**
 * Attribute for mapping a property field to a JSON field.
 *
 * Type detection is performed automatically via property type hints.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class JsonField
{
    /**
     * @param string|null $name JSON field name (if different from property name)
     * @param string $dateFormat Format for DateTimeImmutable (default: 'Y-m-d H:i:s')
     * @param bool $skipNull Do not include the field if null during transform
     */
    public function __construct(
        public ?string $name = null,
        public string $dateFormat = 'Y-m-d H:i:s',
        public bool $skipNull = false,
    ) {
    }
}
