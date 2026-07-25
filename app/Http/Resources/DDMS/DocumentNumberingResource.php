<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentNumberingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'document_id'     => $this->document_id,
            'document_number' => $this->document_number,
            'prefix'          => $this->prefix,
            'year'            => $this->year,
            'sequence_number' => $this->sequence_number,
            'generated_by'    => $this->generated_by,
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),

            // Relasi
            'document' => new DocumentResource(
                $this->whenLoaded('document')
            ),
        ];
    }
}
