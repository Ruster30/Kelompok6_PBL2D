<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

use RuntimeException;

/**
 * DDMSException
 *
 * Base class untuk seluruh Domain Exception DDMS.
 * Semua custom exception harus extend class ini, bukan RuntimeException langsung.
 */
class DDMSException extends RuntimeException
{
    public function __construct(
        string $message = 'DDMS Domain Exception.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
