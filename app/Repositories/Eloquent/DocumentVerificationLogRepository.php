<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentVerificationLog;
use App\Repositories\Contracts\DocumentVerificationLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function paginateWithFilters(
        int $page = 1,
        int $perPage = 20,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $source = null,
        ?string $search = null
    ): LengthAwarePaginator
    {
        $query = $this->model
            ->with([
                'documentQrVerification.document.numbering',
                'documentQrVerification.document.event',
                'verifiedBy',
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('verified_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('verified_at', '<=', $dateTo);
        }

        if ($source) {
            $query->where('verification_source', $source);
        }

        if ($search) {
            $query->whereHas('documentQrVerification.document', function ($subQuery) use ($search) {
                $subQuery
                    ->where('nama_file', 'like', "%{$search}%")
                    ->orWhereHas('numbering', function ($numQuery) use ($search) {
                        $numQuery->where('document_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('event', function ($eventQuery) use ($search) {
                        $eventQuery->where('nama_event', 'like', "%{$search}%");
                    });
            });
        }

        return $query
            ->latest('verified_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
