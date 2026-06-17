@extends('layouts.vendor')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')

<div class="section-card" style="padding:0; overflow:hidden;">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between" style="padding:20px 24px 16px; border-bottom:1px solid #f1f5f9;">
        <div>
            <h2 style="font-size:16px; font-weight:600; color:#1a2332; margin:0 0 2px;">Semua Notifikasi</h2>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span style="font-size:12px; color:#8a9bb0;">{{ $unreadCount }} belum dibaca</span>
            @endif
        </div>
        @if(isset($unreadCount) && $unreadCount > 0)
            <form action="{{ route('vendor.notifikasi.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="action-btn action-btn-teal" style="padding:7px 14px;">
                    <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- NOTIF LIST -->
    @forelse($notifikasi ?? [] as $notif)
        <div class="notif-item {{ !$notif->dibaca ? 'unread' : '' }}">
            <div class="notif-icon-wrap"
                style="background: {{ $notif->tipe === 'tugas' ? '#e6f5f3' : ($notif->type === 'event' ? '#dbeafe' : '#fef9c3') }}">
                <i class="bi bi-{{ $notif->tipe === 'tugas' ? 'check2-square' : ($notif->type === 'event' ? 'calendar3-event' : 'bell') }}"
                   style="color: {{ $notif->tiype === 'tugas' ? '#1a8f7e' : ($notif->type === 'event' ? '#2563eb' : '#ca8a04') }}; font-size:16px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="notif-title">{{ $notif->judul }}</div>
                <div class="notif-body">{{ $notif->isi }}</div>
                <div class="notif-time">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
            </div>
            @if(!$notif->dibaca)
                <div class="notif-unread-dot"></div>
            @endif
        </div>
    @empty
        {{-- Demo static notifications --}}
        @php
            $demoNotif = [
                ['icon'=>'check2-square','icon_color'=>'#1a8f7e','bg'=>'#e6f5f3','title'=>'Tugas Baru Ditugaskan','body'=>'Anda mendapat tugas baru: katering untuk event Konser Feast.','time'=>'2 jam lalu','unread'=>true],
                ['icon'=>'calendar3-event','icon_color'=>'#2563eb','bg'=>'#dbeafe','title'=>'Event Mendatang','body'=>'Konser Feast akan berlangsung pada 11 Juni 2026 di Gor Hj. Agus Salim.','time'=>'1 hari lalu','unread'=>true],
                ['icon'=>'exclamation-triangle','icon_color'=>'#ca8a04','bg'=>'#fef9c3','title'=>'Deadline Mendekat','body'=>'Deadline tugas katering adalah 13/6/2026. Harap segera selesaikan.','time'=>'2 hari lalu','unread'=>false],
                ['icon'=>'bell','icon_color'=>'#8a9bb0','bg'=>'#f1f5f9','title'=>'Pengingat Jadwal','body'=>'Gladi resik akan dilaksanakan pada 10 Juni 2026.','time'=>'3 hari lalu','unread'=>false],
            ];
        @endphp

        @foreach($demoNotif as $notif)
            <div class="notif-item {{ $notif['unread'] ? 'unread' : '' }}">
                <div class="notif-icon-wrap" style="background: {{ $notif['bg'] }}">
                    <i class="bi bi-{{ $notif['icon'] }}" style="color: {{ $notif['icon_color'] }}; font-size:16px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="notif-title">{{ $notif['title'] }}</div>
                    <div class="notif-body">{{ $notif['body'] }}</div>
                    <div class="notif-time">{{ $notif['time'] }}</div>
                </div>
                @if($notif['unread'])
                    <div class="notif-unread-dot"></div>
                @endif
            </div>
        @endforeach

        <div class="text-center py-3" style="font-size:13px; color:#b0bec5; border-top:1px solid #f1f5f9;">
            Tidak ada notifikasi lainnya
        </div>
    @endforelse

</div>

@endsection
