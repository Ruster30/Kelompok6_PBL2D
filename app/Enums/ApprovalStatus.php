<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status approval dokumen.
 */
enum ApprovalStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
