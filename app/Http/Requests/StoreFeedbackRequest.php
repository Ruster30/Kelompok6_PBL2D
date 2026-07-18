<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "event_id" => "required|exists:events,id",
            "rating"   => "required|integer|min:1|max:5",
            "ulasan"   => "required|string|min:10",
        ];
    }

    public function messages(): array
    {
        return [
            "rating.required" => "Rating harus dipilih.",
            "rating.integer"  => "Rating tidak valid.",
            "rating.min"      => "Rating minimal 1.",
            "rating.max"      => "Rating maksimal 5.",
            "ulasan.required" => "Ulasan harus diisi.",
            "ulasan.min"      => "Ulasan minimal 10 karakter.",
            "event_id.required" => "Event tidak valid.",
            "event_id.exists"   => "Event tidak ditemukan.",
        ];
    }
}
