<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentVerificationLog;
use App\Repositories\Contracts\DocumentVerificationLogRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VerificationAuditController extends Controller
{
    public function __construct(
        private readonly DocumentVerificationLogRepositoryInterface $logRepository,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', DocumentVerificationLog::class);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in([
                DocumentVerificationLog::STATUS_VALID,
                DocumentVerificationLog::STATUS_EXPIRED,
                DocumentVerificationLog::STATUS_INVALID,
                DocumentVerificationLog::STATUS_TAMPERED,
            ])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $status = $validated['status'] ?? null;
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        $search = $validated['search'] ?? null;
        $page = (int) ($validated['page'] ?? 1);

        $logs = $this->logRepository->paginateWithFilters(
            page: $page,
            perPage: 20,
            status: $status,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            search: $search,
        )->withQueryString();

        return view('admin.verification_audit.index', [
            'logs' => $logs,
            'statuses' => [
                DocumentVerificationLog::STATUS_VALID => 'Valid',
                DocumentVerificationLog::STATUS_EXPIRED => 'Expired',
                DocumentVerificationLog::STATUS_INVALID => 'Invalid',
                DocumentVerificationLog::STATUS_TAMPERED => 'Tampered',
            ],
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search,
            ],
        ]);
    }

    public function show(DocumentVerificationLog $log): View
    {
        Gate::authorize('view', $log);

        $log->load([
            'documentQrVerification.document.numbering',
            'documentQrVerification.document.event',
            'verifiedBy',
        ]);

        return view('admin.verification_audit.show', [
            'log' => $log,
        ]);
    }
}
