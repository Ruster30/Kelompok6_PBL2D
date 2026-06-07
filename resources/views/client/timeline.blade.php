{{-- resources/views/client/timeline.blade.php --}}
@extends('layouts.client')
@section('title', 'Timeline Event')
@section('page-title', 'Timeline Event')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px; font-weight:800; color:var(--dark); margin-bottom:4px;">Timeline Event</h1>
    <p style="color:var(--text-muted);">Pantau progress persiapan event Anda.</p>
</div>

{{-- Event Selector --}}
<div style="margin-bottom:20px;">
    <select class="form-select-styled">
        <option>Konser Feast</option>
        <option>Retret Perusahaan 2026</option>
    </select>
</div>

{{-- Total Progress --}}
<div class="total-progress-card">
    <div class="total-progress-hdr">
        <span>Total Progress</span>
        <span class="total-progress-pct">0%</span>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width:0%"></div>
    </div>
    <div class="total-progress-sub">0 dari 7 tugas selesai</div>
</div>

{{-- Timeline --}}
<div class="timeline-wrap">
    <div class="timeline-list">

        @php
        $tasks = [
            ['title' => 'Persiapan Event',    'date' => '21 Mar 2026', 'desc' => 'Tahap awal koordinasi internal, perencanaan konsep, dan finalisasi briefing event.',           'done' => false],
            ['title' => 'Konfirmasi Vendor',  'date' => '5 Apr 2026',  'desc' => 'Mengonfirmasi seluruh vendor pendukung event: katering, dekorasi, sound system, dll.',         'done' => false],
            ['title' => 'Setup Venue',        'date' => '17 Mei 2026', 'desc' => 'Persiapan lokasi event, instalasi dekorasi, dan setup teknis.',                                'done' => false],
            ['title' => 'Gladi Resik',        'date' => '19 Mei 2026', 'desc' => 'Rehearsal akhir untuk memastikan semua rangkaian acara berjalan lancar.',                      'done' => false],
            ['title' => 'Hari Pelaksanaan',   'date' => '20 Mei 2026', 'desc' => 'Eksekusi acara utama sesuai rundown yang telah disepakati.',                                   'done' => false],
            ['title' => 'Dokumentasi',        'date' => '24 Mei 2026', 'desc' => 'Pengumpulan foto, video, dan dokumentasi acara untuk diserahkan ke klien.',                    'done' => false],
            ['title' => 'Evaluasi Event',     'date' => '30 Mei 2026', 'desc' => 'Rapat evaluasi internal dan feedback dari klien setelah event selesai.',                       'done' => false],
        ];
        @endphp

        @foreach($tasks as $task)
        <div class="timeline-item {{ $task['done'] ? 'done' : '' }}">
            <div class="timeline-dot"></div>
            <div class="timeline-item-inner">
                <div class="timeline-item-hdr">
                    <span class="timeline-item-name">{{ $task['title'] }}</span>
                    <span class="timeline-item-date">{{ $task['date'] }}</span>
                </div>
                <div class="timeline-item-desc">{{ $task['desc'] }}</div>
            </div>
        </div>
        @endforeach

    </div>
</div>

@endsection