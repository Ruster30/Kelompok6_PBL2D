<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * DocumentVerificationService
 *
 * Mengelola Verification Token — identitas permanen setiap dokumen Published.
 *
 * Business Rules:
 * - Token hanya dibuat saat status Published.
 * - Source harus Generated.
 * - Nomor surat wajib ada.
 * - Satu dokumen hanya memiliki satu token (immutable).
 */
class DocumentVerificationService
{
    public function __construct(
        private readonly DocumentQrVerificationRepositoryInterface $qrRepository,
    ) {}

    /**
     * Dapatkan token verifikasi. Jika belum ada, buat UUID v4.
     */
    public function getOrCreateVerificationToken(Document $document): string
    {
        // Business Rule: Status harus Published
        if ($document->status !== \App\Enums\DocumentStatus::Published) {
            throw ValidationException::withMessages([
                "verification" => "Token verifikasi hanya dapat dibuat untuk dokumen Published.",
            ]);
        }

        // Business Rule: Source harus Generated
        if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
            throw ValidationException::withMessages([
                "verification" => "Hanya dokumen Generated yang memiliki token verifikasi.",
            ]);
        }

        // Business Rule: Nomor surat wajib ada
        $numbering = $document->numbering;
        if (! $numbering || ! $numbering->document_number) {
            throw ValidationException::withMessages([
                "verification" => "Nomor surat wajib diisi sebelum token verifikasi dibuat.",
            ]);
        }

        // Jika token sudah ada ? return existing (immutable)
        $existing = $this->qrRepository->findByDocument($document->id);
        if ($existing && $existing->verification_token) {
            return $existing->verification_token;
        }

        // Buat token UUID v4
        $token = (string) Str::uuid();

        $this->qrRepository->create([
            "document_id"        => $document->id,
            "verification_token" => $token,
            "qr_path"            => null,
            "generated_by"       => null,
            "generated_at"       => null,
            "expires_at"         => null,
        ]);

        Log::info("Verification Token dibuat", [
            "document_id" => $document->id,
            "document_number" => $numbering->document_number,
            "token" => $token,
        ]);

        return $token;
    }

    /**
     * Ambil token yang sudah ada (tanpa membuat baru).
     */
    public function getVerificationToken(Document $document): ?string
    {
        $existing = $this->qrRepository->findByDocument($document->id);

        return $existing?->verification_token;
    }
}