<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentNumbering;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DocumentNumberingRepository implements DocumentNumberingRepositoryInterface
{
    public function __construct(
        private readonly DocumentNumbering $model,
    ) {}

    public function findById(int $id): ?DocumentNumbering
    {
        return $this->model->find($id);
    }

    public function findByDocument(int $documentId): ?DocumentNumbering
    {
        return $this->model->where('document_id', $documentId)->first();
    }

    public function findByNumber(string $number): ?DocumentNumbering
    {
        return $this->model->where('document_number', $number)->first();
    }

    public function nextSequence(string $prefix, int $year): int
    {
        return DB::transaction(function () use ($prefix, $year): int {
            $max = $this->model
                ->where('prefix', $prefix)
                ->where('year', $year)
                ->lockForUpdate()
                ->max('sequence_number');

            return ($max ?? 0) + 1;
        });
    }

    public function create(array $data): DocumentNumbering
    {
        return $this->model->create($data);
    }

    public function existsByDocument(int $documentId): bool
    {
        return $this->model->where('document_id', $documentId)->exists();
    }
}
