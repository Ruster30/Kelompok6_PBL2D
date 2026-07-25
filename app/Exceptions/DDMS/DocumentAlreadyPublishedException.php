<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * DocumentAlreadyPublishedException
 *
 * Domain: Document
 * Digunakan ketika: Dokumen sudah diterbitkan (Published).
 */
class DocumentAlreadyPublishedException extends DDMSException
{
    public function __construct(
        string $message = 'Dokumen sudah diterbitkan (Published).',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
