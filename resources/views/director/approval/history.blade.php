@extends('layouts.director')

@section('title', 'Riwayat Approval')
@section('page-title', 'Riwayat Approval')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Riwayat Approval</h1>
        <p>Daftar dokumen yang telah diproses Director.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('director.approval.history') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Cari nomor dokumen, event, atau client..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nomor Dokumen</th>
                        <th>Jenis</th>
                        <th>Event / Client</th>
                        <th>Status</th>
                        <th>Director</th>
                        <th>Tanggal Approval</th>
                        <th class="pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    @php
                        $approval = $document->approvals?->firstWhere('status', $document->status->value);
                    @endphp
                    <tr>
                        <td class="ps-3">
                            @php $docNum = optional($document->numbering)->document_number; @endphp
                            @if($docNum)
                                <span style="font-family:monospace;font-size:12px;">{{ $docNum }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $document->tipe_label }}</span>
                        </td>
                        <td>
                            <div class="fw-medium small">{{ $document->event?->nama_event ?? '-' }}</div>
                            <small class="text-muted">{{ $document->event?->client?->name ?? '-' }}</small>
                        </td>
                        <td>
                            @php
    $statusColors = [
        "approved" => ["bg" => "#dcfce7", "border" => "#86efac", "text" => "#166534"],
        "rejected" => ["bg" => "#fee2e2", "border" => "#fca5a5", "text" => "#991b1b"],
        "pending" => ["bg" => "#fef3c7", "border" => "#fcd34d", "text" => "#92400e"],
        "draft" => ["bg" => "#f1f5f9", "border" => "#cbd5e1", "text" => "#475569"],
    ];
    $s = $document->status?->value ?? "draft";
    $sc = $statusColors[$s] ?? $statusColors["draft"];
@endphp
<span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;background:{{ $sc["bg"] }};border:1px solid {{ $sc["border"] }};color:{{ $sc["text"] }};">{{ $document->status?->label() ?? $s }}</span>
                        </td>
                        <td>
                            <span class="small">{{ $approval?->approvedBy?->name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">
                                {{ $approval?->reviewed_at?->format('d M Y, H:i') ?? '-' }}
                            </span>
                        </td>
                        <td class="pe-3">
                            <a href="{{ route('director.approval.history-show', $document->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size:36px;opacity:0.4;"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada riwayat approval.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
        <div class="d-flex justify-content-center py-3 border-top">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection