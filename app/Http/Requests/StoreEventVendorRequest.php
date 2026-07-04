<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "event_id"      => "required|exists:events,id",
            "vendor_id"     => "required|exists:vendors,id",
            "jadwal_vendor" => "nullable|date",
            "status_vendor" => "required|in:ditugaskan,dikerjakan,selesai",
            "harga_vendor"  => "nullable|numeric",
            "nama_tugas"    => "nullable|string|max:255",
            "prioritas"     => "nullable|in:rendah,sedang,tinggi",
            "deskripsi"     => "nullable|string",
        ];
    }

    public function messages(): array
    {
        return [
            "event_id.required"      => "Event harus dipilih.",
            "event_id.exists"        => "Event tidak ditemukan.",
            "vendor_id.required"     => "Vendor harus dipilih.",
            "vendor_id.exists"       => "Vendor tidak ditemukan.",
            "status_vendor.required" => "Status vendor harus dipilih.",
            "status_vendor.in"       => "Status vendor tidak valid.",
        ];
    }
}