<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Document;
use App\Models\User;

/**
 * SubmitDocumentDTO
 *
 * Data untuk workflow submit dokumen ke approval.
 */
readonly class SubmitDocumentDTO
{
    public function __construct(
        public Document $document,
        public User $submittedBy,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            document: $data["document"],
            submittedBy: $data["submitted_by"],
        );
    }
}

