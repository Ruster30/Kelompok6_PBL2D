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
            <select class="select-filter" name="status" onchange="saveScrollAndSubmit(this.form)">
                <option value="">Semua Status</option>
                <option value="menunggu"   @selected(request('status') === 'menunggu')>Menunggu</option>
                <option value="diproses"   @selected(request('status') === 'diproses')>Diterima</option>
                <option value="berjalan"   @selected(request('status') === 'berjalan')>Berjalan</option>
                <option value="selesai"    @selected(request('status') === 'selesai')>Selesai</option>
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
            $proposal = $req->latestProposal;

            $hasProposal = $proposal !== null;
            $hasNegotiation = $req->negotiations->isNotEmpty();

            if ($proposal) {

                $status = $proposal->status;

                $map = [
                    'menunggu_konfirmasi' => 'badge-pending',
                    'negosiasi'           => 'badge-warning',
                    'direvisi'            => 'badge-purple',
                    'diterima'            => 'badge-success',
                    'ditolak'             => 'badge-danger',
                ];

                $labels = [
                    'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                    'negosiasi'           => 'Negosiasi Diajukan',
                    'direvisi'            => 'Penawaran Direvisi',
                    'diterima'            => 'Diterima',
                    'ditolak'             => 'Ditolak',
                ];

            } else {

                $status = $req->status_event;

                $map = [
                    'menunggu' => 'badge-pending',
                    'diproses' => 'badge-active',
                    'berjalan' => 'badge-done',
                    'selesai' => 'badge-active',
                    'dibatalkan' => 'badge-cancel',
                ];

                $labels = [
                    'menunggu' => 'Menunggu',
                    'diproses' => 'Diproses',
                    'berjalan' => 'Berjalan',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ];
            }
        @endphp
            <tr>
                <td style="font-weight:600;">{{ $req->client->name ?? '-' }}</td>
                <td style="font-weight:500;">{{ $req->nama_event }}</td>
                <td>{{ $req->jenis_event ?? '-' }}</td>
                <td>{{ $req->tanggal_event?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ $req->lokasi_event ?? '-' }}</td>
                <td>{{ $req->rentang_anggaran ?? '-' }}</td>
                <td>
                    <span class="badge {{ $map[$status] ?? 'badge-pending' }}">
                        {{ $labels[$status] ?? ucfirst($status) }}
                    </span>
                </td>
                <td>
                    <div class="action-btns" style="flex-wrap:wrap; gap:6px;">

                        {{-- Tombol Negosiasi (muncul jika ada negosiasi dari client) --}}
                        @if($hasNegotiation)
                        <a href="{{ route('admin.requests.negosiasi', $req->id) }}"
                           class="btn btn-sm"
                           style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; white-space:nowrap;">
                            <i class="fas fa-comments"></i> Lihat Negosiasi
                        </a>
                        @endif

                        {{-- Tombol Surat Penawaran --}}
                        @if($req->status_event !== 'dibatalkan')
                            @if($hasProposal)
                            {{-- Sudah punya penawaran: tampilkan dua tombol --}}
                            <a href="{{ route('admin.requests.surat-penawaran', $req->id) }}"
                               class="btn btn-sm"
                               style="background:#f0fdf9; color:#0f766e; border:1px solid #99f6e4; white-space:nowrap;">
                                <i class="fas fa-file-alt"></i> Lihat Penawaran
                            </a>
                            @else
                            {{-- Belum punya penawaran --}}
                            <a href="{{ route('admin.requests.surat-penawaran', $req->id) }}"
                               class="btn btn-sm btn-primary" style="white-space:nowrap;">
                                <i class="fas fa-file-alt"></i> Buat Penawaran
                            </a>
                            @endif
                        @else
                        <span style="color:#94a3b8; font-size:13px;">Tidak tersedia</span>
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
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">{{ $requests->links() }}</div>
    @endif
</div>
<script>
function saveScrollAndSubmit(form) {
    var nav = document.getElementById('sidebarNav');
    if (nav) {
        sessionStorage.setItem('adminSidebarScrollPosition', nav.scrollTop);
    }
    form.submit();
}
</script>
@endsection
