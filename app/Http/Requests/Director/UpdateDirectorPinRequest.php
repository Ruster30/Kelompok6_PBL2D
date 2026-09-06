<?php

declare(strict_types=1);

namespace App\Http\Requests\Director;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDirectorPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "current_pin" => "required|digits:6",
            "pin"         => "required|digits:6|confirmed",
        ];
    }

    public function messages(): array
    {
        return [
            "current_pin.required" => "PIN lama wajib diisi.",
            "current_pin.digits"   => "PIN lama harus tepat 6 digit angka.",
            "pin.required"         => "PIN baru wajib diisi.",
            "pin.digits"           => "PIN baru harus tepat 6 digit angka.",
            "pin.confirmed"        => "Konfirmasi PIN baru tidak cocok.",
        ];
    }
}