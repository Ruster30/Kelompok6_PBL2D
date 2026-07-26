<?php

declare(strict_types=1);

namespace App\Exceptions\DDMS;

/**
 * ApprovalNotPendingException
 *
 * Domain: Approval
 * Digunakan ketika: Approval sudah diproses. Status bukan Pending.
 */
class ApprovalNotPendingException extends DDMSException
{
    public function __construct(
        string $message = 'Approval sudah diproses. Status bukan Pending.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
