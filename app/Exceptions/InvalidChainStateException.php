<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a blockchain entity is in an illegal state for the
 * requested operation (e.g. trying to execute a non-locked escrow).
 */
class InvalidChainStateException extends RuntimeException
{
    public function __construct(string $message = 'Invalid blockchain/chain state for this operation.', int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
