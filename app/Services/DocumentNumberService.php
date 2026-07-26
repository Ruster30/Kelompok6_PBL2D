<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * DocumentNumberService
 *
 * Menghasilkan nomor dokumen resmi otomatis setelah approval.
 * Format: SEQ/COMPANY/PREFIX/ROMAN_MONTH/YEAR
 * Contoh: 001/ALPHA/KONTRAK/VIII/2026
 */
class DocumentNumberService
{
    private const COMPANY_CODE = 'ALPHA';

    public function __construct(
        private readonly DocumentNumberingRepositoryInterface $numberingRepository,
    ) {}

    /**
     * Generate nomor dokumen untuk dokumen yang sudah Approved.
     * Jika nomor sudah ada, return nomor yang sudah ada.
     */
    public function generate(Document $document, User $generatedBy): string
    {
        // Cek apakah sudah punya nomor
        $existing = $this->numberingRepository->findByDocument($document->id);
        if ($existing) {
            return $existing->document_number;
        }

        // Validasi workflow
        if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
            throw new \App\Exceptions\DDMS\DDMSException(
                "Hanya dokumen Generated yang dapat diberikan nomor."
            );
        }

        $prefix  = $document->numberingPrefix();
        $year    = (int) now()->format('Y');
        $month   = (int) now()->format('n');
        $company = config('document.default_company_code', 'ALPH');

        // Dapatkan nomor urut berikutnya (atomic via lockForUpdate)
        $sequence = $this->numberingRepository->nextSequence($prefix, $year);
        $seqPadded = str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

        $documentNumber = sprintf(
            "%s/%s-%s/%s/%s",
            $seqPadded,
            $prefix,
            $company,
            $this->romanMonth($month),
            $year
        );

        // Simpan ke tabel document_numberings
        $this->numberingRepository->create([
            'document_id'     => $document->id,
            'document_number' => $documentNumber,
            'prefix'          => $prefix,
            'year'            => $year,
            'sequence_number' => $sequence,
            'generated_by'    => $generatedBy->id,
        ]);

        Log::info('Nomor dokumen berhasil dibuat', [
            'document_id'     => $document->id,
            'document_number' => $documentNumber,
            'generated_by'    => $generatedBy->id,
        ]);

        return $documentNumber;
    }

    /**
     * Konversi angka bulan ke Romawi.
     */
    private function romanMonth(int $month): string
    {
        return match ($month) {
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => '',
        };
    }
}