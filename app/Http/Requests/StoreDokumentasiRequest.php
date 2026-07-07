<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumentasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "tugas_id" => "required|exists:tasks,id",
            "file"     => "required|array|min:1",  // UPDATED: Sekarang menerima array file
            "file.*"   => "file|mimes:jpg,jpeg,png,mp4,mov|max:20480",  // Validasi setiap file
            "judul"    => "nullable|string|max:255",
            "catatan"  => "nullable|string|max:1000",
        ];
    }

    public function messages(): array
    {
        return [
            "tugas_id.required" => "Tugas tidak valid.",
            "tugas_id.exists"   => "Tugas tidak ditemukan.",
            "file.required"     => "File harus diunggah.",
            "file.array"        => "Format file tidak valid.",
            "file.min"          => "Minimal 1 file harus diunggah.",
            "file.*.file"       => "Salah satu file tidak valid.",
            "file.*.mimes"      => "Format file harus jpg, jpeg, png, mp4, atau mov.",
            "file.*.max"        => "Ukuran file maksimal 20 MB per file.",
        ];
    }
}