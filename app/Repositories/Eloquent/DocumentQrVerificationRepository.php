<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentQrVerification;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;

class DocumentQrVerificationRepository implements DocumentQrVerificationRepositoryInterface
{
    public function __construct(
        private readonly DocumentQrVerification $model,
    ) {}

    public function findById(int $id): ?DocumentQrVerification
    {
        return $this->model->find($id);
    }

    public function findByDocument(int $documentId): ?DocumentQrVerification
    {
        return $this->model->where('document_id', $documentId)->first();
    }

    public function findByToken(string $token): ?DocumentQrVerification
    {
        return $this->model
            ->where('verification_token', $token)
            ->with('document')
            ->first();
    }

    public function create(array $data): DocumentQrVerification
    {
        return $this->model->create($data);
    }

    public function delete(DocumentQrVerification $qrVerification): void
    {
        $qrVerification->delete();
    }
}
