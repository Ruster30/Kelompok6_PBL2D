<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            position: relative; width: 36px; height: 36px; display: flex; align-items: center;
            justify-content: center; cursor: pointer; border-radius: 8px; transition: background 0.2s;
        }
        .topbar-notif:hover { background: #f1f5f9; }
        .notif-badge {
            position: absolute; top: 4px; right: 4px; width: 8px; height: 8px;
            background: #f43f5e; border-radius: 50%; border: 2px solid white;
        }
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
            display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 500;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-done { background: #dbeafe; color: #1e40af; }
        .badge-cancel { background: #fee2e2; color: #991b1b; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px;
            border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: none;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-primary { background: #14b8a6; color: white; }
        .btn-primary:hover { background: #0f9488; }
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

        /* RAB empty state */
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

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .dashboard-bottom { grid-template-columns: 1fr; }
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
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Ringkasan Dashboard
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
        <a href="#" class="nav-item">
            <i class="fas fa-calendar"></i> Timeline
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-tasks"></i> Tugas &amp; Tim
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-users"></i> Vendor &amp; Klien
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-globe"></i> Landing Page CMS
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-file-alt"></i> Proposal &amp; Dokumen
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-chart-bar"></i> Analitik
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-bell"></i> Notifikasi
        </a>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; background:none; border:none; text-align:left;">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main-wrapper">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="topbar-right">
            <div class="topbar-notif">
                <i class="fas fa-bell" style="color:#64748b; font-size:17px;"></i>
                <span class="notif-badge"></span>
            </div>
            <div class="topbar-user">
                <span>{{ auth()->user()->name ?? 'admin' }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
            </div>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2; color:#991b1b; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:14px;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
