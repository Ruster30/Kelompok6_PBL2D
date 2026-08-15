<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentQrVerification;
use App\Models\User;
use App\Exceptions\DDMS\DocumentAlreadyHasQrException;
use App\Exceptions\DDMS\QrVerificationExpiredException;
use App\Exceptions\DDMS\QrVerificationNotFoundException;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * DocumentQrVerificationService
 *
 * Bertanggung jawab terhadap business process QR Verification DDMS.
 *
 * Service ini hanya mengelola metadata QR dan validasi token.
 * Generate gambar QR (PNG/SVG) dilakukan oleh Infrastructure Layer terpisah.
 *
 * Flow:
 *   Published -> generate() -> Simpan metadata QR -> (future: generate image)
 *   Scan QR -> validateToken() -> Return DocumentQrVerification jika valid
 *
 * @todo Migrasikan exception ke custom exception class.
 * @todo Integrasikan dengan QrCodeGenerator (Infrastructure Layer).
 */
class DocumentQrVerificationService
{
    public function __construct(
        private readonly DocumentQrVerificationRepositoryInterface $qrRepository,
        private readonly DocumentRepositoryInterface $documentRepository,
        private readonly DdmsSettingService $settingService,
    ) {}

    // ── Generate ─────────────────────────────────────────────

    /**
     * Generate QR verification untuk dokumen yang sudah Published.
     *
     * Business Rule:
     * - Hanya dokumen Published yang boleh memiliki QR.
     * - Satu dokumen hanya boleh memiliki satu QR.
     * - Token unik 32 karakter hexadecimal (128-bit random).
     * - Expiration date dari DdmsSetting (default 365 hari).
     *
     * Flow:
     *   1. Validasi document.status === published
     *   2. Validasi document belum memiliki QR
     *   3. Generate token
     *   4. Tentukan expiry date
     *   5. Simpan metadata QR (qr_path = placeholder)
     *
     * @throws \RuntimeException Jika dokumen belum published atau QR sudah ada
     */
    public function generate(Document $document, User $generatedBy): DocumentQrVerification
    {
        return DB::transaction(function () use ($document, $generatedBy): DocumentQrVerification {
            // Business Rule 1: Hanya dokumen Published
            if (! $document->isPublished()) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    'Hanya dokumen berstatus Published yang dapat memiliki QR. ' .
                    "Status saat ini: {$document->status->value}."
                );
            }

            // Business Rule 2: Satu dokumen = satu QR
            if ($this->exists($document)) {
                throw new \App\Exceptions\DDMS\DocumentAlreadyHasQrException(
                    'Dokumen ini sudah memiliki QR Verification. QR bersifat permanen dan tidak dapat digenerate ulang.'
                );
            }

            $token     = $this->generateToken();
            $expiresAt = $this->resolveExpiryDate();
            $now       = now();

            $qr = $this->qrRepository->create([
                'document_id'         => $document->id,
                'verification_token'  => $token,
                'qr_path'             => '',  // placeholder — diisi oleh QrCodeGenerator nanti
                'generated_by'        => $generatedBy->id,
                'generated_at'        => $now,
                'expires_at'          => $expiresAt,
            ]);

            Log::info('QR Code digenerate', [
                'document_id' => $document->id,
                'qr_id' => $qr->id,
                'generated_by' => $generatedBy->id,
                'expires_at' => $expiresAt?->toISOString(),
            ]);

            return $qr;
        });
    }

    // ── Query Methods ────────────────────────────────────────

    /**
     * Cari QR verification berdasarkan dokumen.
     */
    public function findByDocument(Document $document): ?DocumentQrVerification
    {
        return $this->qrRepository->findByDocument($document->id);
    }

    /**
     * Cari QR verification berdasarkan token.
     */
    public function findByToken(string $token): ?DocumentQrVerification
    {
        return $this->qrRepository->findByToken($token);
    }

    /**
     * Validasi token untuk verifikasi publik.
     *
     * Business Rule:
     * - Token harus ditemukan di database.
     * - Token belum expired.
     *
     * @throws \RuntimeException Jika token tidak ditemukan atau sudah expired
     */
    public function validateToken(string $token): DocumentQrVerification
    {
        $qr = $this->qrRepository->findByToken($token);

        if (! $qr) {
            throw new \App\Exceptions\DDMS\QrVerificationNotFoundException('Token verifikasi tidak ditemukan.');
        }

        if ($qr->isExpired()) {
            throw new \App\Exceptions\DDMS\QrVerificationExpiredException(
                'QR Code sudah kedaluwarsa sejak ' . $qr->expires_at->format('d M Y H:i') . '.'
            );
        }

        return $qr;
    }

    /**
     * Cek apakah dokumen sudah memiliki QR verification.
     */
    public function exists(Document $document): bool
    {
        return $this->qrRepository->findByDocument($document->id) !== null;
    }

    // ── Private Helpers ──────────────────────────────────────

    /**
     * Generate token verifikasi unik.
     *
     * Token: 32 karakter hexadecimal (128-bit random).
     * Format: bin2hex(random_bytes(16))
     */
    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Tentukan tanggal kedaluwarsa QR.
     *
     * Prioritas:
     *   1. Dari DdmsSetting: verification_expiry_days
     *   2. Default: 365 hari
     *
     * @todo Migrasikan ke DdmsSetting sepenuhnya
     */
    private function resolveExpiryDate(): ?\Carbon\Carbon
    {
        $days = (int) $this->settingService->getSettingValue(
            'verification_expiry_days',
            365,
        );

        // 0 atau null berarti QR berlaku permanen
        if ($days <= 0) {
            // expires_at tetap diisi dengan NULL di database
            // Tapi kita override di sini karena kolom expires_at sudah dibuat nullable
            // Service akan mengirim Carbon agar repository konsisten
            // Repository akan menyimpan NULL karena kolom nullable
            return null;
        }

        return now()->addDays($days);
    }
}
