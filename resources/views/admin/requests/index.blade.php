@extends('layouts.admin')

@section('title', 'Request Client')
@section('page-title', 'Request Client')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Request Client</h1>
        <p>Kelola pengajuan event baru dari client.</p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="border-bottom:none; padding-bottom:14px;">
        <form method="GET" class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari request..." value="{{ request('search') }}">
            </div>
            <select class="select-filter" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
                <option value="diproses" @selected(request('status') === 'diproses')>Diterima</option>
                <option value="berjalan" @selected(request('status') === 'berjalan')>Berjalan</option>
                <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-outline"><i class="fas fa-filter"></i> Filter Lainnya</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Client</th>
                <th>Nama Event</th>
                <th>Jenis Event</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Anggaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
            @php
                $map = ['menunggu' => 'badge-pending', 'diproses' => 'badge-active', 'berjalan' => 'badge-done', 'selesai' => 'badge-active', 'dibatalkan' => 'badge-cancel'];
                $labels = ['menunggu' => 'Menunggu', 'diproses' => 'Diterima', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
                $status = strtolower($req->status_event);
            @endphp
            <tr>
                <td style="font-weight:600;">{{ $req->client->name ?? '-' }}</td>
                <td style="font-weight:500;">{{ $req->nama_event }}</td>
                <td>{{ $req->jenis_event ?? '-' }}</td>
                <td>{{ $req->tanggal_event?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ $req->lokasi_event ?? '-' }}</td>
                <td>{{ $req->rentang_anggaran ?? '-' }}</td>
                <td><span class="badge {{ $map[$status] ?? 'badge-pending' }}">{{ $labels[$status] ?? ucfirst($status) }}</span></td>
                <td>
                    @if($req->latestProposal)
                    <a href="{{ route('admin.proposals.download', $req->latestProposal) }}" target="_blank" class="btn btn-outline btn-sm" style="color:#0f766e; border-color:#99f6e4; white-space:nowrap;">
                        <i class="fas fa-file-alt"></i> Lihat Penawaran
                    </a>
                    @elseif($req->status_event !== 'dibatalkan')
                    <a href="{{ route('admin.proposals.builder', ['event_id' => $req->id]) }}" class="btn btn-outline btn-sm" style="color:#0f766e; border-color:#99f6e4; white-space:nowrap;">
                        <i class="fas fa-file-alt"></i> Buat Penawaran
                    </a>
                    @else
                    <span style="color:#94a3b8; font-size:13px;">Tidak tersedia</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Belum ada request.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($requests->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
