<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ALPHA.COM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <style>
        /* Additional responsive fixes */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        
        /* Ensure sidebar works properly on mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
            }
            
            #sidebarToggle {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border: none;
                background: #f8fafc;
                color: #64748b;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s;
            }
            
            #sidebarToggle:hover {
                background: #f1f5f9;
                color: #2DD4BF;
            }
            
            #sidebarToggle i {
                font-size: 20px;
            }
        }
        
        @media (min-width: 769px) {
            #sidebarToggle {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="dash-wrap">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('client.dashboard') }}" class="sidebar-logo">
            <span class="sidebar-logo-text">ALPHA<span>.</span>CORP</span>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-label">Menu</div>
                <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Ringkasan Saya
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Event Saya</div>
                <a href="{{ route('client.events') }}" class="nav-item {{ request()->routeIs('client.events') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Event Terdaftar
                </a>
                <a href="{{ route('client.timeline') }}" class="nav-item {{ request()->routeIs('client.timeline*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i> Timeline Event
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Keuangan</div>
                <a href="{{ route('client.invoices') }}" class="nav-item {{ request()->routeIs('client.invoices*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Tagihan & Pembayaran
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Dokumen</div>
                <a href="{{ route('client.proposals') }}" class="nav-item {{ request()->routeIs('client.proposals*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Dokumen
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Komunikasi</div>
                <a href="{{ route('client.notifications') }}" class="nav-item {{ request()->routeIs('client.notifications*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifikasi
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="sidebar-notification-badge">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Pengajuan</div>
                <a href="{{ route('client.event.create') }}" class="nav-item {{ request()->routeIs('client.event.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Ajukan Event Baru
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Akun</div>
                <a href="{{ route('client.settings') }}" class="nav-item {{ request()->routeIs('client.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Pengaturan Akun
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" onclick="confirmLogout(event)" class="nav-item danger" style="width:100%;">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ══════ MAIN AREA ══════ --}}
    <div class="main-area">

        {{-- Topbar --}}
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button id="sidebarToggle" class="topbar-notif-link"
                        style="display:none;padding:6px 10px;">
                    <i class="bi bi-list" style="font-size:18px;"></i>
                </button>
                <span class="topbar-title">@yield('page-title')</span>
            </div>

            <div class="topbar-right">
                <div class="topbar-notif">
                    <a href="{{ route('client.notifications') }}"
                    class="topbar-notif-link">
                        <i class="bi bi-bell-fill"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="notif-count">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <div class="user-badge">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Klien</div>
                    </div>
                    <div class="user-avatar">
                        {{ Auth::user()->initials }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="page-content">
            @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;
                        padding:12px 16px;border-radius:8px;margin-bottom:20px;
                        font-size:13.5px;display:flex;align-items:center;gap:8px;" id="success-alert">
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
            <script>
                    document.addEventListener('DOMContentLoaded', function () {

                        const alert = document.getElementById('success-alert');

                        if (alert) {
                            setTimeout(() => {
                                alert.style.transition = "opacity .3s ease";
                                alert.style.opacity = "0";

                                setTimeout(() => {
                                    alert.remove();
                                }, 300);
                            }, 2000);
                        }

                    });
            </script>
        </main>
    </div>
</div>

<script>
// Sidebar responsive toggle
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('sidebarToggle');
    
    if (toggleBtn && sidebar && overlay) {
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        
        overlay.addEventListener('click', closeSidebar);
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
        
        // Close sidebar when clicking nav links on mobile
        var navLinks = sidebar.querySelectorAll('.nav-item');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });
        
        // Reset sidebar state on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    }
});
</script>
<x-swal-helper />
<x-logout-confirmation />
@stack('scripts')
</body>
</html>
