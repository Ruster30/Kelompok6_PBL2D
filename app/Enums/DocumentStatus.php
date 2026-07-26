<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status dokumen dalam workflow DDMS.
 */
enum DocumentStatus: string
{
    case Draft     = "draft";
    case Pending   = "pending";
    case Approved  = "approved";
    case Rejected  = "rejected";
    case Published = "published";
    case Archived  = "archived";

    /**
     * Label status untuk tampilan UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => "Draft",
            self::Pending   => "Pending Approval",
            self::Approved  => "Approved",
            self::Rejected  => "Rejected",
            self::Published => "Published",
            self::Archived  => "Archived",
        };
    }

    /**
     * Class badge Bootstrap sesuai status.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Draft     => "badge-mendatang",
            self::Pending   => "badge-pending",
            self::Approved  => "badge-selesai",
            self::Rejected  => "badge-ditolak",
            self::Published => "badge-selesai",
            self::Archived  => "badge-purple",
        };
    }
}
