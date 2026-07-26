<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentNumbering;
use App\Models\User;
use App\Exceptions\DDMS\DocumentAlreadyNumberedException;
use App\Exceptions\DDMS\DocumentNotApprovedException;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * DocumentNumberingService
 *
 * Bertanggung jawab penuh terhadap business process penomoran dokumen resmi.
 * Tidak ada class lain yang boleh melakukan generate nomor dokumen.
 *
 * Flow:
 *   Approved -> generate() -> Simpan numbering -> Document.status = published
 *
 * @todo Migrasikan exception ke custom exception class.
 * @todo Format nomor sepenuhnya dari DdmsSetting pada iterasi berikutnya.
 */
class DocumentNumberingService
{
    public function __construct(
        private readonly DocumentNumberingRepositoryInterface $numberingRepository,
        private readonly DocumentRepositoryInterface $documentRepository,
        private readonly DdmsSettingService $settingService,
    ) {}

    // ── Generate ─────────────────────────────────────────────

    /**
     * Generate nomor resmi untuk dokumen yang sudah Approved.
     *
     * Business Rule:
     * - Hanya dokumen Approved yang boleh diberi nomor.
     * - Satu dokumen hanya boleh memiliki satu nomor.
     * - Nomor bersifat permanen — tidak bisa diubah atau digenerate ulang.
     * - Sequence diambil via Repository dengan lockForUpdate().
     * - Format nomor ditentukan oleh method buildDocumentNumber().
     *
     * Flow:
     *   1. Validasi document.status === approved
     *   2. Validasi document belum memiliki nomor
     *   3. Tentukan prefix
     *   4. Ambil nextSequence dari repository (atomic)
     *   5. Build document number
     *   6. Simpan numbering
     *   7. Update document.status = published
     *
     * @throws \RuntimeException Jika dokumen belum approved atau sudah memiliki nomor
     */
    public function generate(Document $document, User $generatedBy): DocumentNumbering
    {
        return DB::transaction(function () use ($document, $generatedBy): DocumentNumbering {
            // Business Rule 1: Hanya dokumen Approved
            if (! $document->isApproved()) {
                throw new \App\Exceptions\DDMS\DocumentNotApprovedException(
                    'Hanya dokumen berstatus Approved yang dapat diberi nomor. ' .
                    "Status saat ini: {$document->status}."
                );
            }

            // Business Rule 2: Satu dokumen = satu nomor
            if ($this->exists($document)) {
                throw new \App\Exceptions\DDMS\DocumentAlreadyNumberedException(
                    'Dokumen ini sudah memiliki nomor. Nomor bersifat permanen dan tidak dapat digenerate ulang.'
                );
            }

            $prefix = $this->resolvePrefix($document);
            $year   = (int) now()->format('Y');
            $seq    = $this->numberingRepository->nextSequence($prefix, $year);
            $number = $this->buildDocumentNumber($prefix, $year, $seq);

            $numbering = $this->numberingRepository->create([
                'document_id'      => $document->id,
                'document_number'  => $number,
                'prefix'           => $prefix,
                'year'             => $year,
                'sequence_number'  => $seq,
                'generated_by'     => $generatedBy->id,
            ]);

            // Update document status ke published
            $this->documentRepository->update($document, [
                'status' => Document::STATUS_PUBLISHED,
            ]);

            Log::info('Nomor dokumen digenerate', [
                'document_id' => $document->id,
                'document_number' => $number,
                'generated_by' => $generatedBy->id,
                'prefix' => $prefix,
                'sequence' => $seq,
            ]);

            return $numbering;
        });
    }

    // ── Query Methods ────────────────────────────────────────

    /**
     * Cari numbering berdasarkan dokumen.
     */
    public function findByDocument(Document $document): ?DocumentNumbering
    {
        return $this->numberingRepository->findByDocument($document->id);
    }

    /**
     * Cari numbering berdasarkan nomor dokumen.
     */
    public function findByNumber(string $number): ?DocumentNumbering
    {
        return $this->numberingRepository->findByNumber($number);
    }

    /**
     * Cek apakah dokumen sudah memiliki nomor.
     */
    public function exists(Document $document): bool
    {
        return $this->numberingRepository->existsByDocument($document->id);
    }

    // ── Private Helpers ──────────────────────────────────────

    /**
     * Tentukan prefix nomor berdasarkan tipe dokumen.
     *
     * Prioritas:
     *   1. Dari DdmsSetting (jika sudah dikonfigurasi)
     *   2. Mapping default berdasarkan tipe dokumen
     *
     * @todo Migrasikan mapping prefix ke DdmsSetting sepenuhnya
     */
    private function resolvePrefix(Document $document): string
    {
        // Cek dari setting terlebih dahulu
        $prefix = $this->settingService->getSettingValue(
            'numbering_prefix_' . $document->tipe,
        );

        if ($prefix) {
            return $prefix;
        }

        // Fallback ke mapping default
        return match ($document->tipe) {
            'proposal' => 'SP',
            'kontrak'  => 'KTR',
            'invoice'  => 'INV',
            'kwitansi' => 'KW',
            'rab'      => 'RAB',
            default    => 'DOC',
        };
    }

    /**
     * Cari kode perusahaan/entitas dari pengaturan.
     * Fallback ke string kosong jika tidak dikonfigurasi.
     */
    private function resolveCompanyCode(): string
    {
        return $this->settingService->getSettingValue('numbering_company_code', '');
    }

    /**
     * Bangun nomor dokumen lengkap.
     *
     * Format: {company}/{prefix}/{year}/{sequence_4digit}
     *
     * Contoh dengan company:  ALPHA/SP/2026/0001
     * Contoh tanpa company:   SP/2026/0001
     *
     * @todo Jadikan format sepenuhnya dari DdmsSetting
     */
    private function buildDocumentNumber(string $prefix, int $year, int $sequence): string
    {
        $seq = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        $company = $this->resolveCompanyCode();

        if ($company) {
            return "{$company}/{$prefix}/{$year}/{$seq}";
        }

        return "{$prefix}/{$year}/{$seq}";
    }
}
