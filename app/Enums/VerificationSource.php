<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Sumber/asal verifikasi QR.
 */
enum VerificationSource: string
{
    case Public  = 'public';
    case Admin   = 'admin';
    case Api     = 'api';
    case Mobile  = 'mobile';
    case System  = 'system';
}
