<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when an escrow cannot be executed because one or more
 * required conditions (e.g. property_inspection, legal_approval) are unmet.
 */
class EscrowConditionException extends RuntimeException
{
    public function __construct(string $message = 'One or more escrow conditions have not been satisfied.', int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
