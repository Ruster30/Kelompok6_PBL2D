<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DocumentRepositoryInterface
{
    /** Cari document berdasarkan ID */
    public function findById(int $id): ?Document;

    /** Cari document berdasarkan status */
    public function findByStatus(string $status): Collection;

    /** Dokumen dengan status draft */
    public function findDraft(): Collection;

    /** Dokumen dengan status pending */
    public function findPending(): Collection;

    /** Dokumen dengan status approved */
    public function findApproved(): Collection;

    /** Dokumen dengan status rejected */
    public function findRejected(): Collection;

    /** Dokumen dengan status published */
    public function findPublished(): Collection;

    /** Buat document baru */
    public function create(array $data): Document;

    /** Update document */
    public function update(Document $document, array $data): Document;

    /** Hapus document */
    public function delete(Document $document): bool;

    /** Cek apakah document dengan ID tertentu ada */
    public function exists(int $id): bool;

    /** Pagination document */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
