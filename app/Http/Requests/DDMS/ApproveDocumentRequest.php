<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\ApproveDocumentDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApproveDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approval_id' => 'required|exists:document_approvals,id',
            'approver_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'approval_id.required' => 'Approval wajib dipilih.',
            'approval_id.exists'   => 'Approval tidak ditemukan.',
            'approver_id.required' => 'Penyetuju wajib diisi.',
            'approver_id.exists'   => 'Penyetuju tidak ditemukan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'approval_id' => 'approval',
            'approver_id' => 'penyetuju',
        ];
    }

    public function toDTO(): ApproveDocumentDTO
    {
        return ApproveDocumentDTO::fromArray($this->validated());
    }
}
