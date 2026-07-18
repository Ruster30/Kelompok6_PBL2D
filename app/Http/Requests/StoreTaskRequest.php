<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nama_tugas" => "required|string|max:255",
            "event_id"   => "required|exists:events,id",
            "vendor_id"  => "nullable|exists:vendors,id",
            "prioritas"  => "required|in:rendah,sedang,tinggi",
            "deadline"   => "nullable|date",
            "status"     => "required|in:ditugaskan,dikerjakan,selesai",
            "deskripsi"  => "nullable|string",
        ];
    }

    public function messages(): array
    {
        return [
            "nama_tugas.required" => "Nama tugas harus diisi.",
            "event_id.required"   => "Event harus dipilih.",
            "event_id.exists"     => "Event tidak ditemukan.",
            "prioritas.required"  => "Prioritas harus dipilih.",
            "prioritas.in"        => "Prioritas tidak valid.",
            "status.required"     => "Status harus dipilih.",
            "status.in"           => "Status tidak valid.",
        ];
    }
}