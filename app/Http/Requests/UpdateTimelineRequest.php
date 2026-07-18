<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nama_kegiatan"    => "required|string|max:255",
            "deskripsi"        => "nullable|string|max:1000",
            "penanggung_jawab" => "nullable|string|max:100",
            "tanggal_kegiatan" => "required|date",
            "deadline"         => "nullable|date",
            "status_kegiatan"  => "required|in:belum_mulai,berjalan,selesai",
        ];
    }

    public function messages(): array
    {
        return [
            "nama_kegiatan.required"    => "Nama kegiatan harus diisi.",
            "tanggal_kegiatan.required" => "Tanggal kegiatan harus diisi.",
            "tanggal_kegiatan.date"     => "Format tanggal kegiatan tidak valid.",
            "status_kegiatan.required"  => "Status kegiatan harus dipilih.",
            "status_kegiatan.in"        => "Status kegiatan tidak valid.",
        ];
    }
}