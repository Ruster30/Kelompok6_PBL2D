<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f6fa; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 255px; min-width: 255px; background: #0f172a; color: #94a3b8;
            display: flex; flex-direction: column; position: fixed; height: 100vh; overflow-y: auto; z-index: 100;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px; padding: 20px 20px;
            border-bottom: 1px solid #1e293b;
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px; background: #14b8a6; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: 700;
        }
        .sidebar-brand .brand-name { color: white; font-weight: 700; font-size: 16px; letter-spacing: 1px; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 11px 20px; cursor: pointer;
            color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all 0.2s; border-left: 3px solid transparent;
        }
        .sidebar-footer .nav-logout{
            width:100%;
            background:transparent;
            border:none;
            text-align:left;
            display:flex;
            align-items:center;
            gap:12px;
            color:#d1d5db;
            padding:14px 18px;
            border-radius:10px;
            transition:.25s;
            cursor:pointer;
        }
        .sidebar-footer .nav-logout:hover{
            background:#dc2626;
            color:#fff;
        }
        .sidebar-footer .nav-logout:hover i{
            color:#fff;
        }

        .nav-item:hover { background: #1e293b; color: #e2e8f0; }
        .nav-item.active { background: #134e4a; color: #2dd4bf; border-left-color: #14b8a6; }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-footer { padding: 16px 0; border-top: 1px solid #1e293b; }

        /* Main content */
        .main-wrapper { margin-left: 255px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* Topbar */
        .topbar {
            background: white; padding: 0 28px; height: 60px; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 18px; font-weight: 600; color: #1e293b; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-notif {
            position: relative; display: flex; align-items: center; justify-content: center;
            color: #94a3b8; text-decoration: none; transition: color .2s ease;
        }

        .topbar-notif:hover {
            color: #2dd4bf;
        }
        .notif-count {
            position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px;
            background: #ef4444; color: white; border-radius: 50%; border: 2px solid white;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; z-index: 10;
        }
        .topbar-notif i { font-size: 18px; }
        .topbar-user { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .topbar-user span { font-size: 14px; font-weight: 500; color: #334155; }
        .avatar {
            width: 34px; height: 34px; background: #14b8a6; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700;
        }

        /* Page content */
        .page-content { padding: 28px; flex: 1; }

        /* Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
        .stat-card {
            background: white; border-radius: 12px; padding: 22px; border: 1px solid #e2e8f0;
            display: flex; flex-direction: column; gap: 8px;
        }
        .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
        .stat-icon { font-size: 22px; color: #14b8a6; }
        .stat-badge {
            font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; background: #ccfbf1; color: #0f766e;
        }
        .stat-badge.yellow { background: #fef9c3; color: #92400e; }
        .stat-badge.blue { background: #dbeafe; color: #1e40af; }
        .stat-badge.green { background: #dcfce7; color: #166534; }
        .stat-value { font-size: 26px; font-weight: 700; color: #0f172a; }
        .stat-label { font-size: 13px; color: #64748b; }

        /* Plain stat card (no icon, used in Vendor & Klien) */
        .plain-stat { background: white; border-radius: 12px; padding: 20px 22px; border: 1px solid #e2e8f0; }
        .plain-stat-label { font-size: 13px; color: #64748b; margin-bottom: 6px; }
        .plain-stat-value { font-size: 24px; font-weight: 700; color: #0f172a; }

        /* Table */
        .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header {
            padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }
        .card-title { font-size: 16px; font-weight: 600; color: #0f172a; }
        .card-link { font-size: 13px; color: #14b8a6; text-decoration: none; font-weight: 500; }
        .card-link:hover { text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid #f1f5f9; }
        thead th {
            padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 600;
            color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase;
        }
        tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 14px 24px; font-size: 14px; color: #334155; }
        .empty-row td { text-align: center; color: #94a3b8; padding: 40px; font-size: 14px; }

        /* Status badges */
        .badge {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:5px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
            min-width:120px;
            width:auto;
            white-space:nowrap;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-done { background: #dbeafe; color: #1e40af; }
        .badge-cancel { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .badge-warning{ background: #FEF3C7; color: #92400E; }
        .badge-purple{ background: #F3E8FF; color: #7E22CE; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px;
            border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: none;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-primary { background: #14b8a6; color: white; }
        .btn-primary:hover { background: #0f9488; }
        .btn-primary:disabled { background: #99e6da; cursor: not-allowed; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-outline { background: white; color: #334155; border: 1px solid #e2e8f0; }
        .btn-outline:hover { background: #f8fafc; }

        /* Search & filter bar */
        .toolbar { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
        .search-wrap { flex: 1; position: relative; }
        .search-wrap input {
            width: 100%; padding: 9px 16px 9px 38px; border: 1px solid #e2e8f0; border-radius: 8px;
            font-size: 14px; color: #334155; outline: none; background: white;
        }
        .search-wrap input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px #ccfbf180; }
        .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .select-filter {
            padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;
            color: #334155; background: white; outline: none; cursor: pointer;
        }
        .select-filter:focus { border-color: #14b8a6; }

        /* Page header */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-header-left h1 { font-size: 22px; font-weight: 700; color: #0f172a; }
        .page-header-left p { font-size: 13px; color: #64748b; margin-top: 3px; }

        /* Grid layout for dashboard bottom */
        .dashboard-bottom { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
        .quick-actions { display: flex; flex-direction: column; gap: 12px; }
        .quick-action-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px;
            display: flex; align-items: center; gap: 14px; cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .quick-action-card:hover { border-color: #14b8a6; box-shadow: 0 2px 8px #14b8a620; }
        .qa-icon {
            width: 40px; height: 40px; border-radius: 8px; background: #f0fdf9;
            display: flex; align-items: center; justify-content: center; color: #14b8a6; font-size: 16px;
        }
        .qa-title { font-size: 14px; font-weight: 600; color: #0f172a; }
        .qa-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

        /* Empty state */
        .empty-state {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 60px 20px; text-align: center; gap: 12px;
        }
        .empty-state i { font-size: 40px; color: #cbd5e1; }
        .empty-state h3 { font-size: 16px; font-weight: 600; color: #475569; }
        .empty-state p { font-size: 13px; color: #94a3b8; }

        /* Action buttons in table */
        .action-btns { display: flex; gap: 6px; }
        .action-btn {
            width: 30px; height: 30px; border-radius: 6px; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center; cursor: pointer; background: white;
            transition: all 0.15s; color: #64748b;
        }
        .action-btn:hover { border-color: #14b8a6; color: #14b8a6; }
        .action-btn.danger:hover { border-color: #f43f5e; color: #f43f5e; }

        /* Alert banner */
        .alert-banner {
            display: flex; align-items: center; gap: 10px; background: #fef9c3; color: #854d0e;
            padding: 12px 18px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; border: 1px solid #fde68a;
        }

        /* Tabs */
        .tabs { display: flex; gap: 0; border-bottom: 1px solid #e2e8f0; background: white; border-radius: 12px 12px 0 0; padding: 0 8px; }
        .tab-link {
            padding: 16px 20px; font-size: 14px; font-weight: 500; color: #64748b; text-decoration: none;
            border-bottom: 2px solid transparent; transition: all 0.2s;
        }
        .tab-link:hover { color: #0f172a; }
        .tab-link.active { color: #0f9488; border-bottom-color: #14b8a6; font-weight: 600; }
        .tab-content { background: white; border-radius: 0 0 12px 12px; padding: 24px; border: 1px solid #e2e8f0; border-top: none; }

        /* Grid cards (CMS) */
        .cms-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .cms-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; position: relative; }
        .cms-card-actions { position: absolute; top: 12px; right: 12px; display: flex; gap: 6px; }
        .cms-icon-circle {
            width: 44px; height: 44px; border-radius: 10px; background: #f0fdf9; color: #14b8a6;
            display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px;
        }
        .cms-card h3 { font-size: 15px; font-weight: 600; color: #0f172a; margin-bottom: 6px; }
        .cms-card p { font-size: 13px; color: #64748b; line-height: 1.5; }

        .portfolio-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .portfolio-card { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .portfolio-img { width: 100%; height: 180px; object-fit: cover; display: block; background: #e2e8f0; }
        .portfolio-img-wrap { position: relative; }
        .portfolio-img-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 6px; }
        .portfolio-body { padding: 14px; }
        .portfolio-category { font-size: 11px; font-weight: 600; color: #14b8a6; text-transform: uppercase; letter-spacing: 0.5px; }
        .portfolio-title { font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 4px; }

        .team-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .team-card { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .team-photo-wrap { position: relative; }
        .team-photo { width: 100%; height: 180px; object-fit: cover; display: block; background: #e2e8f0; }
        .team-photo-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 6px; }
        .team-body { padding: 14px; }
        .team-name { font-size: 15px; font-weight: 600; color: #0f172a; }
        .team-role { font-size: 13px; color: #14b8a6; font-weight: 500; margin: 2px 0 8px; }
        .team-desc { font-size: 12px; color: #64748b; line-height: 1.5; }

        .logo-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .logo-card {
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;
            display: flex; flex-direction: column; align-items: center; gap: 12px; position: relative;
        }
        .logo-card img { max-height: 50px; max-width: 100%; object-fit: contain; }
        .logo-card-name { font-size: 13px; color: #475569; font-weight: 500; }
        .logo-card-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 6px; }

        /* File upload */
        .upload-zone {
            border: 2px dashed #cbd5e1; border-radius: 10px; padding: 40px;
            display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer;
            transition: all 0.2s; text-align: center;
        }
        .upload-zone:hover { border-color: #14b8a6; background: #f0fdf980; }
        .upload-zone i { font-size: 32px; color: #14b8a6; }
        .upload-zone h3 { font-size: 15px; font-weight: 600; color: #334155; }
        .upload-zone p { font-size: 12px; color: #94a3b8; }

        /* Form */
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-label { font-size:13px; font-weight:600; color:#374151; }
        .form-input {
            padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px;
            color:#334155; outline:none; font-family:inherit; background:white; width:100%;
        }
        .form-input:focus { border-color:#14b8a6; box-shadow:0 0 0 3px #ccfbf180; }
        .form-input.error { border-color:#f43f5e; }
        .form-error { font-size:12px; color:#f43f5e; }
        .form-check { display:flex; align-items:center; gap:10px; padding:8px 0; font-size:14px; color:#334155; }
        .form-check input { width:16px; height:16px; accent-color:#14b8a6; }

        /* Modal */
        .modal-overlay {
            display:none; position:fixed; inset:0; background:#00000060; z-index:200;
            align-items:center; justify-content:center;
        }
        .modal-overlay.show { display:flex; }
        .modal-box { background:white; border-radius:12px; width:560px; max-width:95vw; max-height:90vh; overflow-y:auto; }
        .modal-header {
            padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex;
            justify-content:space-between; align-items:center; position:sticky; top:0; background:white;
        }
        .modal-header span { font-size:16px; font-weight:600; }
        .modal-close { background:none; border:none; cursor:pointer; color:#64748b; font-size:18px; }
        .modal-body { padding:24px; display:grid; gap:16px; }
        .modal-footer { padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:flex-end; }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .dashboard-bottom { grid-template-columns: 1fr; }
            .cms-grid, .portfolio-grid, .team-grid, .logo-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">A</div>
        <span class="brand-name">ADMIN</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"  class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Ringkasan Dashboard
        </a>
        <a href="{{ route('admin.kelola-klien.index') }}"
            class="nav-item {{ request()->routeIs('admin.kelola-klien.*') ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i> Kelola Klien
        </a>
        <a href="{{ route('admin.events.index') }}" class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Kelola Event
        </a>
        <a href="{{ route('admin.requests.index') }}" class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
            <i class="fas fa-inbox"></i> Request Client
        </a>
        <a href="{{ route('admin.rab.index') }}" class="nav-item {{ request()->routeIs('admin.rab.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Anggaran (RAB)
        </a>
        <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="fas fa-check-square"></i> Pembayaran
        </a>
        <a href="{{ route('admin.timeline.index') }}" class="nav-item {{ request()->routeIs('admin.timeline.*') ? 'active' : '' }}">
            <i class="fas fa-calendar"></i> Timeline
        </a>
        <a href="{{ route('admin.event-vendors.index') }}" class="nav-item {{ request()->routeIs('admin.event-vendors.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> Tugas &amp; Tim
        </a>
        <a href="{{ route('admin.vendors.index') }}" class="nav-item {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Vendor
        </a>
        <a href="{{ route('admin.cms.index') }}" class="nav-item {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
            <i class="fas fa-palette"></i> Landing Page CMS
        </a>
        <a href="{{ route('admin.proposals.index') }}" class="nav-item {{ request()->routeIs('admin.proposals.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Dokumen
        </a>
        <a href="{{ route('admin.documentation.index') }}" class="nav-item {{ request()->routeIs('admin.documentation.*') ? 'active' : '' }}">
            <i class="fas fa-folder-open"></i> Pusat Dokumentasi
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Analitik
        </a>
        <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifikasi
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="button" onclick="confirmLogout(event)" class="nav-item nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main-wrapper">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="topbar-right">
            <a href="{{ route('admin.notifications.index') }}"
                class="topbar-notif">
                    <i class="bi bi-bell-fill"></i>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="notif-count">
                            {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                        </span>
                    @endif
                </a>
            <div class="topbar-user">
                <span>{{ auth()->user()->name ?? 'admin' }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
            </div>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div id="success-alert"
                style="background:#dcfce7; color:#166534; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2; color:#991b1b; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background:#fee2e2; color:#991b1b; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
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

<x-logout-confirmation />
@stack('scripts')

</body>
</html>
