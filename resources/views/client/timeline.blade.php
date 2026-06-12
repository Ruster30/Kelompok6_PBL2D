@extends('layouts.client')
@section('title','Timeline Event')
@section('page-title','Timeline Event')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:4px;">Timeline Event</h1>
    <p style="color:var(--text-muted);">Pantau progress persiapan event Anda.</p>
</div>

{{-- Selector event jika lebih dari 1 --}}
@if($myEvents->count() > 1)
<div style="margin-bottom:20px;">
    <select class="form-select-styled"
            onchange="window.location='{{ url('client/timeline') }}/'+this.value">
        @foreach($myEvents as $e)
        <option value="{{ $e->id }}" {{ $selected?->id == $e->id ? 'selected' : '' }}>
            {{ $e->nama_event }}
        </option>
        @endforeach
    </select>
</div>
@endif

@if(!$selected)
<div class="card">
    <div class="empty-state">
        <i class="bi bi-calendar3"></i>
        <h4>Belum Ada Event</h4>
        <p>Anda belum memiliki event yang terdaftar.</p>
        <a href="{{ route('client.event.create') }}" class="btn btn-accent" style="margin-top:16px;">
            Ajukan Event Baru
        </a>
    </div>
</div>
@else

{{-- Total Progress --}}
<div class="total-progress-card">
    <div class="total-progress-hdr">
        <span>Total Progress</span>
        <span class="total-progress-pct">{{ $progress }}%</span>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width:{{ $progress }}%"></div>
    </div>
    <div class="total-progress-sub">{{ $doneTask }} dari {{ $totalTask }} kegiatan selesai</div>
</div>

{{-- Timeline List --}}
<div class="timeline-wrap">
    @if($timelines->isEmpty())
    <div class="empty-state">
        <i class="bi bi-list-task"></i>
        <h4>Timeline Kosong</h4>
        <p>Timeline untuk event ini belum tersedia. Tim kami akan segera menyusunnya.</p>
    </div>
    @else
    <div class="timeline-list">
        @foreach($timelines as $tl)
        <div class="timeline-item {{ $tl->isDone() ? 'done' : ($tl->isBerjalan() ? 'active' : '') }}">
            <div class="timeline-dot"></div>
            <div class="timeline-item-inner">
                <div class="timeline-item-hdr">
                    <span class="timeline-item-name">{{ $tl->nama_kegiatan }}</span>
                    <span class="timeline-item-date">
                        {{ $tl->tanggal_kegiatan->isoFormat('D MMM Y') }}
                    </span>
                </div>
                <div style="margin-top:6px;">
                    <span class="badge {{ $tl->badge_class }}" style="font-size:10px;">
                        @if($tl->isDone())
                            <i class="bi bi-check-circle-fill" style="margin-right:3px;"></i>
                        @elseif($tl->isBerjalan())
                            <i class="bi bi-arrow-right-circle-fill" style="margin-right:3px;"></i>
                        @else
                            <i class="bi bi-clock" style="margin-right:3px;"></i>
                        @endif
                        {{ $tl->status_label }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endif
@endsection

@push('styles')
<style>
.timeline-item.active .timeline-dot {
    background: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,.2);
}
.timeline-item.active .timeline-item-inner {
    border-color: rgba(245,158,11,.3);
    background: #fffbeb;
}
</style>
@endpush