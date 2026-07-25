<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * DocumentAlreadyHasQrException
 *
 * Domain: Document
 * Digunakan ketika: Dokumen sudah memiliki QR Verification.
 */
class DocumentAlreadyHasQrException extends DDMSException
{
    public function __construct(
        string $message = 'Dokumen sudah memiliki QR Verification.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
