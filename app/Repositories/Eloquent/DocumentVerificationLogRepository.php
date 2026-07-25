<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentVerificationLog;
use App\Repositories\Contracts\DocumentVerificationLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentVerificationLogRepository implements DocumentVerificationLogRepositoryInterface
{
    public function __construct(
        private readonly DocumentVerificationLog $model,
    ) {}

    public function findById(int $id): ?DocumentVerificationLog
    {
        return $this->model->find($id);
    }

    public function findByVerification(int $verificationId): Collection
    {
        return $this->model
            ->where('verification_id', $verificationId)
            ->latest('verified_at')
            ->get();
    }

    public function findRecent(int $limit = 20): Collection
    {
        return $this->model
            ->with('documentQrVerification.document')
            ->latest('verified_at')
            ->limit($limit)
            ->get();
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function countByStatusAndDate(string $status, string $date): int
    {
        return $this->model
            ->where('status', $status)
            ->whereDate('verified_at', $date)
            ->count();
    }

    public function create(array $data): DocumentVerificationLog
    {
        return $this->model->create($data);
    }
}
