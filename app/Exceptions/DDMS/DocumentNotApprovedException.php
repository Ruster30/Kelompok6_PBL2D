<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * DocumentNotApprovedException
 *
 * Domain: Document
 * Digunakan ketika: Dokumen belum disetujui (Approved).
 */
class DocumentNotApprovedException extends DDMSException
{
    public function __construct(
        string $message = 'Dokumen belum disetujui (Approved).',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
