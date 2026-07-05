<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        :root {
    --sidebar-width: 280px;
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

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 24px;
            height: 64px;
        }

        .sidebar-brand span {
            color: #fff;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.3px;
        }
        .sidebar-brand span span { color: #2DD4BF; }










        .sidebar-footer a:hover {
            background: rgba(229,62,62,0.10);
            color: #e53e3e;
        }

        .sidebar-footer a:hover i {
            color: #e53e3e;
        }

        .sidebar-footer a i {
            font-size: 17px;
            width: 20px;
            text-align: center;
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
            font-weight: 700;
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
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f8fafc;
            color: #94a3b8;
            text-decoration: none;
            font-size: 18px;
            transition: all .2s ease;
        }

        .notif-btn:hover {
            background: #f1f5f9;
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
            padding: 4px 12px 4px 4px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info:hover {
            background: #f8fafc;
        }

        .user-info .name-role { text-align: right; }
        .user-info .user-name { font-size: 13px; font-weight: 600; color: #1a2332; line-height: 1.3; }
        .user-info .user-role { font-size: 11px; color: var(--text-muted-custom); }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: white; background: #2DD4BF;
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

        /* ============================================================
           RESPONSIVE DESIGN
           ============================================================ */

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
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
        .mobile-toggle:hover {
            background: #f1f5f9;
            color: #1a8f7e;
        }
        .mobile-toggle i {
            font-size: 20px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Sidebar Overlay */
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

        /* Desktop (≥1200px) - No changes */
        @media (min-width: 1200px) {
            .mobile-toggle {
                display: none !important;
            }
        }

        /* Tablet (768px - 1199px) */
        @media (max-width: 1199px) {
            /* 2 column layout for cards */
            .row .col-md-6 {
                width: 50%;
            }
        }

        /* Mobile (<768px) */
        @media (max-width: 767px) {
            /* Sidebar becomes offcanvas */
            .sidebar {
                transform: translateX(-100%);
                z-index: 100;
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
            }

            /* Main content takes full width */
            .main-wrapper {
                margin-left: 0 !important;
            }

            /* Show hamburger button */
            .mobile-toggle {
                display: flex !important;
            }

            /* Topbar adjustments */
            .topbar {
                padding: 0 16px;
            }
            .topbar-title {
                font-size: 15px;
            }
            .user-info .name-role {
                display: none;
            }
            .avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            /* Page content */
            .page-content {
                padding: 20px 16px;
            }

            /* Single column layout */
            .row .col-md-6,
            .row .col-md-4,
            .row .col-md-3 {
                width: 100%;
            }

            /* Stat cards */
            .stat-card {
                padding: 18px 20px;
            }
            .stat-card .stat-number {
                font-size: 24px;
            }

            /* Section cards */
            .section-card {
                padding: 18px;
            }
            .section-card-title {
                font-size: 15px;
            }

            /* Task items */
            .task-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            /* Event items */
            .event-row-item {
                padding: 12px 0;
            }

            /* Tables - horizontal scroll */
            .table-responsive {
                overflow-x: auto;
            }
            .custom-table {
                min-width: 700px;
            }
            .custom-table thead th {
                padding: 10px 12px;
                font-size: 11px;
            }
            .custom-table tbody td {
                padding: 12px;
                font-size: 13px;
            }

            /* Search */
            .search-wrapper {
                margin-bottom: 12px;
            }
            .search-input {
                font-size: 13px;
                padding: 9px 14px 9px 36px;
            }

            /* Buttons */
            .btn-teal {
                padding: 8px 16px;
                font-size: 13px;
            }

            /* Settings */
            .profile-card {
                padding: 16px;
            }

            /* Modal */
            .modal-content {
                margin: 15px;
            }
            .modal-body {
                padding: 20px;
            }

            /* Event search */
            .event-search {
                padding: 18px;
            }
            .event-search input {
                height: 48px;
                padding-left: 42px;
                font-size: 14px;
            }

            /* Event table */
            .event-table {
                display: block;
                overflow-x: auto;
            }
            .event-table thead,
            .event-table tbody,
            .event-table th,
            .event-table td {
                display: block;
            }
            .event-table thead {
                display: none;
            }
            .event-table tr {
                margin-bottom: 12px;
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 12px;
            }
            .event-table td {
                padding: 8px 0;
                border: none;
                text-align: left;
            }
            .event-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-muted-custom);
                display: block;
                font-size: 11px;
                margin-bottom: 4px;
            }
        }

        /* Very Small Mobile (<576px) */
        @media (max-width: 575px) {
            .topbar-title {
                font-size: 14px;
            }
            .page-content {
                padding: 16px 12px;
            }
            .stat-card {
                padding: 16px 18px;
            }
            .stat-card .stat-number {
                font-size: 22px;
            }
            .section-card {
                padding: 16px;
            }
        }

        /* Responsive helper */
        @media (max-width: 768px) {
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('vendor.ringkasan') }}" class="sidebar-logo">
        <span class="sidebar-logo-text">ALPHA<span>.</span>CORP</span>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-label">Dashboard</div>
            <a href="{{ route('vendor.ringkasan') }}" class="nav-item {{ request()->routeIs('vendor.ringkasan') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Ringkasan
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Pekerjaan</div>
            <a href="{{ route('vendor.event-saya') }}" class="nav-item {{ request()->routeIs('vendor.event-saya') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Event Saya
            </a>
            <a href="{{ route('vendor.daftar-tugas') }}" class="nav-item {{ request()->routeIs('vendor.daftar-tugas') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> Daftar Tugas
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Komunikasi</div>
            <a href="{{ route('vendor.notifikasi') }}" class="nav-item {{ request()->routeIs('vendor.notifikasi') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifikasi
                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                    <span class="sidebar-notification-badge">
                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                    </span>
                @endif
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Akun</div>
            <a href="{{ route('vendor.pengaturan') }}" class="nav-item {{ request()->routeIs('vendor.pengaturan') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="#" onclick="confirmLogout(event)" class="nav-item danger">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
        <form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button id="mobileToggle" class="mobile-toggle" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="topbar-title">@yield('page-title')</h1>
        </div>
        <div class="topbar-right">
            <a href="{{ route('vendor.notifikasi') }}" class="notif-btn">
                <i class="bi bi-bell-fill"></i>
                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                    <span class="notif-count">
                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
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
<script>
// Sidebar responsive toggle
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('mobileToggle');
    
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
    }
});
</script>
<x-swal-helper />
<x-logout-confirmation />
@stack('scripts')
</body>
</html>
