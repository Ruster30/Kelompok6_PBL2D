<?php

namespace App\Http\Requests\Admin;

use App\Models\Event;
use App\Services\AdminProposalService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

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

    /**
     * Server-side guard tambahan untuk DDMS edit lock.
     * Dipindahkan ke FormRequest agar dijalankan SEBELUM controller body
     * (cegah bypass via direct POST dengan data tidak lengkap).
     *
     * Jika Proposal memiliki Document DDMS dan statusnya tidak mengizinkan
     * edit (draft/pending/approved/published), tambahkan error 'edit'
     * sehingga request ditolak.
     */
    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $eventParam = $this->route('event');

            // Handle: Event model, event ID (int/string), or Collection
            if ($eventParam instanceof Event) {
                $event = $eventParam;
            } elseif (is_numeric($eventParam)) {
                $event = Event::find($eventParam);
            } elseif ($eventParam instanceof \Illuminate\Database\Eloquent\Collection) {
                $event = $eventParam->first();
            } else {
                return;
            }

            if (! $event) {
                return;
            }

            $service = App::make(AdminProposalService::class);
            if (! $service->canEditDdmsProposal($event)) {
                $validator->errors()->add('edit', 'Surat Penawaran dengan DDMS tidak dapat diedit pada status dokumen saat ini. Gunakan DDMS untuk memperbarui.');
            }
        });
    }
}
