<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\DocumentApproval;
use App\Models\User;

readonly class ApproveDocumentDTO
{
    public function __construct(
        public DocumentApproval $approval,
        public User $approver,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            approval: $data["approval"],
            approver: $data["approver"],
        );
    }
}

