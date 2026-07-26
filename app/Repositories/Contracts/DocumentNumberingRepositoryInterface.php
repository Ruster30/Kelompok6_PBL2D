<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DocumentNumbering;

interface DocumentNumberingRepositoryInterface
{
    /** Cari numbering berdasarkan ID */
    public function findById(int $id): ?DocumentNumbering;

    /** Cari numbering berdasarkan dokumen (one-to-one) */
    public function findByDocument(int $documentId): ?DocumentNumbering;

    /** Cari numbering berdasarkan nomor dokumen */
    public function findByNumber(string $number): ?DocumentNumbering;

    /** Dapatkan nomor urut berikutnya untuk prefix dan tahun tertentu */
    public function nextSequence(string $prefix, int $year): int;

    /** Buat numbering baru */
    public function create(array $data): DocumentNumbering;

    /** Cek apakah dokumen sudah memiliki nomor */
    public function existsByDocument(int $documentId): bool;
}
