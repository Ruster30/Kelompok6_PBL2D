<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * TemplateCodeAlreadyExistsException
 *
 * Domain: Template
 * Digunakan ketika: Kode template sudah digunakan.
 */
class TemplateCodeAlreadyExistsException extends DDMSException
{
    public function __construct(
        string $message = 'Kode template sudah digunakan.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
