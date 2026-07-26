<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * DocumentAlreadyNumberedException
 *
 * Domain: Document
 * Digunakan ketika: Dokumen sudah memiliki nomor.
 */
class DocumentAlreadyNumberedException extends DDMSException
{
    public function __construct(
        string $message = 'Dokumen sudah memiliki nomor.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
