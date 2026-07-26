<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentApproval;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentApprovalRepository implements DocumentApprovalRepositoryInterface
{
    public function __construct(
        private readonly DocumentApproval $model,
    ) {}

    public function findById(int $id): ?DocumentApproval
    {
        return $this->model->find($id);
    }

    public function findByDocument(int $documentId): Collection
    {
        return $this->model
            ->where('document_id', $documentId)
            ->latest()
            ->get();
    }

    public function findPending(): Collection
    {
        return $this->model
            ->where('status', DocumentApproval::STATUS_PENDING)
            ->with('document', 'submittedBy')
            ->oldest('submitted_at')
            ->get();
    }

    public function findLatestByDocument(int $documentId): ?DocumentApproval
    {
        return $this->model
            ->where('document_id', $documentId)
            ->latest()
            ->first();
    }

    public function create(array $data): DocumentApproval
    {
        return $this->model->create($data);
    }

    public function update(DocumentApproval $approval, array $data): DocumentApproval
    {
        $approval->update($data);
        return $approval->fresh();
    }

    public function delete(DocumentApproval $approval): void
    {
        $approval->delete();
    }
}
