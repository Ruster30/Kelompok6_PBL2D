<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DocumentApproval;
use Illuminate\Database\Eloquent\Collection;

interface DocumentApprovalRepositoryInterface
{
    /** Cari approval berdasarkan ID */
    public function findById(int $id): ?DocumentApproval;

    /** Semua riwayat approval untuk satu dokumen */
    public function findByDocument(int $documentId): Collection;

    /** Semua approval yang masih pending (dashboard direktur) */
    public function findPending(): Collection;

    /** Approval terakhir untuk satu dokumen */
    public function findLatestByDocument(int $documentId): ?DocumentApproval;

    /** Buat approval baru */
    public function create(array $data): DocumentApproval;

    /** Update approval (misal: approve/reject) */
    public function update(DocumentApproval $approval, array $data): DocumentApproval;

    /** Hapus approval */
    public function delete(DocumentApproval $approval): void;
}
