<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DocumentQrVerification;
use Illuminate\Database\Eloquent\Collection;

interface DocumentQrVerificationRepositoryInterface
{
    /** Cari QR berdasarkan ID */
    public function findById(int $id): ?DocumentQrVerification;

    /** Cari QR berdasarkan dokumen (one-to-one) */
    public function findByDocument(int $documentId): ?DocumentQrVerification;

    /** Cari QR berdasarkan verification token (untuk scan publik) */
    public function findByToken(string $token): ?DocumentQrVerification;

    /** Buat QR verification baru */
    public function create(array $data): DocumentQrVerification;

    /** Hapus QR verification */
    public function delete(DocumentQrVerification $qrVerification): void;
}
