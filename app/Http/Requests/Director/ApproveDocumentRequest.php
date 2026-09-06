<?php

declare(strict_types=1);

namespace App\Http\Requests\Director;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "pin" => "required|digits:6",
        ];
    }

    public function messages(): array
    {
        return [
            "pin.required" => "PIN wajib diisi.",
            "pin.digits"   => "PIN harus tepat 6 digit angka.",
        ];
    }
}