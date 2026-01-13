<?php

namespace App\Infrastructure\Ar24\Http\Client\Exception;

use Exception;

/**
 * Base exception for AR24 API errors.
 */
class ApiException extends \Exception
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly string $name,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * Get the name of the error.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
