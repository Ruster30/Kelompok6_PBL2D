@extends('layouts.director')

@section('title', 'Approval Dashboard')
@section('page-title', 'Approval Dashboard')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Approval Dashboard</h1>
        <p>Daftar dokumen yang menunggu proses Director (review atau publish).</p>
    </div>
</div>

{{-- Stat Card --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:52px;height:52px;">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Menunggu Proses</p>
                        <h3 class="fw-bold mb-0">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('director.approval.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small text-muted">Cari Dokumen</label>
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama dokumen..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Semua</option>
                    @php
                        use App\Enums\DocumentCategory;
                    @endphp
                    @foreach(DocumentCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" @selected(request('category') === $cat->value)>
                            {{ ucfirst($cat->value) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama Dokumen</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Event</th>
                        <th>Nomor</th>
                        <th>Tanggal Dibuat</th>
                        <th class="pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-medium">{{ $document->nama_file }}</div>
                        </td>
                        <td>
                            @php
                                $categoryLabels = [
                                    'official' => 'Official',
                                    'general' => 'General',
                                    'invoice' => 'Invoice',
                                    'receipt' => 'Receipt',
                                ];
                            @endphp
                            <span class="badge bg-label-secondary">
                                {{ $categoryLabels[$document->document_category?->value] ?? ucfirst($document->document_category?->value ?? '-') }}
                            </span>
                        </td>
                        <td>
                            <x-document-status-badge :status="$document->status" />
                        </td>
                        <td>
                            <x-document-source-badge :source="$document->document_source" />
                        </td>
                        <td>
                            <span class="small text-muted">
                                @if($document->event)
                                    {{ $document->event->nama_event }}
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td>
                            @php $docNum = optional($document->numbering)->document_number; @endphp
                            @if($docNum)
                                <span style="font-family:monospace;font-size:12px;">{{ $docNum }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">
                                {{ $document->created_at->format('d M Y, H:i') }}
                            </span>
                        </td>
                        <td class="pe-3">
                            <a href="{{ route('director.approval.show', $document->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size:36px;opacity:0.4;"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada dokumen yang menunggu approval.</p>
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