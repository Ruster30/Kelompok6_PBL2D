<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRabAdditionalDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fee_enabled'  => 'boolean',
            'fee_percent'  => 'required|numeric|min:0|max:100',
            'ppn_enabled'  => 'boolean',
            'ppn_percent'  => 'required|numeric|min:0|max:100',
            'pph_enabled'  => 'boolean',
            'pph_percent'  => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'fee_percent.required' => 'Persentase Fee EO harus diisi.',
            'fee_percent.numeric'  => 'Persentase Fee EO harus angka.',
            'fee_percent.max'      => 'Persentase Fee EO maksimal 100.',
            'ppn_percent.required' => 'Persentase PPN harus diisi.',
            'ppn_percent.numeric'  => 'Persentase PPN harus angka.',
            'ppn_percent.max'      => 'Persentase PPN maksimal 100.',
            'pph_percent.required' => 'Persentase PPh harus diisi.',
            'pph_percent.numeric'  => 'Persentase PPh harus angka.',
            'pph_percent.max'      => 'Persentase PPh maksimal 100.',
        ];
    }
}
