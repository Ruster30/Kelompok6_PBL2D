@extends('vendor.layouts.app')

@section('title', 'Jadwal')
@section('page-title', 'Jadwal')

@section('content')

<div class="section-card">

    <!-- HEADER -->
    <div class="d-flex align-items-start justify-content-between mb-4">
        <div>
            <h2 style="font-size:18px; font-weight:600; color:#1a2332; margin:0 0 4px;">Jadwal Event</h2>
            <p style="font-size:13px; color:#8a9bb0; margin:0;">Pantau progress event yang Anda kerjakan (mode hanya-lihat).</p>
        </div>
        @if(isset($events) && $events->count() > 1)
            <div class="search-wrapper" style="width:220px;">
                <i class="bi bi-search"></i>
                <select class="search-input" id="eventFilter" onchange="filterEvent()" style="padding-left:36px; cursor:pointer;">
                    @foreach($events as $e)
                        <option value="{{ $e->id }}" {{ $selectedEvent == $e->id ? 'selected' : '' }}>
                            {{ $e->nama_event }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- TIMELINE -->
    <div class="timeline-wrapper">
        <div class="timeline-line"></div>

        @forelse($jadwal ?? [] as $item)
            @php
                $now = \Carbon\Carbon::now();
                $tgl = \Carbon\Carbon::parse($item->tanggal);
                $dotClass = $tgl->isPast() ? 'done' : ($tgl->isToday() ? 'active' : '');
            @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $dotClass }}"></div>
                <div class="timeline-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-semibold" style="font-size:14px; color:#1a2332;">{{ $item->nama }}</div>
                        <span class="timeline-date">{{ \Carbon\Carbon::parse($item->tanggal)->format('j M Y') }}</span>
                    </div>
                    @if($item->deskripsi)
                        <div style="font-size:13px; color:#64748b; margin-top:6px;">{{ $item->deskripsi }}</div>
                    @endif
                </div>
            </div>
        @empty
            {{-- Demo static timeline --}}
            @php
                $demoJadwal = [
                    ['nama' => 'Persiapan Event',      'tgl' => '12 Apr 2026', 'desc' => 'Tahap awal koordinasi internal, perencanaan konsep, dan finalisasi briefing event.', 'dot' => 'done'],
                    ['nama' => 'Konfirmasi Vendor',    'tgl' => '27 Apr 2026', 'desc' => 'Mengonfirmasi seluruh vendor pendukung event: katering, dekorasi, sound system, dll.', 'dot' => 'done'],
                    ['nama' => 'Setup Venue',          'tgl' => '8 Jun 2026',  'desc' => 'Persiapan lokasi event, instalasi dekorasi, dan setup teknis.', 'dot' => 'done'],
                    ['nama' => 'Gladi Resik',          'tgl' => '10 Jun 2026', 'desc' => 'Rehearsal akhir untuk memastikan semua rangkaian acara berjalan lancar.', 'dot' => 'done'],
                    ['nama' => 'Hari Pelaksanaan',     'tgl' => '11 Jun 2026', 'desc' => 'Eksekusi acara utama sesuai rundown yang telah disepakati.', 'dot' => 'active'],
                    ['nama' => 'Dokumentasi',          'tgl' => '13 Jun 2026', 'desc' => 'Pengumpulan foto, video, dan dokumentasi acara untuk diserahkan ke klien.', 'dot' => ''],
                    ['nama' => 'Evaluasi Event',       'tgl' => '19 Jun 2026', 'desc' => 'Rapat evaluasi internal dan feedback dari klien setelah event selesai.', 'dot' => ''],
                ];
            @endphp

            @foreach($demoJadwal as $item)
                <div class="timeline-item">
                    <div class="timeline-dot {{ $item['dot'] }}"></div>
                    <div class="timeline-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold" style="font-size:14px; color:#1a2332;">{{ $item['nama'] }}</div>
                            <span class="timeline-date">{{ $item['tgl'] }}</span>
                        </div>
                        <div style="font-size:13px; color:#64748b; margin-top:6px;">{{ $item['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        @endforelse

    </div>
</div>

@endsection

@push('scripts')
<script>
function filterEvent() {
    const val = document.getElementById('eventFilter').value;
    window.location.href = '{{ route("vendor.jadwal") }}?event=' + val;
}
</script>
@endpush
