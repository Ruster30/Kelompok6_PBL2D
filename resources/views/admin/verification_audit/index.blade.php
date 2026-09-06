@php
    $layout = auth()->user()?->isDirector() ? 'layouts.director' : 'layouts.admin';
    $routePrefix = request()->routeIs('director.*') ? 'director' : 'admin';
@endphp

@extends($layout)

@section('title', 'Verification Audit')
@section('page-title', 'Verification Audit')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($routePrefix . '.verification-audit.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label fw-semibold">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label fw-semibold">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label fw-semibold">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Nama file, nomor surat, nama event" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route($routePrefix . '.verification-audit.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Verification Audit Records</h5>
                <small class="text-muted">{{ $logs->total() }} records found</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Verification Time</th>
                            <th>Document</th>
                            <th>Nomor Surat</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>IP Address</th>
                            <th>User-Agent</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $document = $log->documentQrVerification?->document;
                                $numbering = $document?->numbering;
                                $statusClass = match($log->status) {
                                    \App\Models\DocumentVerificationLog::STATUS_VALID => 'success',
                                    \App\Models\DocumentVerificationLog::STATUS_EXPIRED => 'warning',
                                    \App\Models\DocumentVerificationLog::STATUS_INVALID => 'secondary',
                                    \App\Models\DocumentVerificationLog::STATUS_TAMPERED => 'danger',
                                    default => 'light',
                                };
                            @endphp
                            <tr>
                                <td><div class="fw-semibold">{{ optional($log->verified_at)->format('d M Y H:i') }}</div></td>
                                <td>
                                    <div class="fw-semibold">{{ $document?->nama_file ?? '-' }}</div>
                                    @if($document?->event?->nama_event)
                                        <small class="text-muted">{{ $document->event->nama_event }}</small>
                                    @endif
                                </td>
                                <td>{{ $numbering?->document_number ?? '-' }}</td>
                                <td><span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($log->status) }}</span></td>
                                <td>{{ ucfirst($log->verification_source) }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 260px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route($routePrefix . '.verification-audit.show', array_merge(['log' => $log], request()->query())) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Belum ada verification audit record.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
