<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Tipe dokumen berdasarkan origin module.
 */
enum DocumentType: string
{
    case Proposal  = 'proposal';
    case Kontrak   = 'kontrak';
    case Invoice   = 'invoice';
    case Rab       = 'rab';
    case Laporan   = 'laporan';
    case Kwitansi  = 'kwitansi';
    case Lainnya   = 'lainnya';
}
