<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by the service layer when a business rule is violated
 * (e.g. booking an already-booked slot). Carries an HTTP status so
 * the exception handler can render a clean JSON response.
 */
class BusinessRuleException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
