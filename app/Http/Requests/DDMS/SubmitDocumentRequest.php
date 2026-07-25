<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\SubmitDocumentDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * SubmitDocumentRequest
 *
 * Validasi input untuk workflow submit dokumen ke approval.
 *
 * @todo Authorization akan dipindahkan ke Policy Layer.
 */
class SubmitDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_id'  => 'required|exists:documents,id',
            'submitted_by' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'document_id.required'  => 'Dokumen wajib dipilih.',
            'document_id.exists'    => 'Dokumen tidak ditemukan.',
            'submitted_by.required' => 'Pengaju wajib diisi.',
            'submitted_by.exists'   => 'Pengaju tidak ditemukan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'document_id'  => 'dokumen',
            'submitted_by' => 'pengaju',
        ];
    }

    public function toDTO(): SubmitDocumentDTO
    {
        return SubmitDocumentDTO::fromArray($this->validated());
    }
}
