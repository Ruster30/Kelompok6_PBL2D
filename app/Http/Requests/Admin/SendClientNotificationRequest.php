<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendClientNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'exists:users,id'],
            'judul'        => ['required', 'string', 'max:255'],
            'pesan'        => ['required', 'string', 'max:2000'],
            'tipe'         => ['required', 'string', 'in:info,promo,pengingat,peringatan,event,pembayaran'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_id.required' => 'Penerima notifikasi wajib dipilih.',
            'recipient_id.exists'   => 'Klien yang dipilih tidak ditemukan.',
            'judul.required'        => 'Judul notifikasi wajib diisi.',
            'judul.max'             => 'Judul maksimal 255 karakter.',
            'pesan.required'        => 'Isi pesan wajib diisi.',
            'pesan.max'             => 'Pesan maksimal 2000 karakter.',
            'tipe.required'         => 'Tipe notifikasi wajib dipilih.',
            'tipe.in'               => 'Tipe notifikasi tidak valid.',
        ];
    }
}
