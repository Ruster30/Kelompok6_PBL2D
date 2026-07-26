<?php

declare(strict_types=1);

namespace App\Http\Requests\Director;

use Illuminate\Foundation\Http\FormRequest;

class RejectDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "reason" => "required|string|max:1000",
        ];
    }

    public function messages(): array
    {
        return [
            "reason.required" => "Alasan penolakan wajib diisi.",
            "reason.max"      => "Alasan penolakan maksimal 1000 karakter.",
        ];
    }
}