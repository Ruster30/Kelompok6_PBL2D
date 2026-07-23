<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') ALPHA.COM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">

    <style>
        /* ---- Empty State ---- */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
        }
        .empty-state-icon {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #475569;
            margin: 0 0 8px 0;
        }
        .empty-state-text {
            font-size: 14px;
            color: #94a3b8;
            max-width: 360px;
            margin: 0;
            line-height: 1.5;
        }
        .empty-row td {
            padding: 40px 20px !important;
            text-align: center !important;
            color: #94a3b8 !important;
            font-size: 14px !important;
        }
        /* ---- Responsive Tables ---- */
        table:not(.table-no-responsive) {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
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
            div[style*='grid-template-columns:repeat(4,1fr)'],
            div[style*='grid-template-columns: repeat(4, 1fr)'] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .topbar {
                padding: 0 16px;
            }
            .topbar-title {
                font-size: 15px;
            }
            .topbar-user span {
                display: none;
            }
            .avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
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
        
        @media (max-width: 575px) {
            .topbar {
                padding: 0 16px !important;
            }
            .topbar-title {
                font-size: 14px !important;
            }
            .page-content {
                padding: 16px 12px;
            }
        }
        
        @media (max-width: 480px) {
            .topbar {
                padding: 0 16px;
            }
            .page-content {
                padding: 16px 12px;
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

        <nav class="sidebar-nav" id="sidebarNav">
            <div class="nav-section">
                <div class="nav-section-label">Menu</div>
                <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2" aria-hidden="true"></i> Ringkasan Saya
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Event Saya</div>
                <a href="{{ route('client.events') }}" class="nav-item {{ request()->routeIs('client.events') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event" aria-hidden="true"></i> Event Terdaftar
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
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i> Dokumen
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-label">Komunikasi</div>
                <a href="{{ route('client.notifications') }}" class="nav-item {{ request()->routeIs('client.notifications*') ? 'active' : '' }}">
                    <i class="bi bi-bell" aria-hidden="true"></i> Notifikasi
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
                    <i class="bi bi-gear" aria-hidden="true"></i> Pengaturan Akun
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" onclick="confirmLogout(event)" class="nav-item danger" style="width:100%;">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Keluar
                </button>
            </form>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{--  --}}
    <div class="main-area">
        
        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="topbar-right">
                <x-dark-mode-toggle />
                <a href="{{ route('client.notifications') }}" class="topbar-notif">
                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="notif-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
                <div class="topbar-user">
                    <span>{{ Auth::user()->name ?? 'client' }}</span>
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'CL', 0, 2)) }}</div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="page-content">
            <x-alert type="success" />
            <x-alert type="error" />
            <x-alert-dismiss />

            @yield('content')
            
        </main>
    </div>
</div>

<x-sidebar-script storageKey="clientSidebarScrollPosition" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/skeleton.js') }}"></script>
<x-swal-helper />
<x-logout-confirmation />
@stack('scripts')
</body>
</html>