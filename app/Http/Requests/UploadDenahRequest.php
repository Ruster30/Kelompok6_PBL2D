<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDenahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id'      => 'required|exists:events,id',
            'layout_denah'  => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required'      => 'Event harus dipilih.',
            'event_id.exists'        => 'Event tidak ditemukan.',
            'layout_denah.required'  => 'File denah/layout harus diupload.',
            'layout_denah.file'      => 'Denah/layout harus berupa file.',
            'layout_denah.mimes'     => 'Format file harus: jpg, jpeg, png, webp.',
            'layout_denah.max'       => 'Ukuran file maksimal 5 MB.',
        ];
    }
}
