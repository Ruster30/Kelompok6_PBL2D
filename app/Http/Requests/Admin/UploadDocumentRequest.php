<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'     => 'required|file|max:102400|mimes:svg,png,jpg,jpeg,pdf,docx,xlsx',
            'event_id' => 'nullable|exists:events,id',
            'tipe'     => 'required|in:proposal,kontrak,invoice,rab,laporan,lainnya',
        ];
    }
}
