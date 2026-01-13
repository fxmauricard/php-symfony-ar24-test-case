<?php

namespace App\Infrastructure\Ar24\Http\Common\DataTransformer;

use App\Infrastructure\Ar24\Http\Common\DataTransformer\Attribute\JsonField;
use DateTimeImmutable;

/**
 * Automatic transformer for converting between objects and arrays using PHP 8.4 attributes.
 *
 * This class provides bidirectional transformation:
 * - `transform()`: converts an object to a JSON-compatible array
 * - `reverseTransform()`: converts an array back to a typed object
 *
 * The transformer automatically handles type conversion based on property type hints:
 * - Scalar types: int, float, string, bool
 * - Complex types: DateTimeImmutable (with custom format support)
 * - Backed enums: automatically converts between enum instances and their underlying values
 * - Nullable types: automatically handles null values
 *
 * Properties of any visibility (public, protected, private) can be marked with the #[JsonField] attribute.
 */
final class AutomaticTransformer
{
    /**
     * Transforms an object to a JSON-compatible array using attributes and detecting types.
     *
     * This method iterates over all properties marked with the #[JsonField] attribute
     * and converts them to their JSON representation:
     * - Boolean values are converted to integers (0 or 1)
     * - DateTimeImmutable values are formatted as strings
     * - Other types are returned as-is
     * - Null values are included unless skipNull is true
     *
     * Properties are accessed directly via reflection, no getters required.
     *
     * @param object $object The object to transform
     *
     * @return array Associative array with JSON field names as keys
     */
    public function transform(object $object): array
    {
        // Create a reflection object to inspect the target object's structure
        $reflection = new \ReflectionClass($object);
        $result = [];

        // Iterate through all properties (public, protected, and private) defined on the object
        foreach ($reflection->getProperties() as $property) {
            // Check if the property has the #[JsonField] attribute
            $attributes = $property->getAttributes(JsonField::class);
            if (empty($attributes)) {
                // Skip properties without the attribute - they won't be transformed
                continue;
            }

            // Extract and instantiate the attribute to access its configuration
            /** @var JsonField $attribute */
            $attribute = $attributes[0]->newInstance();

            // Determine the JSON field name: use custom name if provided, otherwise use property name
            $jsonKey = $attribute->name ?? $property->getName();

            // Get the property value directly via reflection (no getter needed)
            $value = $property->getValue($object);

            // Skip null values if skipNull flag is set in the attribute
            if ($attribute->skipNull && null === $value) {
                continue;
            }

            // Convert the value to its JSON representation (handles type conversions)
            $result[$jsonKey] = $this->convertValueForJson($value, $attribute, $property);
        }

        return $result;
    }

    /**
     * Transforms an array to a typed object using attributes and detecting types.
     *
     * This method creates a new instance of the specified class and populates its
     * properties from the input array. Type conversion is performed automatically:
     * - String values are parsed according to the property's type hint
     * - DateTimeImmutable values are created from string using the specified format
     * - Boolean conversion: empty strings, '0', 0, false, and null are false; everything else is true
     * - Int/float conversion: uses standard PHP casting
     * - Null values are preserved for nullable properties
     *
     * Properties are set directly via reflection, no setters required.
     *
     * @param array  $data      Input array with JSON field names as keys
     * @param string $className Fully qualified class name to instantiate
     *
     * @return object Typed instance of the requested class
     */
    public function reverseTransform(array $data, string $className): object
    {
        // Create a new instance of the target class
        $object = new $className();

        // Create a reflection object to inspect the target class structure
        $reflection = new \ReflectionClass($object);

        // Iterate through all properties (public, protected, and private) defined on the class
        foreach ($reflection->getProperties() as $property) {
            // Check if the property has the #[JsonField] attribute
            $attributes = $property->getAttributes(JsonField::class);
            if (empty($attributes)) {
                // Skip properties without the attribute - they won't be populated
                continue;
            }

            // Extract and instantiate the attribute to access its configuration
            /** @var JsonField $attribute */
            $attribute = $attributes[0]->newInstance();

            // Determine the JSON field name: use custom name if provided, otherwise use property name
            $jsonKey = $attribute->name ?? $property->getName();

            // Skip if the JSON key doesn't exist in the input data
            if (!array_key_exists($jsonKey, $data)) {
                continue;
            }

            // Get the raw value from the JSON data
            $value = $data[$jsonKey];

            // Convert the JSON value to its proper type based on the property's type hint
            $convertedValue = $this->convertValueFromJson(
                $value,
                $property,
                $attribute
            );

            // Set the property value directly via reflection (no setter needed)
            $property->setValue($object, $convertedValue);
        }

        return $object;
    }

    /**
     * Converts a value from object to JSON by detecting the property type.
     *
     * Conversion rules:
     * - null: returns null
     * - bool: converted to int (0 or 1) for JSON compatibility
     * - DateTimeImmutable: formatted as string using the configured dateFormat
     * - backed enum: converted to its underlying value (string or int)
     * - other types: returned as-is
     *
     * @param mixed               $value     The value to convert
     * @param JsonField           $attribute The attribute configuration
     * @param \ReflectionProperty $property  The property reflection for type information
     *
     * @return mixed JSON-compatible value
     */
    private function convertValueForJson(
        mixed $value,
        JsonField $attribute,
        \ReflectionProperty $property,
    ): mixed {
        // Return null values as-is without conversion
        if (null === $value) {
            return null;
        }

        // Handle backed enums by extracting their underlying value
        // This must be checked before type matching since enums have specific class names
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        // Extract the property's type hint for conversion logic
        $type = $this->getPropertyType($property);

        // Convert based on the detected type
        return match ($type) {
            // Boolean values must be converted to integers (0 or 1) for JSON compatibility
            // Most APIs expect boolean values as integers
            'bool' => (int) $value,

            // DateTimeImmutable objects must be formatted as strings for JSON output
            // Uses the configured format from the attribute (defaults to 'Y-m-d H:i:s')
            'DateTimeImmutable' => $value->format($attribute->dateFormat),

            // All other types (int, string, float, etc.) are passed through as-is
            default => $value,
        };
    }

