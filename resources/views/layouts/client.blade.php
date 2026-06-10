<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ALPHA.COM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    @stack('styles')
</head>
<body>
<div class="dash-wrap">

    {{-- ══════ SIDEBAR ══════ --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('client.dashboard') }}" class="sidebar-logo">
            <span class="sidebar-logo-text">ALPHA<span>.</span>CORP</span>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ route('client.dashboard') }}"
               class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Ringkasan Saya
            </a>
            <a href="{{ route('client.events') }}"
               class="nav-item {{ request()->routeIs('client.events') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> Event Terdaftar
            </a>
            <a href="{{ route('client.timeline') }}"
               class="nav-item {{ request()->routeIs('client.timeline*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Timeline Event
            </a>
            <a href="{{ route('client.invoices') }}"
               class="nav-item {{ request()->routeIs('client.invoices*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Anggaran & Faktur
            </a>
            <a href="{{ route('client.proposals') }}"
               class="nav-item {{ request()->routeIs('client.proposals*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Surat Penawaran
            </a>

            <div style="height:1px;background:var(--border);margin:12px 4px;"></div>

            <a href="{{ route('client.event.create') }}"
               class="nav-item {{ request()->routeIs('client.event.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Ajukan Event Baru
            </a>
            <a href="{{ route('client.settings') }}"
               class="nav-item {{ request()->routeIs('client.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan Akun
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item danger" style="width:100%;">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════ MAIN AREA ══════ --}}
    <div class="main-area">

        {{-- Topbar --}}
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button id="sidebarToggle" class="btn btn-outline btn-sm"
                        style="display:none;padding:6px 10px;">
                    <i class="bi bi-list" style="font-size:18px;"></i>
                </button>
                <span class="topbar-title">@yield('page-title')</span>
            </div>

            <div class="topbar-right">
                {{-- Notifikasi Bell --}}
                <div style="position:relative;">
                    <button onclick="toggleNotif(this)" class="btn btn-outline btn-sm"
                            style="padding:7px 10px;">
                        <i class="bi bi-bell"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                        <span style="position:absolute;top:-5px;right:-5px;background:#ef4444;
                              color:#fff;font-size:10px;font-weight:700;border-radius:999px;
                              min-width:17px;height:17px;display:flex;align-items:center;
                              justify-content:center;padding:0 3px;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown Notifikasi --}}
                    <div id="notifDropdown"
                         style="display:none;position:absolute;right:0;top:48px;width:320px;
                                background:#fff;border:1px solid var(--border);border-radius:12px;
                                box-shadow:var(--shadow-lg);z-index:300;overflow:hidden;">
                        <div style="padding:14px 16px;border-bottom:1px solid var(--border);
                                    display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-weight:700;font-size:14px;color:var(--dark);">Notifikasi</span>
                            <form method="POST" action="{{ route('client.notif.read') }}" style="margin:0;">
                                @csrf
                                <button type="submit"
                                        style="background:none;border:none;font-size:11px;
                                               color:var(--accent);font-weight:600;cursor:pointer;
                                               padding:0;">
                                    Tandai semua dibaca
                                </button>
                            </form>
                        </div>
                        <div style="max-height:300px;overflow-y:auto;">
                            @forelse($notifications ?? [] as $notif)
                            <div style="padding:12px 16px;border-bottom:1px solid var(--border);
                                        background:{{ $notif->dibaca ? '#fff' : '#f0fbf9' }};">
                                <div style="font-size:13px;
                                            font-weight:{{ $notif->dibaca ? '500' : '700' }};
                                            color:var(--dark);margin-bottom:3px;">
                                    {{ $notif->judul }}
                                </div>
                                <div style="font-size:12px;color:var(--text-muted);line-height:1.5;">
                                    {{ $notif->pesan }}
                                </div>
                                <div style="font-size:11px;color:var(--text-light);margin-top:4px;">
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @empty
                            <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:13px;">
                                Tidak ada notifikasi
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Badge --}}
                <div class="user-badge">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Klien</div>
                    </div>
                    <div class="user-avatar">{{ Auth::user()->initials }}</div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="page-content">
            @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;
                        padding:12px 16px;border-radius:8px;margin-bottom:20px;
                        font-size:13.5px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;
                        padding:12px 16px;border-radius:8px;margin-bottom:20px;
                        font-size:13.5px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
// Sidebar mobile
const toggle  = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
function checkMobile() {
    toggle.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
    if (window.innerWidth > 768) sidebar.classList.remove('open');
}
toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
document.addEventListener('click', e => {
    if (!sidebar.contains(e.target) && !toggle.contains(e.target))
        sidebar.classList.remove('open');
});
window.addEventListener('resize', checkMobile);
checkMobile();

// Notifikasi dropdown
function toggleNotif(btn) {
    const d = document.getElementById('notifDropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', e => {
    if (!e.target.closest('#notifDropdown') && !e.target.closest('[onclick^="toggleNotif"]'))
        document.getElementById('notifDropdown').style.display = 'none';
});
</script>
@stack('scripts')
</body>
</html>