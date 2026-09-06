<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KirimPenawaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_surat'   => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
            'uses_ddms'     => 'nullable|boolean',
        ];
    }
}
