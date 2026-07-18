<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name"  => "required|string|max:255",
            "email" => [
                "required",
                "email",
                Rule::unique("users", "email")->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "name.required"  => "Nama harus diisi.",
            "email.required" => "Email harus diisi.",
            "email.email"    => "Format email tidak valid.",
            "email.unique"   => "Email sudah digunakan.",
        ];
    }
}