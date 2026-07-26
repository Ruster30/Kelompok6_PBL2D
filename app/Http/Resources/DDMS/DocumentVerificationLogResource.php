<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVerificationLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'verification_id'     => $this->verification_id,
            'verified_at'         => $this->verified_at?->toISOString(),
            'status'              => $this->status,
            'ip_address'          => $this->ip_address,
            'user_agent'          => $this->user_agent,
            'verified_by'         => $this->verified_by,
            'verification_source' => $this->verification_source,
            'created_at'          => $this->created_at?->toISOString(),

            // Relasi
            'qr_verification' => new DocumentQrVerificationResource(
                $this->whenLoaded('documentQrVerification')
            ),
        ];
    }
}
