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
            'jenis_pembayaran' => 'required|in:dp,pelunasan',
            'nominal'          => 'required|numeric|min:1000',
            'bukti_pembayaran' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ];
    }
}
