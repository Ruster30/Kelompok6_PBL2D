@extends('layouts.director')

@section('title', 'Pengaturan PIN')
@section('page-title', 'Pengaturan PIN')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Pengaturan PIN</h1>
        <p>
            <a href="{{ route('director.approval.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Approval Dashboard
            </a>
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(! $director->hasApprovalPin())
        {{-- --- SET PIN --------------------------------------- --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">Set PIN Director</h4>
                <p class="text-muted small mb-4">Buat PIN 6 digit untuk verifikasi approval dokumen.</p>

                <form method="POST" action="{{ route('director.settings.pin.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="pin" class="form-label fw-medium">PIN</label>
                        <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror" placeholder="Masukkan 6 digit PIN" inputmode="numeric" pattern="\d{6}" maxlength="6" required autocomplete="off">
                        @error('pin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pin_confirmation" class="form-label fw-medium">Konfirmasi PIN</label>
                        <input type="password" name="pin_confirmation" id="pin_confirmation" class="form-control @error('pin_confirmation') is-invalid @enderror" placeholder="Ulangi 6 digit PIN" inputmode="numeric" pattern="\d{6}" maxlength="6" required autocomplete="off">
                        @error('pin_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-shield-lock me-1"></i> Simpan PIN
                    </button>
                </form>
            </div>
        </div>
        @else
        {{-- --- UBAH PIN --------------------------------------- --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">Ubah PIN Director</h4>
                <p class="text-muted small mb-4">Ganti PIN lama Anda dengan PIN baru 6 digit.</p>

                <form method="POST" action="{{ route('director.settings.pin.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_pin" class="form-label fw-medium">PIN Lama</label>
                        <input type="password" name="current_pin" id="current_pin" class="form-control @error('current_pin') is-invalid @enderror" placeholder="Masukkan PIN lama" inputmode="numeric" pattern="\d{6}" maxlength="6" required autocomplete="off">
                        @error('current_pin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pin" class="form-label fw-medium">PIN Baru</label>
                        <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror" placeholder="Masukkan 6 digit PIN baru" inputmode="numeric" pattern="\d{6}" maxlength="6" required autocomplete="off">
                        @error('pin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pin_confirmation" class="form-label fw-medium">Konfirmasi PIN Baru</label>
                        <input type="password" name="pin_confirmation" id="pin_confirmation" class="form-control @error('pin_confirmation') is-invalid @enderror" placeholder="Ulangi 6 digit PIN baru" inputmode="numeric" pattern="\d{6}" maxlength="6" required autocomplete="off">
                        @error('pin_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-shield-lock me-1"></i> Ubah PIN
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection