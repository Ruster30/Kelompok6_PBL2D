<?php

namespace App\Http\Requests\Admin;

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
            'client_id'        => 'required|exists:users,id',
            'jenis_event'      => 'nullable|string|max:100',
            'tanggal_event'    => 'required|date',
            'lokasi_event'     => 'nullable|string|max:255',
            'jumlah_tamu'      => 'nullable|integer|min:0',
            'detail_kebutuhan' => 'nullable|string',
            'status_event'     => 'required|in:menunggu,diproses,berjalan,selesai,dibatalkan',
        ];
    }
}
