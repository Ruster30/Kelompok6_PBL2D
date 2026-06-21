@extends('layouts.admin')

@section('title', 'Kelola Event')
@section('page-title', 'Kelola Event')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Kelola Event</h1>
    </div>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Event Baru
    </a>
</div>

<div class="card">
    <div class="card-header" style="border-bottom:none; padding-bottom:14px;">
        <div class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari event..." value="{{ request('search') }}">
            </div>
            <select class="select-filter" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status')=='menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="diproses" {{ request('status')=='diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="berjalan" {{ request('status')=='berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ request('status')=='dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button class="btn btn-outline" id="filterBtn"><i class="fas fa-filter"></i> Filter Lainnya</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Event</th>
                <th>Klien</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Jumlah Tamu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr>
                <td style="font-weight:500;">{{ $event->nama_event }}</td>
                <td>{{ $event->client->name ?? '-' }}</td>
                <td>{{ $event->tanggal_event ? $event->tanggal_event->format('d M Y') : '-' }}</td>
                <td>{{ $event->lokasi_event ?? '-' }}</td>
                <td>{{ number_format($event->jumlah_tamu ?? 0, 0, ',', '.') }}</td>
                <td>
                    @php
                        $statusMap = [
                            'menunggu' => 'badge-pending',
                            'diproses' => 'badge-done',
                            'berjalan' => 'badge-active',
                            'selesai' => 'badge-done',
                            'dibatalkan' => 'badge-cancel',
                        ];
                        $labelMap = [
                            'menunggu' => 'Menunggu',
                            'diproses' => 'Diproses',
                            'berjalan' => 'Berjalan',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ];
                        $status = strtolower($event->status_event);
                        $cls = $statusMap[$status] ?? 'badge-pending';
                    @endphp
                    <span class="badge {{ $cls }}">{{ $labelMap[$status] ?? ucfirst($status) }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.events.show', $event->id) }}" class="action-btn" title="Lihat">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="action-btn" title="Edit">
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </a>
                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Hapus event ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="7">Belum ada event.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($events->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Live search + filter
    document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));
    document.getElementById('statusFilter').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        window.location.href = `{{ route('admin.events.index') }}?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
    }

    function debounce(fn, delay) {
        let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
    }
</script>
@endpush
