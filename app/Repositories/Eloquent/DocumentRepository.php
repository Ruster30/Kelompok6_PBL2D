<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function __construct(
        private readonly Document $model,
    ) {}

    public function findById(int $id): ?Document
    {
        return $this->model->find($id);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }

    public function findDraft(): Collection
    {
        return $this->findByStatus(Document::STATUS_DRAFT);
    }

    public function findPending(): Collection
    {
        return $this->findByStatus(Document::STATUS_PENDING);
    }

    public function findApproved(): Collection
    {
        return $this->findByStatus(Document::STATUS_APPROVED);
    }

    public function findRejected(): Collection
    {
        return $this->findByStatus(Document::STATUS_REJECTED);
    }

    public function findPublished(): Collection
    {
        return $this->findByStatus(Document::STATUS_PUBLISHED);
    }

    public function create(array $data): Document
    {
        return $this->model->create($data);
    }

    public function update(Document $document, array $data): Document
    {
        $document->update($data);
        return $document->fresh();
    }

    public function delete(Document $document): bool
    {
        return $document->delete();
    }

    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }
}
