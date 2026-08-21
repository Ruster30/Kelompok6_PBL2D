<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DocumentVerificationLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DocumentVerificationLogRepositoryInterface
{
    /** Cari log berdasarkan ID */
    public function findById(int $id): ?DocumentVerificationLog;

    /** Semua log untuk satu QR verification */
    public function findByVerification(int $verificationId): Collection;

    /** Log verifikasi terbaru */
    public function findRecent(int $limit = 20): Collection;

    /** Hitung jumlah log berdasarkan status */
    public function countByStatus(string $status): int;

    /** Hitung jumlah log berdasarkan status dan tanggal */
    public function countByStatusAndDate(string $status, string $date): int;

    /** Buat log verifikasi baru */
    public function create(array $data): DocumentVerificationLog;

    /** Paginate logs dengan filter dan search */
    public function paginateWithFilters(
        int $page = 1,
        int $perPage = 20,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $source = null,
        ?string $search = null
    ): LengthAwarePaginator;
}
