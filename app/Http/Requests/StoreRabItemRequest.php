<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRabItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "event_id"       => "required|exists:events,id",
            "vendor_id"      => "nullable|exists:vendors,id",
            "nama_biaya"     => "required|string|max:255",
            "kategori_biaya" => "nullable|string|max:100",
            "jumlah_item"    => "required|integer|min:1",
            "harga_satuan"   => "required|numeric|min:0",
        ];
    }

    public function messages(): array
    {
        return [
            "event_id.required"    => "Event harus dipilih.",
            "event_id.exists"      => "Event tidak ditemukan.",
            "nama_biaya.required"  => "Nama biaya harus diisi.",
            "jumlah_item.required" => "Jumlah item harus diisi.",
            "jumlah_item.min"      => "Jumlah item minimal 1.",
            "harga_satuan.required"=> "Harga satuan harus diisi.",
            "harga_satuan.min"     => "Harga satuan tidak boleh negatif.",
        ];
    }
}