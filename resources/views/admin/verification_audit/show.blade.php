@php
    $layout = auth()->user()?->isDirector() ? 'layouts.director' : 'layouts.admin';
    $routePrefix = request()->routeIs('director.*') ? 'director' : 'admin';
    $document = $log->documentQrVerification?->document;
    $numbering = $document?->numbering;
@endphp

@extends($layout)

@section('title', 'Verification Audit Detail')
@section('page-title', 'Verification Audit Detail')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-3">
        <a href="{{ route($routePrefix . '.verification-audit.index', request()->query()) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Verification Record #{{ $log->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small fw-bold">Verification</h6>
                    <table class="table table-sm">
                        <tr><th class="w-50">Verification ID</th><td>{{ $log->id }}</td></tr>
                        <tr><th>Verification Status</th><td>{{ ucfirst($log->status) }}</td></tr>
                        <tr><th>Verification Source</th><td>{{ ucfirst($log->verification_source) }}</td></tr>
                        <tr><th>Verification Time</th><td>{{ optional($log->verified_at)->format('d M Y H:i:s') }}</td></tr>
                        <tr><th>IP Address</th><td>{{ $log->ip_address ?? '-' }}</td></tr>
                        <tr><th>User-Agent</th><td class="text-break">{{ $log->user_agent ?? '-' }}</td></tr>
                        <tr><th>Verified By</th><td>{{ $log->verifiedBy?->name ?? 'Public / Guest' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small fw-bold">Document</h6>
                    <table class="table table-sm">
                        <tr><th class="w-50">Document</th><td>{{ $document?->nama_file ?? '-' }}</td></tr>
                        <tr><th>Nomor Surat</th><td>{{ $numbering?->document_number ?? '-' }}</td></tr>
                        <tr><th>Event</th><td>{{ $document?->event?->nama_event ?? '-' }}</td></tr>
                        <tr><th>Verification Token</th><td><code>{{ $log->documentQrVerification?->verification_token ?? '-' }}</code></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
