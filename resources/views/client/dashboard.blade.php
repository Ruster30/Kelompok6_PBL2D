<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Klien</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

:root{
    --primary:#14b8a6;
    --primary-soft:#e8f7f3;
    --dark:#0f172a;
    --text:#64748b;
    --border:#e5e7eb;
    --bg:#f8fafc;
}

body{
    background:var(--bg);
    font-family:Inter, sans-serif;
}

/* ===================
SIDEBAR
=================== */

.sidebar{
    background:#fff;
    min-height:100vh;
    border-right:1px solid var(--border);
    position:sticky;
    top:0;
}

.logo-box{
    height:85px;
    display:flex;
    align-items:center;
    padding-left:30px;
    border-bottom:1px solid var(--border);
}

.logo-box img{
    height:40px;
}

.sidebar-menu{
    padding:25px 15px;
}

.sidebar-menu .nav-link{
    color:#64748b;
    padding:15px 18px;
    border-radius:14px;
    margin-bottom:8px;
    font-weight:500;
}

.sidebar-menu .nav-link:hover{
    background:#f1f5f9;
}

.sidebar-menu .active{
    background:#e8f7f3;
    color:#0f766e;
}

.sidebar-menu i{
    width:25px;
}

.logout{
    position:absolute;
    bottom:30px;
    width:100%;
    padding:0 15px;
}

/* ===================
CONTENT
=================== */

.main-content{
    padding:35px;
}

.page-title{
    font-size:18px;
    font-weight:700;
    color:var(--dark);
}

.greeting{
    font-size:48px;
    font-weight:700;
    color:var(--dark);
}

.sub-greeting{
    font-size:22px;
    color:var(--text);
}

/* ===================
CARDS
=================== */

.stat-card{
    background:#fff;
    border-radius:20px;
    border:1px solid var(--border);
    padding:25px;
}

.stat-icon{
    width:70px;
    height:70px;
    background:var(--primary-soft);
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    color:var(--primary);
    font-size:25px;
}

.stat-number{
    font-size:44px;
    font-weight:700;
    color:var(--dark);
}

.event-card{
    background:#fff;
    border-radius:20px;
    border:1px solid var(--border);
    padding:30px;
}

.update-card{
    background:#fff;
    border-radius:20px;
    border:1px solid var(--border);
    padding:30px;
    height:100%;
}

/* ===================
BADGE
=================== */

.badge-success{
    background:#dcfce7;
    color:#16a34a;
}

.badge-primary{
    background:#dbeafe;
    color:#2563eb;
}

/* ===================
BUTTON
=================== */

.btn-primary-custom{
    background:#0f172a;
    color:#fff;
    border:none;
    border-radius:12px;
    padding:12px 25px;
}

.btn-primary-custom:hover{
    background:#1e293b;
}

.btn-outline-custom{
    border:1px solid #99f6e4;
    color:#0f766e;
    border-radius:12px;
    padding:12px 25px;
}

.btn-outline-custom:hover{
    background:#f0fdfa;
}

/* ===================
PROGRESS
=================== */

.progress{
    height:10px;
    border-radius:30px;
}

.progress-bar{
    background:var(--primary);
}

/* ===================
UPDATE
=================== */

.timeline-dot{
    width:18px;
    height:18px;
    border-radius:50%;
    background:var(--primary);
    margin-top:5px;
}

.profile-circle{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#e2e8f0;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:700;
    color:#475569;
}

</style>
</head>

<body>

<div class="container-fluid">

<div class="row">

<!-- SIDEBAR -->

<div class="col-lg-3 sidebar">

    <div class="logo-box">
        <img src="https://via.placeholder.com/120x40" alt="">
    </div>

    <div class="sidebar-menu">

        <a class="nav-link active" href="#">
            <i class="fa-solid fa-table-columns"></i>
            Ringkasan Saya
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-calendar"></i>
            Event Terdaftar
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-timeline"></i>
            Timeline Event
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-money-bill"></i>
            Anggaran & Faktur
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-file"></i>
            Surat Penawaran
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-circle-plus"></i>
            Ajukan Event Baru
        </a>

        <a class="nav-link" href="#">
            <i class="fa-solid fa-gear"></i>
            Pengaturan Akun
        </a>

    </div>

    <div class="logout">
        <a class="nav-link" href="#">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Keluar
        </a>
    </div>

</div>

<!-- CONTENT -->

<div class="col-lg-9 main-content">

    <div class="d-flex justify-content-between align-items-center mb-5">

        <div>
            <div class="page-title">
                Ringkasan Saya
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">

            <div class="text-end">
                <h5 class="fw-bold mb-0">da</h5>
                <span class="text-secondary">
                    Klien Premium
                </span>
            </div>

            <div class="profile-circle">
                DA
            </div>

        </div>

    </div>

    <h1 class="greeting">
        Selamat datang kembali, da
    </h1>

    <p class="sub-greeting mb-5">
        Berikut adalah ringkasan progres perencanaan event Anda.
    </p>

    <!-- STAT CARD -->

    <div class="row g-4 mb-5">

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon mb-3">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>

                <div class="stat-number">0</div>

                <div class="text-secondary">
                    Event Aktif
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon mb-3">
                    <i class="fa-solid fa-wave-square"></i>
                </div>

                <div class="stat-number">1</div>

                <div class="text-secondary">
                    Event Mendatang
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon mb-3">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>

                <div class="stat-number">
                    Rp 0
                </div>

                <div class="text-secondary">
                    Total Pengeluaran
                </div>

            </div>

        </div>

    </div>

    <!-- CONTENT ROW -->

    <div class="row g-4">

        <!-- LEFT -->

        <div class="col-lg-8">

            <h4 class="fw-bold mb-3">
                Pengajuan Event Saya
            </h4>

            <div class="event-card mb-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h3 class="fw-bold">
                            Konser Feast
                        </h3>

                        <span class="badge rounded-pill badge-success px-3 py-2">
                            Diterima
                        </span>

                        <p class="text-secondary mt-2">
                            2026-05-20 • Basko
                        </p>

                    </div>

                    <button class="btn btn-outline-custom">
                        Lihat Penawaran
                    </button>

                </div>

            </div>

            <h4 class="fw-bold mb-3">
                Event Saya
            </h4>

            <div class="event-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h3 class="fw-bold">
                            Konser Feast
                        </h3>

                        <span class="badge rounded-pill badge-primary px-3 py-2">
                            Mendatang
                        </span>

                        <p class="text-secondary mt-2">
                            20/5/2026 • Basko
                        </p>

                    </div>

                    <button class="btn btn-primary-custom">
                        Lihat Timeline →
                    </button>

                </div>

                <div class="mt-4">

                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            Progres Perencanaan
                        </span>

                        <span class="fw-bold">
                            0%
                        </span>

                    </div>

                    <div class="progress">

                        <div class="progress-bar" style="width:0%"></div>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="col-lg-4">

            <div class="update-card">

                <h3 class="fw-bold mb-4">
                    Pembaruan Terbaru
                </h3>

                <div class="d-flex gap-3">

                    <div class="timeline-dot"></div>

                    <div>

                        <h5 class="fw-bold">
                            Event Dibuat: Konser Feast
                        </h5>

                        <p class="text-secondary">
                            Timeline default telah otomatis disusun.
                        </p>

                        <small class="text-secondary">
                            20/5/2026
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>
</div>

</body>
</html>