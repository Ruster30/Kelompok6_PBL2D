<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f1923;
            --sidebar-text: #c9d4df;
            --sidebar-active-bg: #1a8f7e;
            --sidebar-hover-bg: rgba(255,255,255,0.06);
            --topbar-height: 64px;
            --teal: #1a8f7e;
            --teal-light: #e6f5f3;
            --teal-hover: #157a6b;
            --body-bg: #f4f6f9;
            --card-radius: 12px;
            --text-muted-custom: #8a9bb0;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--body-bg);
            margin: 0;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-brand .logo-box {
            width: 36px; height: 36px;
            background: var(--teal);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 13px;
            letter-spacing: 0.5px;
        }

        .sidebar-brand span {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 18px 12px;
        }

        .nav-item-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 2px;
        }

        .nav-item-custom i {
            font-size: 17px;
            width: 20px;
            text-align: center;
            opacity: 0.8;
        }

        .nav-item-custom:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }

        .nav-item-custom.active {
            background: var(--sidebar-active-bg);
            color: #fff;
            font-weight: 500;
        }

        .nav-item-custom.active i { opacity: 1; }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.15s, color 0.15s;
        }

        .sidebar-footer a:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e8edf2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky; top: 0;
            z-index: 100;
        }

        .topbar-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a2332;
            margin: 0;
        }

        .topbar-right{
            display:flex;
            align-items:center;
            gap:16px;
        }

        .notif-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d1d5db;
            text-decoration: none;
            font-size: 18px;
            transition: all .2s ease;
        }

        .notif-btn:hover {
            color: #2dd4bf;
        }

        .notif-btn i {
            font-size: 18px;
        }

        .notif-count {
            position: absolute;
            top: -6px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            padding: 0 5px;
            z-index: 10;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info .name-role { text-align: right; }
        .user-info .user-name { font-size: 13px; font-weight: 600; color: #1a2332; line-height: 1.3; }
        .user-info .user-role { font-size: 11px; color: var(--text-muted-custom); }

        .avatar {
            width: 36px; height: 36px;
            background: #b2e8e2;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: var(--teal);
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            flex: 1;
            padding: 32px;
        }

        /* ===== CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e8edf2;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            background: var(--teal-light);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-card .stat-icon i { font-size: 22px; color: var(--teal); }

        .stat-card .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1a2332;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: var(--text-muted-custom);
        }

        .section-card {
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e8edf2;
            padding: 24px;
        }

        .section-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a2332;
            margin: 0;
        }

        .link-teal {
            color: var(--teal);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .link-teal:hover { color: var(--teal-hover); }

        /* Status badges */
        .badge-mendatang {
            background: #dbeafe;
            color: #2563eb;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-berlangsung {
            background: #dcfce7;
            color: #16a34a;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-selesai {
            background: #f1f5f9;
            color: #64748b;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Task item */
        .task-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #eef1f5;
            background: #fafbfc;
        }

        .task-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            margin-top: 4px; flex-shrink: 0;
        }

        .task-dot.pending { background: var(--teal); }
        .task-dot.selesai { background: #22c55e; }
        .task-dot.terlambat { background: #ef4444; }

        .task-name { font-size: 14px; font-weight: 500; color: #1a2332; }
        .task-sub { font-size: 12px; color: var(--text-muted-custom); margin-top: 2px; }

        .badge-status {
            font-size: 12px; padding: 3px 10px;
            border-radius: 6px; font-weight: 500;
        }

        .badge-pending { background: #f1f5f9; color: #64748b; }
        .badge-selesai-t { background: #dcfce7; color: #16a34a; }
        .badge-terlambat { background: #fee2e2; color: #dc2626; }

        /* Event item in list */
        .event-row-item {
            border-bottom: 1px solid #eef1f5;
            padding: 16px 0;
        }

        .event-row-item:last-child { border-bottom: none; }

        /* Table styles */
        .custom-table thead th {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted-custom);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #eef1f5;
            padding: 10px 16px;
            background: transparent;
        }

        .custom-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .custom-table tbody tr:last-child td { border-bottom: none; }
        .custom-table tbody tr:hover td { background: #fafbfc; }

        /* Search input */
        .search-input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 16px 10px 40px;
            font-size: 14px;
            color: #334155;
            background: #fafbfc;
            width: 100%;
            outline: none;
            transition: border 0.15s, box-shadow 0.15s;
        }

        .search-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(26,143,126,0.1);
            background: #fff;
        }

        .search-wrapper { position: relative; }
        .search-wrapper i {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #b0bec5; font-size: 15px;
        }

        /* Timeline (Jadwal) */
        .timeline-wrapper { position: relative; padding-left: 32px; }

        .timeline-line {
            position: absolute;
            left: 10px; top: 12px; bottom: 12px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -26px;
            top: 18px;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #d1d5db;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #d1d5db;
            z-index: 1;
        }

        .timeline-dot.active {
            background: var(--teal);
            box-shadow: 0 0 0 2px var(--teal);
        }

        .timeline-dot.done {
            background: #22c55e;
            box-shadow: 0 0 0 2px #22c55e;
        }

        .timeline-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef1f5;
            padding: 16px 20px;
            margin-bottom: 12px;
        }

        .timeline-card:hover { border-color: #cbd5e1; }

        .timeline-date {
            font-size: 12px;
            color: var(--text-muted-custom);
            background: #f1f5f9;
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* Buttons */
        .btn-teal {
            background: var(--teal);
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-teal:hover { background: var(--teal-hover); color: #fff; }

        .btn-outline-teal {
            background: transparent;
            color: var(--teal);
            border: 1.5px solid var(--teal);
            padding: 6px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }

        .btn-outline-teal:hover { background: var(--teal); color: #fff; }

        /* Action buttons in table */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
        }

        .action-btn-teal {
            color: var(--teal);
            border-color: #b2e8e2;
            background: var(--teal-light);
        }

        .action-btn-teal:hover { background: var(--teal); color: #fff; border-color: var(--teal); }

        .action-btn-gray {
            color: #64748b;
            border-color: #e2e8f0;
            background: #f8fafc;
        }

        .action-btn-gray:hover { background: #e2e8f0; color: #334155; }

        /* Pengaturan */
        .settings-info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 24px;
        }

        .settings-info-box i { color: #2563eb; font-size: 18px; margin-top: 1px; }
        .settings-info-box p { margin: 0; font-size: 14px; color: #1e40af; }

        .profile-card {
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e8edf2;
            overflow: hidden;
        }

        .profile-card-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile-card-header h5 { font-size: 15px; font-weight: 600; color: #1a2332; margin: 0; }
        .profile-card-header p { font-size: 13px; color: var(--text-muted-custom); margin: 2px 0 0; }

        .readonly-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #64748b;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
        }

        .profile-field-label {
            font-size: 12px;
            color: var(--text-muted-custom);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-field-label i { font-size: 13px; }
        .profile-field-value { font-size: 14px; font-weight: 500; color: #1a2332; }

        /* Modal */
        .modal-content { border-radius: 14px; border: none; }
        .modal-header-custom {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 24px 0;
        }

        .modal-icon {
            width: 36px; height: 36px;
            background: var(--teal-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .modal-icon i { color: var(--teal); font-size: 18px; }
        .modal-header-custom h5 { font-size: 16px; font-weight: 600; color: #1a2332; margin: 0; }
        .modal-subtext { font-size: 13px; color: var(--text-muted-custom); padding: 2px 24px 0 72px; }

        .form-label-custom {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-label-custom .req { color: #e74c3c; }

        .form-control-custom {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: #334155;
            width: 100%;
            outline: none;
            transition: border 0.15s, box-shadow 0.15s;
            background: #fff;
        }

        .form-control-custom:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(26,143,126,0.1);
        }

        .form-hint { font-size: 12px; color: var(--text-muted-custom); margin-top: 4px; }

        /* Notifikasi */
        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.1s;
        }

        .notif-item:hover { background: #fafbfc; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item.unread { background: #f0faf9; }

        .notif-icon-wrap {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .notif-title { font-size: 14px; font-weight: 500; color: #1a2332; margin-bottom: 2px; }
        .notif-body { font-size: 13px; color: #64748b; }
        .notif-time { font-size: 11px; color: #b0bec5; margin-top: 4px; }
        .notif-unread-dot { width: 8px; height: 8px; background: var(--teal); border-radius: 50%; margin-top: 6px; flex-shrink: 0; }

        /* ===== EVENT SAYA ===== */
        .event-card {
            background: #fff;
            border: 1px solid #e8edf2;
            border-radius: 18px;
            overflow: hidden;
        }

        .event-search {
            padding: 22px;
            border-bottom: 1px solid #eef1f5;
        }

        .event-search .search-box {
            position: relative;
            max-width: 600px;
        }

        .event-search .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .event-search input {
            width: 100%;
            height: 52px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding-left: 46px;
            outline: none;
        }

        .event-search input:focus {
            border-color: var(--teal);
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
        }

        .event-table thead {
            background: #f8fafc;
        }

        .event-table th {
            padding: 18px 24px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            text-align: left;
        }

        .event-table td {
            padding: 18px 24px;
            border-top: 1px solid #eef1f5;
            font-size: 14px;
            color: #334155;
        }

        .event-table tbody tr:hover {
            background: #fafafa;
        }

        .empty-event {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
            font-size: 15px;
        }

        .status-menunggu {
            background: #fef3c7;
            color: #b45309;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
        }

        .status-berjalan {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
        }

        .status-selesai {
            background: #dcfce7;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-box">A</div>
        <span>Vendor</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('vendor.ringkasan') }}" class="nav-item-custom {{ request()->routeIs('vendor.ringkasan') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            Ringkasan
        </a>
        <a href="{{ route('vendor.event-saya') }}" class="nav-item-custom {{ request()->routeIs('vendor.event-saya') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i>
            Event Saya
        </a>
        <a href="{{ route('vendor.jadwal') }}" class="nav-item-custom {{ request()->routeIs('vendor.jadwal') ? 'active' : '' }}">
            <i class="bi bi-clock"></i>
            Jadwal
        </a>
        <a href="{{ route('vendor.daftar-tugas') }}" class="nav-item-custom {{ request()->routeIs('vendor.daftar-tugas') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            Daftar Tugas
        </a>
        <a href="{{ route('vendor.notifikasi') }}" class="nav-item-custom {{ request()->routeIs('vendor.notifikasi') ? 'active' : '' }}">
            <i class="bi bi-bell"></i>
            Notifikasi
        </a>
        <a href="{{ route('vendor.pengaturan') }}" class="nav-item-custom {{ request()->routeIs('vendor.pengaturan') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('vendor.logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right" style="font-size:17px; width:20px; text-align:center; opacity:0.8;"></i>
            Keluar
        </a>
        <form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">

    <!-- TOPBAR -->
    <header class="topbar">
        <h1 class="topbar-title">@yield('page-title')</h1>
        <div class="topbar-right">
            <a href="{{ route('vendor.notifikasi') }}" class="notif-btn">
                <i class="bi bi-bell-fill"></i>
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="notif-count">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>
            <div class="user-info">
                <div class="name-role">
                    <div class="user-name">{{ Auth::user()->name ?? 'barak amak' }}</div>
                    <div class="user-role">Mitra Vendor</div>
                </div>
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'BA', 0, 2)) }}</div>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        @yield('content')
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