    /**
     * Converts a value from JSON to a typed object property.
     *
     * Conversion rules based on the property type hint:
     * - null: returns null (for nullable properties)
     * - int: casts using (int) operator
     * - float: casts using (float) operator
     * - string: casts using (string) operator
     * - bool: casts using (bool) operator
     * - DateTimeImmutable: parses using the configured dateFormat
     * - backed enum: creates enum instance using tryFrom() method
     * - other types: returned as-is
     *
     * @param mixed               $value     The value from JSON data
     * @param \ReflectionProperty $property  The property reflection for type information
     * @param JsonField           $attribute The attribute configuration
     *
     * @return mixed Typed value ready for the object property
     */
    private function convertValueFromJson(
        mixed $value,
        \ReflectionProperty $property,
        JsonField $attribute,
    ): mixed {
        // Return null values as-is without conversion
        // This preserves null values for optional/nullable properties
        if (null === $value) {
            return null;
        }

        // Extract the property's type hint to determine conversion strategy
        $type = $this->getPropertyType($property);

        // Check if the type is a backed enum and handle conversion
        if (null !== $type && enum_exists($type)) {
            // Use tryFrom() to safely convert the value to an enum instance
            // Returns null if the value doesn't match any enum case
            // @phpstan-ignore-next-line - $type is a valid enum class name string
            return $type::tryFrom($value);
        }

        // Convert the JSON value to the appropriate PHP type
        return match ($type) {
            // Integer conversion: cast string/numeric values to int
            // Handles cases where API sends "123" but property expects int 123
            'int' => (int) $value,

            // Boolean conversion: uses PHP's standard bool casting rules
            // "0", 0, "", false, and null are false; everything else is true
            'bool' => (bool) $value,

            // Float conversion: cast string/numeric values to float
            // Preserves decimal precision from API responses
            'float' => (float) $value,

            // String conversion: ensures value is always a string
            // Handles numeric or other types being converted to string
            'string' => (string) $value,

            // DateTimeImmutable parsing: converts ISO8601 or formatted date strings
            // Uses the dateFormat from the attribute to parse the date correctly
            'DateTimeImmutable' => self::parseDate($value, $attribute->dateFormat),

            // Unknown/custom types: return value as-is
            // Allows flexibility for user-defined types or special handling
            default => $value,
        };
    }

    /**
     * Extracts the type of a property from its signature.
     *
     * This method handles:
     * - Simple named types: int, string, float, bool, DateTimeImmutable, ClassName, etc.
     * - Union types: int|float, string|null, etc. (returns the first non-null type, prioritizing non-builtin types)
     * - Nullable types: ?int is equivalent to int|null
     *
     * @param \ReflectionProperty $property The property to analyze
     *
     * @return ?string The type name or null if no type hint is defined
     */
    private function getPropertyType(\ReflectionProperty $property): ?string
    {
        // Check if the property has a type hint defined
        // If no type hint exists, we can't perform type conversion
        if (!$property->hasType()) {
            return null;
        }

        // Get the type information from the property's type hint
        $type = $property->getType();

        // Handle simple named types (e.g., int, string, DateTimeImmutable, MyClass)
        // These are straightforward: the property has exactly one type
        if ($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }

        // Handle union types (e.g., int|float, string|null, ?int which is int|null)
        // Union types can have multiple possible types separated by |
        if ($type instanceof \ReflectionUnionType) {
            // First pass: prioritize non-builtin types (custom classes like DateTimeImmutable)
            // This ensures DateTime properties are recognized correctly before fallback to builtin types
            foreach ($type->getTypes() as $t) {
                if ($t instanceof \ReflectionNamedType && !$t->isBuiltin() && 'null' !== $t->getName()) {
                    return $t->getName();
                }
            }

            // Second pass: return the first non-null builtin type (int, string, bool, float, etc.)
            // This handles nullable types like ?int (which is int|null), preferring int over null
            foreach ($type->getTypes() as $t) {
                if ($t instanceof \ReflectionNamedType && 'null' !== $t->getName()) {
                    return $t->getName();
                }
            }
        }

        // No type information could be extracted
        return null;
    }

    /**
     * Parses a date according to the specified format.
     *
     * Uses DateTimeImmutable::createFromFormat() to parse the date string.
     * Returns null if the value is empty, not a string, or parsing fails.
     *
     * @param mixed  $value  The value to parse (typically a string from JSON)
     * @param string $format The date format (e.g., 'Y-m-d H:i:s')
     *
     * @return ?\DateTimeImmutable The parsed DateTimeImmutable or null if parsing fails
     */
    private static function parseDate(mixed $value, string $format): ?\DateTimeImmutable
    {
        // Validate that we have a non-empty string to parse
        // Return null for empty strings, non-strings, or null values
        if (!is_string($value) || '' === $value) {
            return null;
        }

        // Attempt to parse the date string using the specified format
        // DateTimeImmutable::createFromFormat() returns false if parsing fails
        $parsed = \DateTimeImmutable::createFromFormat($format, $value);

        // Return the parsed DateTimeImmutable, or null if parsing failed
        // This prevents returning false (which would be unexpected type)
        return $parsed ?: null;
    }
}
