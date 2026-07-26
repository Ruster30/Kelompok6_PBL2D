<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Collection;

interface DocumentTemplateRepositoryInterface
{
    /** Ambil semua template */
    public function all(): Collection;

    /** Ambil template aktif saja (untuk dropdown) */
    public function findActive(): Collection;

    /** Cari template berdasarkan ID */
    public function findById(int $id): ?DocumentTemplate;

    /** Cari template berdasarkan kode unik */
    public function findByCode(string $code): ?DocumentTemplate;

    /** Buat template baru */
    public function create(array $data): DocumentTemplate;

    /** Update template */
    public function update(DocumentTemplate $template, array $data): DocumentTemplate;

    /** Hapus template */
    public function delete(DocumentTemplate $template): void;
}
