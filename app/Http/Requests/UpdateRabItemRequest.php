<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRabItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id'      => 'nullable|exists:vendors,id',
            'nama_biaya'     => 'required|string|max:255',
            'kategori_biaya' => 'nullable|string|max:100',
            'satuan'         => 'nullable|string|max:50',   // [TAMBAH]
            'jumlah_item'    => 'required|integer|min:1',
            'harga_satuan'   => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_biaya.required'   => 'Nama biaya harus diisi.',
            'jumlah_item.required'  => 'Jumlah item harus diisi.',
            'jumlah_item.min'       => 'Jumlah item minimal 1.',
            'harga_satuan.required' => 'Harga satuan harus diisi.',
            'harga_satuan.min'      => 'Harga satuan tidak boleh negatif.',
        ];
    }
}