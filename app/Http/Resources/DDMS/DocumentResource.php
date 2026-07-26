<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use App\Http\Resources\DDMS\DocumentApprovalResource;
use App\Http\Resources\DDMS\DocumentNumberingResource;
use App\Http\Resources\DDMS\DocumentQrVerificationResource;
use App\Http\Resources\DDMS\DocumentTemplateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'event_id'          => $this->event_id,
            'user_id'           => $this->user_id,
            'nama_file'         => $this->nama_file,
            'file_path'         => $this->file_path,
            'file_url'          => $this->file_url,
            'tipe'              => $this->tipe,
            'tipe_label'        => $this->tipe_label,
            'status'            => $this->status,
            'document_category' => $this->document_category,
            'current_version'   => $this->current_version,
            'file_size'         => $this->file_size,
            'mime_type'         => $this->mime_type,
            'is_archived'       => $this->is_archived,
            'archived_at'       => $this->archived_at?->toISOString(),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),

            // Relasi
            'template'     => new DocumentTemplateResource(
                $this->whenLoaded('template')
            ),
            'numbering'    => new DocumentNumberingResource(
                $this->whenLoaded('numbering')
            ),
            'approvals'    => DocumentApprovalResource::collection(
                $this->whenLoaded('approvals')
            ),
            'qr_verification' => new DocumentQrVerificationResource(
                $this->whenLoaded('qrVerification')
            ),
        ];
    }
}
