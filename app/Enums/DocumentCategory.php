<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Kategori dokumen untuk menentukan workflow approval.
 */
enum DocumentCategory: string
{
    case Official = 'official';
    case General  = 'general';
    case Invoice  = 'invoice';
    case Receipt  = 'receipt';
}
