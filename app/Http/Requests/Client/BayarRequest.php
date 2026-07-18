<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BayarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nominal"          => "required|numeric|min:1",
            "bukti_pembayaran" => "required|file|max:5120|mimes:jpg,jpeg,png,pdf",
        ];
    }
}
