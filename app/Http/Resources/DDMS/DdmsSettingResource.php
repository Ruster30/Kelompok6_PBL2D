<?php

declare(strict_types=1);

namespace App\Http\Resources\DDMS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DdmsSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'setting_key'   => $this->setting_key,
            'setting_value' => $this->setting_value,
            'description'   => $this->description,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
