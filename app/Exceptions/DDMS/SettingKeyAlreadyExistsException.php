<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * SettingKeyAlreadyExistsException
 *
 * Domain: Setting
 * Digunakan ketika: Setting key sudah ada.
 */
class SettingKeyAlreadyExistsException extends DDMSException
{
    public function __construct(
        string $message = 'Setting key sudah ada.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
