<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentQrVerificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'document_id'       => $this->document_id,
            'verification_token' => $this->verification_token,
            'qr_path'           => $this->qr_path,
            'generated_by'      => $this->generated_by,
            'generated_at'      => $this->generated_at?->toISOString(),
            'expires_at'        => $this->expires_at?->toISOString(),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),

            // Relasi
            'document' => new DocumentResource(
                $this->whenLoaded('document')
            ),
            'verification_logs' => DocumentVerificationLogResource::collection(
                $this->whenLoaded('verificationLogs')
            ),
        ];
    }
}
