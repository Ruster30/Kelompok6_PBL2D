<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nomor_surat" => "required|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            "nomor_surat.required" => "Nomor surat wajib diisi.",
            "nomor_surat.string"   => "Nomor surat harus berupa teks.",
            "nomor_surat.max"      => "Nomor surat maksimal 255 karakter.",
        ];
    }
}