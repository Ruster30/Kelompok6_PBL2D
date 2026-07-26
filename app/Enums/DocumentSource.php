<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Sumber dokumen: hasil upload manual atau hasil generate Document Builder.
 */
enum DocumentSource: string
{
    case Uploaded  = "uploaded";
    case Generated = "generated";

    /**
     * Label untuk tampilan UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Uploaded  => "Uploaded",
            self::Generated => "Generated",
        };
    }

    /**
     * Class badge Bootstrap.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Uploaded  => "bg-secondary",
            self::Generated => "bg-success",
        };
    }
}