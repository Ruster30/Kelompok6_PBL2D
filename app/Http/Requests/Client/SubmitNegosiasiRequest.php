<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class SubmitNegosiasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pesan'             => 'required|string|max:2000',
            'budget_diinginkan' => 'nullable|string|max:100',
            'catatan_tambahan'  => 'nullable|string|max:1000',
        ];
    }
}
