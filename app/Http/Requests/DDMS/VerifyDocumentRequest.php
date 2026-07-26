<?php

declare(strict_types=1);

namespace App\Http\Requests\DDMS;

use App\DTOs\VerifyDocumentDTO;
use App\Models\DocumentVerificationLog;
use Illuminate\Foundation\Http\FormRequest;

class VerifyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'      => 'required|string|size:32',
            'ip_address' => 'required|ip',
            'user_agent' => 'required|string|max:500',
            'source'     => 'nullable|in:' . implode(',', [
                DocumentVerificationLog::SOURCE_PUBLIC,
                DocumentVerificationLog::SOURCE_ADMIN,
                DocumentVerificationLog::SOURCE_API,
                DocumentVerificationLog::SOURCE_MOBILE,
                DocumentVerificationLog::SOURCE_SYSTEM,
            ]),
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'      => 'Token QR wajib diisi.',
            'token.size'          => 'Hanya token 32 karakter yang valid.',
            'ip_address.required' => 'IP address wajib diisi.',
            'ip_address.ip'       => 'Format IP address tidak valid.',
            'user_agent.required' => 'User agent wajib diisi.',
            'user_agent.max'      => 'User agent maksimal 500 karakter.',
            'source.in'           => 'Sumber verifikasi tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'token'      => 'token QR',
            'ip_address' => 'IP address',
            'user_agent' => 'user agent',
            'source'     => 'sumber verifikasi',
        ];
    }

    public function toDTO(): VerifyDocumentDTO
    {
        $data = $this->validated();
        $data['verified_by'] = $this->user();

        return VerifyDocumentDTO::fromArray($data);
    }
}
