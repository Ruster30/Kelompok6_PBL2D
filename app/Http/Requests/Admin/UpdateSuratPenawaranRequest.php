<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuratPenawaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi checkbox include_ppn sebelum validasi.
     * Checkbox HTML tidak mengirim field apapun ketika tidak dicentang,
     * sehingga kita perlu secara eksplisit set false jika tidak ada.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'include_ppn' => $this->boolean('include_ppn'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nomor_surat_override' => 'required|string|max:100',
            'perihal'              => 'nullable|string|max:255',
            'lokasi_event'         => 'nullable|string|max:255',
            'jenis_event'          => 'nullable|string|max:100',
            'tanggal_event'        => 'nullable|date',
            'tanggal_selesai'      => 'nullable|date|after_or_equal:tanggal_event',
            'luas_area'            => 'nullable|string|max:100',
            'rentang_anggaran'     => 'nullable|string|max:100',
            'terbilang'            => 'nullable|string|max:255',
            'detail_kebutuhan'     => 'nullable|string',
            'include_ppn'          => 'required|boolean',
        ];
    }
}
