<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentQrVerification;
use App\Models\DocumentVerificationLog;
use App\Models\User;
use App\Repositories\Contracts\DocumentVerificationLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * DocumentVerificationLogService
 *
 * Bertanggung jawab mencatat seluruh Audit Trail Verifikasi QR.
 *
 * Service ini adalah satu-satunya class yang boleh membuat Verification Log.
 * Log bersifat immutable — tidak boleh update, tidak boleh delete.
 *
 * Flow:
 *   QR Service validateToken()
 *       ↓ (hasil validasi)
 *   Log Service log{Status}Verification()
 *       ↓
 *   Repository create()
 *
 * @todo Migrasikan exception ke custom exception class.
 */
class DocumentVerificationLogService
{
    public function __construct(
        private readonly DocumentVerificationLogRepositoryInterface $logRepository,
    ) {}

    // ── Log Methods ──────────────────────────────────────────

    /**
     * Catat verifikasi valid (token ditemukan, belum expired).
     */
    public function logValidVerification(
        DocumentQrVerification $verification,
        ?User $verifiedBy,
        string $ipAddress,
        string $userAgent,
        string $source = DocumentVerificationLog::SOURCE_PUBLIC,
    ): DocumentVerificationLog {
        return $this->createLog(
            $verification,
            DocumentVerificationLog::STATUS_VALID,
            $verifiedBy,
            $ipAddress,
            $userAgent,
            $source,
        );
    }

    /**
     * Catat verifikasi expired (token ditemukan, sudah melewati expires_at).
     */
    public function logExpiredVerification(
        DocumentQrVerification $verification,
        ?User $verifiedBy,
        string $ipAddress,
        string $userAgent,
        string $source = DocumentVerificationLog::SOURCE_PUBLIC,
    ): DocumentVerificationLog {
        return $this->createLog(
            $verification,
            DocumentVerificationLog::STATUS_EXPIRED,
            $verifiedBy,
            $ipAddress,
            $userAgent,
            $source,
        );
    }

    /**
     * Catat verifikasi invalid (token tidak ditemukan di database).
     *
     * KETERBATASAN SKEMA:
     * Tabel document_verification_logs memiliki foreign key verification_id
     * yang bersifat NOT NULL dan merujuk ke document_qr_verifications.id.
     *
     * Karena token tidak ditemukan, tidak ada DocumentQrVerification yang
     * dapat dijadikan referensi untuk verification_id.
     *
     * Solusi saat ini:
     *   - Method ini melempar exception karena secara skema tidak dapat
     *     membuat log tanpa verification_id yang valid.
     *
     * @todo Jika di masa depan ingin mencatat invalid token, perlu:
     *       1. Ubah verification_id menjadi nullable (ALTER TABLE)
     *       2. Tambah kolom attempted_token (varchar) untuk mencatat token yang dicoba
     *       3. Atau buat tabel terpisah untuk failed attempts
     *
     * @throws \RuntimeException Selalu — tidak dapat membuat log tanpa verification_id
     */
    public function logInvalidVerification(
        string $token,
        string $ipAddress,
        string $userAgent,
        string $source = DocumentVerificationLog::SOURCE_PUBLIC,
    ): never {
        throw new \RuntimeException(
            'Tidak dapat membuat verification log untuk token yang tidak ditemukan. ' .
            "Token '{$token}' tidak terdaftar di database. " .
            'Keterbatasan: kolom verification_id bersifat NOT NULL dan ' .
            'membutuhkan referensi ke document_qr_verifications yang valid. ' .
            'Lihat PHPDoc method ini untuk opsi solusi di masa depan.'
        );
    }

    /**
     * Catat verifikasi tampered (indikasi manipulasi data QR/dokumen).
     */
    public function logTamperedVerification(
        DocumentQrVerification $verification,
        ?User $verifiedBy,
        string $ipAddress,
        string $userAgent,
        string $source = DocumentVerificationLog::SOURCE_PUBLIC,
    ): DocumentVerificationLog {
        return $this->createLog(
            $verification,
            DocumentVerificationLog::STATUS_TAMPERED,
            $verifiedBy,
            $ipAddress,
            $userAgent,
            $source,
        );
    }

    // ── Query Methods ────────────────────────────────────────

    /**
     * Ambil log verifikasi terbaru.
     */
    public function findRecent(int $limit = 20): Collection
    {
        return $this->logRepository->findRecent($limit);
    }

    /**
     * Hitung jumlah log berdasarkan status.
     */
    public function countByStatus(string $status): int
    {
        return $this->logRepository->countByStatus($status);
    }

    // ── Private Helper ───────────────────────────────────────

    /**
     * Internal method untuk membuat verification log.
     *
     * Menghindari duplikasi kode di seluruh method log{Status}Verification().
     */
    private function createLog(
        DocumentQrVerification $verification,
        string $status,
        ?User $verifiedBy,
        string $ipAddress,
        string $userAgent,
        string $source,
    ): DocumentVerificationLog {
        return $this->logRepository->create([
            'verification_id'     => $verification->id,
            'verified_at'         => now(),
            'status'              => $status,
            'ip_address'          => $ipAddress,
            'user_agent'          => $userAgent,
            'verified_by'         => $verifiedBy?->id,
            'verification_source' => $source,
        ]);
    }
}
