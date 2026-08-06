<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * DocumentNumberService
 *
 * Mengelola nomor dokumen resmi yang diinput MANUAL oleh Admin.
 * Tidak ada lagi auto-generation.
 *
 * Business Rules:
 * - Nomor wajib diisi.
 * - Nomor harus unik.
 * - Nomor hanya boleh diubah saat status Draft.
 */
class DocumentNumberService
{
    public function __construct(
        private readonly DocumentNumberingRepositoryInterface $numberingRepository,
    ) {}

    /**
     * Simpan nomor surat yang diinput manual oleh Admin.
     */
    public function setManualNumber(Document $document, string $number, User $setBy): void
    {
        // Business Rule: Hanya Draft yang boleh membuat/mengubah nomor surat
        if ($document->status !== \App\Enums\DocumentStatus::Draft) {
            throw ValidationException::withMessages([
                "nomor_surat" => "Nomor surat tidak dapat diubah karena dokumen sudah dikirim untuk proses approval.",
            ]);
        }

        $this->validateNumber($number);

        // Business Rule: Nomor harus unik
        if (! $this->isNumberAvailable($number, $document->id)) {
            throw ValidationException::withMessages([
                "nomor_surat" => "Nomor surat sudah digunakan oleh dokumen lain.",
            ]);
        }

        // Simpan / update pada tabel document_numberings
        $existing = $this->numberingRepository->findByDocument($document->id);

        if ($existing) {
            $this->numberingRepository->update($existing, [
                "document_number" => $number,
                "generated_by"    => $setBy->id,
            ]);
        } else {
            $this->numberingRepository->create([
                "document_id"     => $document->id,
                "document_number" => $number,
                "prefix"          => "MANUAL",
                "year"            => (int) now()->format("Y"),
                "sequence_number" => 0,
                "generated_by"    => $setBy->id,
            ]);
        }

        Log::info("Nomor surat diinput manual", [
            "document_id" => $document->id,
            "document_number" => $number,
            "set_by" => $setBy->id,
        ]);
    }

    /**
     * Validasi format nomor surat.
     */
    public function validateNumber(string $number): void
    {
        $number = trim($number);

        if ($number === "") {
            throw ValidationException::withMessages([
                "nomor_surat" => "Nomor surat wajib diisi.",
            ]);
        }

        if (strlen($number) > 255) {
            throw ValidationException::withMessages([
                "nomor_surat" => "Nomor surat maksimal 255 karakter.",
            ]);
        }
    }

    /**
     * Cek apakah nomor surat masih tersedia (belum dipakai dokumen lain).
     */
    public function isNumberAvailable(string $number, ?int $excludeDocumentId = null): bool
    {
        $existing = $this->numberingRepository->findByNumber(trim($number));

        if (! $existing) {
            return true;
        }

        // Jika nomor dipakai dokumen yang sama, anggap tersedia (update)
        return $excludeDocumentId !== null && $existing->document_id === $excludeDocumentId;
    }
}