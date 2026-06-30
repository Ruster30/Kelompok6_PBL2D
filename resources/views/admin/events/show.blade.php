@extends('layouts.admin')

@section('title', 'Detail Event')
@section('page-title', 'Kelola Event')

@section('content')

@php
    $statusMap = [
        'menunggu'   => 'badge-pending',
        'diproses'   => 'badge-done',
        'berjalan'   => 'badge-active',
        'selesai'    => 'badge-done',
        'dibatalkan' => 'badge-cancel',
    ];
    $labelMap = [
        'menunggu'   => 'Menunggu',
        'diproses'   => 'Diproses',
        'berjalan'   => 'Berjalan',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $status   = strtolower($event->status_event);
    $cls      = $statusMap[$status] ?? 'badge-pending';
    $label    = $labelMap[$status] ?? ucfirst($status);

    // Feedback terbaru (jika ada lebih dari satu, ambil yang paling baru)
    $feedback = $event->feedbacks->first();
@endphp

<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>{{ $event->nama_event }}</h1>
        <p>Detail informasi event dan evaluasi dari klien.</p>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="event-detail-grid">

    {{-- ══════════════════════════════════════════════
         KOLOM KIRI — Informasi Event
    ══════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-calendar-day" style="color:#14b8a6; margin-right:8px;"></i>Informasi Event</span>
            <span class="badge {{ $cls }}">{{ $label }}</span>
        </div>

        <div class="event-info-body">
            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-bookmark"></i></div>
                <div>
                    <div class="event-info-label">Nama Event</div>
                    <div class="event-info-value">{{ $event->nama_event }}</div>
                </div>
            </div>

            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-user"></i></div>
                <div>
                    <div class="event-info-label">Client</div>
                    <div class="event-info-value">{{ $event->client->name ?? '-' }}</div>
                </div>
            </div>

            @if($event->jenis_event)
            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-tag"></i></div>
                <div>
                    <div class="event-info-label">Jenis Kegiatan</div>
                    <div class="event-info-value">{{ $event->jenis_event }}</div>
                </div>
            </div>
            @endif

            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="event-info-label">Tanggal</div>
                    <div class="event-info-value">
                        {{ \Carbon\Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') }}
                        @if($event->tanggal_selesai ?? null)
                            &nbsp;s/d&nbsp;{{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <div class="event-info-label">Lokasi</div>
                    <div class="event-info-value">{{ $event->lokasi_event ?? '-' }}</div>
                </div>
            </div>

            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="event-info-label">Jumlah Tamu</div>
                    <div class="event-info-value">{{ $event->jumlah_tamu ? number_format($event->jumlah_tamu,0,',','.') . ' Orang' : '-' }}</div>
                </div>
            </div>

            @if($event->rentang_anggaran)
            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-wallet"></i></div>
                <div>
                    <div class="event-info-label">Budget</div>
                    <div class="event-info-value" style="color:#14b8a6; font-weight:700;">{{ $event->rentang_anggaran }}</div>
                </div>
            </div>
            @endif

            @if($event->detail_kebutuhan)
            <div class="event-info-row">
                <div class="event-info-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <div class="event-info-label">Detail Kebutuhan</div>
                    <div class="event-info-value" style="font-weight:400;">{{ $event->detail_kebutuhan }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         KOLOM KANAN — Feedback Klien
    ══════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-comment-dots" style="color:#14b8a6; margin-right:8px;"></i>Feedback Klien</span>
            @if($feedback)
            <span class="badge badge-active"><i class="fas fa-check-circle" style="margin-right:4px;"></i>Feedback Diterima</span>
            @endif
        </div>

        @if($feedback)
        <div class="feedback-body">
            {{-- Rating bintang --}}
            <div class="feedback-rating-wrap">
                <div class="feedback-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($feedback->rating) ? 'star-filled' : 'star-empty' }}"></i>
                    @endfor
                </div>
                <div class="feedback-rating-number">{{ number_format($feedback->rating, 1) }}<span>/5</span></div>
            </div>

            <div class="feedback-divider"></div>

            {{-- Identitas pemberi feedback --}}
            <div class="feedback-client-row">
                <div class="feedback-client-avatar">
                    {{ strtoupper(substr($feedback->client->name ?? 'K', 0, 1)) }}
                </div>
                <div>
                    <div class="feedback-client-name">{{ $feedback->client->name ?? 'Client' }}</div>
                    <div class="feedback-client-date">
                        <i class="far fa-clock"></i>
                        {{ \Carbon\Carbon::parse($feedback->created_at)->translatedFormat('d F Y, H:i') }}
                    </div>
                </div>
            </div>

            {{-- Komentar --}}
            @if($feedback->ulasan)
            <div class="feedback-comment">
                <i class="fas fa-quote-left feedback-quote-icon"></i>
                <p>{{ $feedback->ulasan }}</p>
            </div>
            @endif

            {{-- Info jika ada riwayat feedback lain sebelumnya --}}
            @if($event->feedbacks->count() > 1)
            <div class="feedback-more-note">
                <i class="fas fa-info-circle"></i>
                Menampilkan feedback terbaru dari {{ $event->feedbacks->count() }} feedback yang diterima.
            </div>
            @endif
        </div>
        @else
        {{-- Empty state --}}
        <div class="feedback-empty">
            <div class="feedback-empty-icon">
                <i class="far fa-comment-dots"></i>
            </div>
            <h3>Belum Ada Feedback</h3>
            <p>Klien belum memberikan evaluasi untuk event ini. Feedback akan tampil di sini setelah klien mengisi formulir penilaian.</p>
        </div>
        @endif
    </div>

</div>{{-- /event-detail-grid --}}

@endsection

@push('styles')
<style>
/* ── Layout 2 kolom ── */
.event-detail-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) {
    .event-detail-grid { grid-template-columns: 1fr; }
}

/* ── Kolom kiri: Informasi Event ── */
.event-info-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 18px; }
.event-info-row { display: flex; align-items: flex-start; gap: 14px; }
.event-info-icon {
    width: 36px; height: 36px; min-width: 36px; border-radius: 9px;
    background: #f0fdf9; color: #14b8a6;
    display: flex; align-items: center; justify-content: center; font-size: 14px;
}
.event-info-label {
    font-size: 11.5px; font-weight: 600; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px;
}
.event-info-value { font-size: 14.5px; font-weight: 600; color: #0f172a; line-height: 1.5; }

/* ── Kolom kanan: Feedback Klien ── */
.feedback-body { padding: 22px; }

.feedback-rating-wrap { text-align: center; padding: 4px 0 6px; }
.feedback-stars { font-size: 26px; letter-spacing: 4px; margin-bottom: 8px; }
.star-filled { color: #f59e0b; }
.star-empty  { color: #e2e8f0; }
.feedback-rating-number {
    font-size: 28px; font-weight: 800; color: #0f172a;
}
.feedback-rating-number span { font-size: 15px; font-weight: 600; color: #94a3b8; }

.feedback-divider { height: 1px; background: #e2e8f0; margin: 18px 0; }

.feedback-client-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.feedback-client-avatar {
    width: 42px; height: 42px; min-width: 42px; border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0f9488);
    color: white; font-weight: 700; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
}
.feedback-client-name { font-size: 14.5px; font-weight: 700; color: #0f172a; }
.feedback-client-date {
    font-size: 12px; color: #94a3b8; margin-top: 2px;
    display: flex; align-items: center; gap: 5px;
}

.feedback-comment {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 16px 18px; position: relative;
}
.feedback-quote-icon { color: #cbd5e1; font-size: 14px; margin-bottom: 6px; display: block; }
.feedback-comment p {
    font-size: 13.5px; color: #334155; line-height: 1.7; font-style: italic;
    margin: 0;
}

.feedback-more-note {
    margin-top: 14px; font-size: 12px; color: #94a3b8;
    display: flex; align-items: center; gap: 6px;
}

/* ── Empty state Feedback ── */
.feedback-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 50px 24px; text-align: center; gap: 10px;
}
.feedback-empty-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: #f0fdf9; color: #5eead4;
    display: flex; align-items: center; justify-content: center; font-size: 26px;
    margin-bottom: 6px;
}
.feedback-empty h3 { font-size: 15px; font-weight: 700; color: #475569; }
.feedback-empty p { font-size: 13px; color: #94a3b8; max-width: 260px; line-height: 1.6; }
</style>
@endpush