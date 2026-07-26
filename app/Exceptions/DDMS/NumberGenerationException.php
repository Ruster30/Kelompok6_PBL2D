<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * NumberGenerationException
 *
 * Domain: Numbering
 * Digunakan ketika: Gagal membuat nomor dokumen.
 */
class NumberGenerationException extends DDMSException
{
    public function __construct(
        string $message = 'Gagal membuat nomor dokumen.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
