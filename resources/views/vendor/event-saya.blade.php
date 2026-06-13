@extends('vendor.layouts.app')

@section('title', 'Event Saya')
@section('page-title', 'Event Saya')

@section('content')

<div class="section-card">

    <!-- SEARCH -->
    <div class="mb-4">
        <div class="search-wrapper" style="max-width: 420px;">
            <i class="bi bi-search"></i>
            <input
                type="text"
                class="search-input"
                id="searchEvent"
                placeholder="Cari event..."
                onkeyup="filterEvent()"
            >
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table custom-table mb-0" id="eventTable">
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>PIC / Klien</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events ?? [] as $event)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="color:#1a2332;">{{ $event->nama_event }}</div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ $event->deskripsi }}</div>
                        </td>
                        <td>{{ $event->klien->nama ?? $event->pic }}</td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-calendar3" style="color:#8a9bb0; font-size:13px;"></i>
                                {{ \Carbon\Carbon::parse($event->tanggal)->format('j/n/Y') }}
                            </span>
                        </td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-geo-alt" style="color:#8a9bb0; font-size:13px;"></i>
                                {{ $event->lokasi }}
                            </span>
                        </td>
                        <td>
                            @php
                                $status = strtolower($event->status ?? 'mendatang');
                            @endphp
                            @if($status === 'mendatang')
                                <span class="badge-mendatang">Mendatang</span>
                            @elseif($status === 'berlangsung')
                                <span class="badge-berlangsung">Berlangsung</span>
                            @else
                                <span class="badge-selesai">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    {{-- Demo static row --}}
                    <tr>
                        <td>
                            <div class="fw-semibold" style="color:#1a2332;">Konser Feast</div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:2px;">
                                dnqwdlqwd0cqonlk kwqkjdpu;jcsa k0dwolyhjnqma
                            </div>
                        </td>
                        <td>daffi</td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-calendar3" style="color:#8a9bb0; font-size:13px;"></i>
                                11/6/2026
                            </span>
                        </td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-geo-alt" style="color:#8a9bb0; font-size:13px;"></i>
                                gor hj. agus salim
                            </span>
                        </td>
                        <td>
                            <span class="badge-mendatang">Mendatang</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- EMPTY STATE -->
    <div id="emptyState" class="text-center py-5 d-none">
        <i class="bi bi-calendar-x" style="font-size:40px; color:#d1d5db;"></i>
        <div class="mt-3" style="font-size:14px; color:#8a9bb0;">Tidak ada event yang ditemukan.</div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function filterEvent() {
    const input = document.getElementById('searchEvent').value.toLowerCase();
    const rows = document.querySelectorAll('#eventTable tbody tr');
    let visible = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(input)) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('emptyState').classList.toggle('d-none', visible > 0);
}
</script>
@endpush
