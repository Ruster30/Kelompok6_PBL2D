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
        <div class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari request..." value="{{ request('search') }}">
            </div>
            <select class="select-filter" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status')=='menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="diproses" {{ request('status')=='diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="berjalan" {{ request('status')=='berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ request('status')=='dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button class="btn btn-outline"><i class="fas fa-filter"></i> Filter Lainnya</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Client</th>
                <th>Nama Event</th>
                <th>Jenis Event</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Jumlah Tamu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
            <tr>
                <td style="font-weight:500;">{{ $req->client->name ?? '-' }}</td>
                <td>{{ $req->nama_event }}</td>
                <td>{{ $req->jenis_event ?? '-' }}</td>
                <td>{{ $req->tanggal_event ? $req->tanggal_event->format('d M Y') : '-' }}</td>
                <td>{{ $req->lokasi_event ?? '-' }}</td>
                <td>{{ number_format($req->jumlah_tamu ?? 0, 0, ',', '.') }}</td>
                <td>
                    @php
                        $map = [
                            'menunggu' => 'badge-pending',
                            'diproses' => 'badge-done',
                            'berjalan' => 'badge-active',
                            'selesai' => 'badge-done',
                            'dibatalkan' => 'badge-cancel',
                        ];
                        $labels = [
                            'menunggu' => 'Menunggu',
                            'diproses' => 'Diproses',
                            'berjalan' => 'Berjalan',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ];
                        $status = strtolower($req->status_event);
                        $cls = $map[$status] ?? 'badge-pending';
                    @endphp
                    <span class="badge {{ $cls }}">{{ $labels[$status] ?? ucfirst($status) }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.requests.show', $req->id) }}" class="action-btn" title="Lihat Detail">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        @if($req->status_event === 'menunggu')
                        <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn" title="Setujui" style="color:#16a34a; border-color:#16a34a;">
                                <i class="fas fa-check" style="font-size:12px;"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Tolak request ini?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Belum ada request.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($requests->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
