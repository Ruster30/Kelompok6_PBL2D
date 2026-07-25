<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\UpdateSettingDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key'   => 'required|string|max:255',
            'value' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'key.required'   => 'Key setting wajib diisi.',
            'key.max'        => 'Key setting maksimal 255 karakter.',
            'value.required' => 'Nilai setting wajib diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'key'   => 'key setting',
            'value' => 'nilai setting',
        ];
    }

    public function toDTO(): UpdateSettingDTO
    {
        return UpdateSettingDTO::fromArray($this->validated());
    }
}
