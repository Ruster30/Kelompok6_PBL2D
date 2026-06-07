{{-- resources/views/client/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — ALPHA.COM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    @stack('styles')
</head>
<body>
<div class="dash-wrap">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('client.dashboard') }}" class="sidebar-logo">
            <span class="sidebar-logo-text">ALPHA<span>.</span>COM</span>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ route('client.dashboard') }}"
               class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Ringkasan Saya
            </a>
            <a href="{{ route('client.events') }}"
               class="nav-item {{ request()->routeIs('client.events*') ? 'active' : '' }}">
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

            <div style="height:1px; background:var(--border); margin:12px 4px;"></div>

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

    {{-- ===== MAIN AREA ===== --}}
    <div class="main-area">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="d-flex align-center gap-3">
                {{-- Mobile toggle --}}
                <button class="btn btn-outline btn-sm d-none" id="sidebarToggle" style="padding:6px 10px;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="topbar-title">@yield('page-title')</span>
            </div>
            <div class="topbar-right">
                <div class="user-badge">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name ?? 'Klien' }}</div>
                        <div class="user-role">Klien Premium</div>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="page-content">
            @if(session('success'))
            <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
// Mobile sidebar toggle
const toggle  = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
if (toggle) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', e => {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}
// Show toggle on mobile
function checkMobile() {
    if (window.innerWidth <= 768) toggle && toggle.classList.remove('d-none');
    else toggle && toggle.classList.add('d-none');
}
checkMobile();
window.addEventListener('resize', checkMobile);
</script>
@stack('scripts')
</body>
</html>