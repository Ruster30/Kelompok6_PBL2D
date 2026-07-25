<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * QrVerificationNotFoundException
 *
 * Domain: QR
 * Digunakan ketika: Token verifikasi tidak ditemukan.
 */
class QrVerificationNotFoundException extends DDMSException
{
    public function __construct(
        string $message = 'Token verifikasi tidak ditemukan.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
