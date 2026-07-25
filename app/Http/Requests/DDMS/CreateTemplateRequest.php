<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\CreateTemplateDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'blade_view'  => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama template wajib diisi.',
            'name.max'             => 'Nama template maksimal 255 karakter.',
            'code.max'             => 'Kode template maksimal 50 karakter.',
            'blade_view.required'  => 'Path Blade view wajib diisi.',
            'blade_view.max'       => 'Path Blade view maksimal 255 karakter.',
            'description.max'      => 'Deskripsi maksimal 500 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'nama template',
            'code'        => 'kode template',
            'blade_view'  => 'path Blade view',
            'description' => 'deskripsi template',
            'is_active'   => 'status aktif',
        ];
    }

    public function toDTO(): CreateTemplateDTO
    {
        return CreateTemplateDTO::fromArray($this->validated());
    }
}
