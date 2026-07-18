<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "current_password" => "required",
            "password"         => "required|string|min:8|confirmed",
        ];
    }

    public function messages(): array
    {
        return [
            "current_password.required" => "Password saat ini harus diisi.",
            "password.required"         => "Password baru harus diisi.",
            "password.min"              => "Password minimal 8 karakter.",
            "password.confirmed"        => "Konfirmasi password tidak cocok.",
        ];
    }
}