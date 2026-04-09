<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a share purchase would cause the total sold shares
 * to exceed 100% of the asset's total_shares cap.
 */
class ShareLimitExceededException extends RuntimeException
{
    public function __construct(string $message = 'Share purchase would exceed the 100% ownership limit.', int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
