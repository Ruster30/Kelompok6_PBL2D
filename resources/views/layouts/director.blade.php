<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Inter", sans-serif; background: #f5f6fa; display: flex; min-height: 100vh; overflow: hidden; }
        .sidebar {
            width: 280px; min-width: 280px; background: #0f172a; color: #94a3b8;
            display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100;
            border-right: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand {
            padding: 24px 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand h2 { font-size: 18px; font-weight: 700; color: #fff; margin: 0; }
        .sidebar-brand small { font-size: 11px; color: #64748b; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .nav-section { font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 1px; padding: 16px 14px 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px; cursor: pointer;
            color: #94a3b8; text-decoration: none; font-size: 13.5px; font-weight: 500;
            transition: all .22s ease; border-radius: 8px; margin-bottom: 2px;
            position: relative; border-left: none;
        }
        .nav-item:hover { background: rgba(45,212,191,.10); color: #1aaa99; }
        .nav-item.active {
            background: rgba(45,212,191,.10); color: #1aaa99; font-weight: 700;
        }
        .nav-item.active::before {
            content: ""; position: absolute; left: 0; top: 6px; bottom: 6px;
            width: 3px; background: #2DD4BF; border-radius: 0 3px 3px 0;
        }
        .nav-item i { width: 20px; text-align: center; font-size: 17px; flex-shrink: 0; }
        .sidebar-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,0.07); }
        .sidebar-footer .nav-logout {
            width:100%; background:transparent; border:none; text-align:left;
            display:flex; align-items:center; gap:12px; color:#94a3b8;
            padding:10px 14px; border-radius:8px; transition:all .22s ease;
            cursor:pointer; font-size:13.5px; font-weight:500; text-decoration:none;
        }
        .sidebar-footer .nav-logout:hover { background:rgba(229,62,62,0.10); color:#e53e3e; }
        .sidebar-footer .nav-logout:hover i { color:#e53e3e; }
        .main-wrapper { overflow: hidden; margin-left: 280px; flex: 1; display: flex; flex-direction: column; height: 100vh; }
        .topbar {
            background: white; padding: 0 28px; height: 64px; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 18px; font-weight: 700; color: #0f172a; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .topbar-user {
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            padding: 4px 12px 4px 4px; border-radius: 50px; transition: all 0.2s;
        }
        .topbar-user:hover { background: #f8fafc; }
        .topbar-user span { font-size: 14px; font-weight: 600; color: #0f172a; }
        .avatar {
            width: 36px; height: 36px; background: #2DD4BF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 13px; font-weight: 700;
        }
        .page-content { padding: 20px; flex: 1; overflow-y: auto; }
        .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>Director Panel</h2>
        <small>DDMS - Approval</small>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>
        <a href="{{ route('director.dashboard') }}" class="nav-item @if(request()->routeIs('director.dashboard')) active @endif">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="nav-section">Approval</div>
        <a href="{{ route('director.approval.index') }}" class="nav-item @if(request()->routeIs('director.approval.index') || request()->routeIs('director.approval.show')) active @endif">
            <i class="fas fa-list-check"></i> Daftar Approval
        </a>
        <a href="{{ route('director.approval.history') }}" class="nav-item @if(request()->routeIs('director.approval.history*')) active @endif">
            <i class="fas fa-history"></i> Riwayat Approval
        </a>
        <a href="{{ route('director.verification-audit.index') }}" class="nav-item @if(request()->routeIs('director.verification-audit*')) active @endif">
            <i class="fas fa-shield-alt"></i> Verifikasi Audit
        </a>

        <div class="nav-section">Keamanan</div>
        <a href="{{ route('director.settings.pin') }}" class="nav-item @if(request()->routeIs('director.settings.pin*')) active @endif">
            <i class="fas fa-shield-alt"></i> Pengaturan PIN
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- Main Content --}}
<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-right">
            <div class="topbar-user">
                <span>{{ auth()->user()->name ?? 'Director' }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'DR', 0, 2)) }}</div>
            </div>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>