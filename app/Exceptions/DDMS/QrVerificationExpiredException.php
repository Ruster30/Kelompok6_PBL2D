<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * QrVerificationExpiredException
 *
 * Domain: QR
 * Digunakan ketika: QR Code sudah kedaluwarsa.
 */
class QrVerificationExpiredException extends DDMSException
{
    public function __construct(
        string $message = 'QR Code sudah kedaluwarsa.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
