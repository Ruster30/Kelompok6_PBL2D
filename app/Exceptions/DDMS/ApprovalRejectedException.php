<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * ApprovalRejectedException
 *
 * Domain: Approval
 * Digunakan ketika: Approval sudah ditolak (Rejected).
 */
class ApprovalRejectedException extends DDMSException
{
    public function __construct(
        string $message = 'Approval sudah ditolak (Rejected).',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
