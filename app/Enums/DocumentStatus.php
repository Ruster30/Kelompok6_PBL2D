<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status dokumen dalam workflow DDMS.
 */
enum DocumentStatus: string
{
    case Draft     = 'draft';
    case Pending   = 'pending';
    case Approved  = 'approved';
    case Rejected  = 'rejected';
    case Published = 'published';
    case Archived  = 'archived';
}
