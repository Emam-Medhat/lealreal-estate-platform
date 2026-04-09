<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a user attempts to spend / use the same funds or shares
 * more than once (double-spend attack pattern).
 */
class DoubleSpendingException extends RuntimeException
{
    public function __construct(string $message = 'Double spending attempt detected.', int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
