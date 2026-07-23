<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_event'       => 'required|string|max:255',
            'jenis_event'      => 'required|string',
            'tanggal_event'    => 'required|date|after:today',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_event',
            'lokasi_event'     => 'required|string|max:500',
            'jumlah_tamu'      => 'nullable|integer|min:1',
            'rentang_anggaran' => 'nullable|string|max:100',
            'detail_kebutuhan' => 'nullable|string|max:2000',
        ];
    }
}
