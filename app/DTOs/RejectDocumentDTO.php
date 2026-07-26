<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\DocumentApproval;
use App\Models\User;

readonly class RejectDocumentDTO
{
    public function __construct(
        public DocumentApproval $approval,
        public User $approver,
        public string $reason,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            approval: $data["approval"],
            approver: $data["approver"],
            reason: $data["reason"],
        );
    }
}

