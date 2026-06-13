@extends('vendor.layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')

<!-- INFO BOX -->
<div class="settings-info-box">
    <i class="bi bi-shield-lock"></i>
    <p>
        Profil vendor bersifat hanya-baca. Perubahan data hanya dapat dilakukan oleh Admin melalui modul
        <strong>Vendor &amp; Klien</strong>.
    </p>
</div>

<!-- PROFILE CARD -->
<div class="profile-card">
    <div class="profile-card-header">
        <div>
            <h5>Profil Vendor</h5>
            <p>Informasi layanan Anda.</p>
        </div>
        <div class="readonly-badge">
            <i class="bi bi-lock" style="font-size:12px;"></i>
            Hanya Baca
        </div>
    </div>

    <div style="padding:24px;">

        <!-- Nama Perusahaan -->
        <div class="mb-4">
            <div class="profile-field-label">Nama Perusahaan / Layanan</div>
            <div class="profile-field-value">{{ $vendor->nama ?? 'barak amak' }}</div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Kategori -->
            <div class="col-md-6">
                <div class="profile-field-label">
                    <i class="bi bi-tag"></i> Kategori
                </div>
                <div class="profile-field-value">{{ $vendor->kategori ?? 'Katering' }}</div>
            </div>
            <!-- Status -->
            <div class="col-md-6">
                <div class="profile-field-label">Status</div>
                <div class="profile-field-value">{{ $vendor->status ?? 'Sedang Bertugas' }}</div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Email -->
            <div class="col-md-6">
                <div class="profile-field-label">
                    <i class="bi bi-envelope"></i> Email
                </div>
                <div class="profile-field-value">{{ $vendor->email ?? 'amak@gmail.com' }}</div>
            </div>
            <!-- Telepon -->
            <div class="col-md-6">
                <div class="profile-field-label">
                    <i class="bi bi-telephone"></i> Telepon
                </div>
                <div class="profile-field-value">{{ $vendor->telepon ?? '081234' }}</div>
            </div>
        </div>

        <!-- Alamat -->
        <div>
            <div class="profile-field-label">
                <i class="bi bi-geo-alt"></i> Alamat
            </div>
            <div class="profile-field-value">{{ $vendor->alamat ?? 'PNP' }}</div>
        </div>

    </div>
</div>

@endsection
