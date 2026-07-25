<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\RejectDocumentDTO;
use Illuminate\Foundation\Http\FormRequest;

class RejectDocumentRequest extends FormRequest
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
            'reason'      => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'approval_id.required' => 'Approval wajib dipilih.',
            'approval_id.exists'   => 'Approval tidak ditemukan.',
            'approver_id.required' => 'Penyetuju wajib diisi.',
            'approver_id.exists'   => 'Penyetuju tidak ditemukan.',
            'reason.required'      => 'Alasan penolakan wajib diisi.',
            'reason.max'           => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'approval_id' => 'approval',
            'approver_id' => 'penyetuju',
            'reason'      => 'alasan penolakan',
        ];
    }

    public function toDTO(): RejectDocumentDTO
    {
        return RejectDocumentDTO::fromArray($this->validated());
    }
}
