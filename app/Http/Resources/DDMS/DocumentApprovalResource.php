<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentApprovalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'document_id'   => $this->document_id,
            'submitted_by'  => $this->submitted_by,
            'approver_id'   => $this->approver_id,
            'status'        => $this->status,
            'approval_note' => $this->approval_note,
            'submitted_at'  => $this->submitted_at?->toISOString(),
            'reviewed_at'   => $this->reviewed_at?->toISOString(),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),

            // Relasi
            'document' => new DocumentResource(
                $this->whenLoaded('document')
            ),
        ];
    }
}
