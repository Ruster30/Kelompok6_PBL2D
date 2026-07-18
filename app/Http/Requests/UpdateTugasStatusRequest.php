<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTugasStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "tugas_id" => "required|exists:tasks,id",
            "status"   => "required|in:ditugaskan,dikerjakan,selesai",
        ];
    }

    public function messages(): array
    {
        return [
            "tugas_id.required" => "Tugas tidak valid.",
            "tugas_id.exists"   => "Tugas tidak ditemukan.",
            "status.required"   => "Status harus dipilih.",
            "status.in"         => "Status tidak valid.",
        ];
    }
}