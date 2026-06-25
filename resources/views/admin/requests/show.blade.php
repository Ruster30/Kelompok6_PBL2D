@extends('layouts.admin')

@section('title', 'Detail Request Client')
@section('page-title', 'Detail Request Client')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>{{ $request->nama_event }}</h1>
        <p>Detail pengajuan event dari client.</p>
    </div>
    <div style="display:flex; gap:10px;">
        @if($request->latestProposal)
        <a href="{{ route('admin.proposals.download', $request->latestProposal) }}" target="_blank" class="btn btn-primary"><i class="fas fa-file-alt"></i> Lihat Penawaran</a>
        @else
        <a href="{{ route('admin.proposals.builder', ['event_id' => $request->id]) }}" class="btn btn-primary"><i class="fas fa-file-alt"></i> Buat Penawaran</a>
        @endif
        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="card" style="padding:24px;">
    <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:18px;">
        <div>
            <div class="form-label">Client</div>
            <div>{{ $request->client->name ?? '-' }}</div>
        </div>
        <div>
            <div class="form-label">Status</div>
            <div>{{ $request->status_label }}</div>
        </div>
        <div>
            <div class="form-label">Jenis Event</div>
            <div>{{ $request->jenis_event ?? '-' }}</div>
        </div>
        <div>
            <div class="form-label">Tanggal Event</div>
            <div>{{ $request->tanggal_event ? $request->tanggal_event->format('d M Y') : '-' }}</div>
        </div>
        <div>
            <div class="form-label">Lokasi</div>
            <div>{{ $request->lokasi_event ?? '-' }}</div>
        </div>
        <div>
            <div class="form-label">Jumlah Tamu</div>
            <div>{{ number_format($request->jumlah_tamu ?? 0, 0, ',', '.') }}</div>
        </div>
        <div>
            <div class="form-label">Rentang Anggaran</div>
            <div>{{ $request->rentang_anggaran ?? '-' }}</div>
        </div>
        <div style="grid-column:1/-1;">
            <div class="form-label">Detail Kebutuhan</div>
            <div style="line-height:1.7;">{{ $request->detail_kebutuhan ?: '-' }}</div>
        </div>
    </div>

    @if($request->status_event === 'menunggu')
    <div style="display:flex; gap:10px; margin-top:24px;">
        <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i> Setujui
            </button>
        </form>
        <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST" onsubmit="return confirm('Tolak request ini?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-outline">
                <i class="fas fa-times"></i> Tolak
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
