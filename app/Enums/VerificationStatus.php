<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status hasil verifikasi QR.
 */
enum VerificationStatus: string
{
    case Valid    = 'valid';
    case Expired  = 'expired';
    case Invalid  = 'invalid';
    case Tampered = 'tampered';
}
