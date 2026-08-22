@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<style>
    .pw-wrap { position: relative; }
    .pw-wrap input { padding-right: 42px !important; }
    .pw-toggle {
        position: absolute; right: 2px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer; padding: 8px 12px;
        color: #94a3b8; font-size: 16px; display: flex; align-items: center;
        transition: color 0.2s; line-height: 1; z-index: 2;
    }
    .pw-toggle:hover { color: #64748b; }
</style>
<div class="page-header">
    <div class="page-header-left">
        <h1>Pengaturan</h1>
    </div>
</div>

<div class="card">
    <div class="card-header block">
        <div class="card-title">DDMS</div>
        <p class="text-sm text-muted mt-1">Status Digital Document Management System.</p>
    </div>
    <div class="p-6">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-3">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center mb-3">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="fw-semibold mb-1">
                    Status DDMS:
                    <span class="badge {{ $ddmsEnabled ? 'bg-success' : 'bg-danger' }}">
                        {{ $ddmsEnabled ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                @if($ddmsEnabled)
                    <p class="text-muted small mb-0">DDMS aktif. Pembuatan dan alur dokumen DDMS tersedia.</p>
                @else
                    <p class="text-muted small mb-0">DDMS sedang dinonaktifkan. Pembuatan/alur dokumen DDMS baru tidak tersedia.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.settings.ddms-toggle') }}">
                @csrf
                @if($ddmsEnabled)
                    <button type="submit" name="enabled" value="0" class="btn btn-outline-danger">
                        <i class="fas fa-power-off me-1"></i> Nonaktifkan DDMS
                    </button>
                @else
                    <button type="submit" name="enabled" value="1" class="btn btn-outline-success">
                        <i class="fas fa-power-off me-1"></i> Aktifkan DDMS
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="card mt-5">
    <div class="card-header block">
        <div class="card-title">Default DDMS per Jenis Surat</div>
        <p class="text-sm text-muted mt-1">Tentukan secara default apakah setiap jenis surat menggunakan alur DDMS. Ini hanya nilai awal; admin tetap dapat mengubah pilihan saat membuat dokumen di halaman Generate.</p>
    </div>
    <form method="POST" action="{{ route('admin.settings.ddms-defaults') }}" class="p-6">
        @csrf
        @method('PUT')
        <div class="ddms-default-list">
            @php
                $ddmsDefaultItems = [
                    'ddms_default_surat_kontrak'  => ['label' => 'Surat Kontrak',  'key' => 'surat_kontrak'],
                    'ddms_default_invoice'        => ['label' => 'Invoice',        'key' => 'invoice'],
                    'ddms_default_rab'            => ['label' => 'RAB',            'key' => 'rab'],
                ];
            @endphp
            @foreach($ddmsDefaultItems as $name => $item)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid #e2e8f0;">
                    <div>
                        <div class="fw-semibold mb-1">{{ $item['label'] }}</div>
                        <small class="text-muted">Secara default menggunakan alur DDMS</small>
                    </div>
                    <div class="ddms-toggle" role="radiogroup" aria-label="{{ $item['label'] }}">
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin-right:14px;">
                            <input type="radio" name="{{ $name }}" value="1" {{ !empty($ddmsDefaults[$item['key']]) ? 'checked' : '' }}>
                            <span class="badge bg-success">ON</span>
                        </label>
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="radio" name="{{ $name }}" value="0" {{ empty($ddmsDefaults[$item['key']]) ? 'checked' : '' }}>
                            <span class="badge bg-danger">OFF</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Default
            </button>
        </div>
    </form>
</div>

<div class="card mt-5">
    <div class="card-header block">
        <div class="card-title">Profil Pribadi</div>
        <p class="text-sm text-muted mt-1">Perbarui informasi pribadi Anda.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama Depan</label>
                <input type="text" name="name" class="form-input @error('name') error @enderror"
                       value="{{ old('name', auth()->user()->name) }}" placeholder="Admin">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input @error('email') error @enderror"
                       value="{{ old('email', auth()->user()->email) }}" placeholder="admin@alphacorp.com">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<div class="card mt-5">
    <div class="card-header block">
        <div class="card-title">Ubah Password</div>
        <p class="text-sm text-muted mt-1">Pastikan password baru aman dan mudah diingat.</p>
    </div>

    <form action="{{ route('admin.settings.updatePassword') }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid-3">
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <div class="pw-wrap">
                    <input type="password" name="current_password" class="form-input @error('current_password') error @enderror">
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <div class="pw-wrap">
                    <input type="password" name="password" class="form-input @error('password') error @enderror">
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="pw-wrap">
                    <input type="password" name="password_confirmation" class="form-input">
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key"></i> Ubah Password
            </button>
        </div>
    </form>
</div>
@push('scripts')
<script>
function togglePw(btn) {
    var field = btn.previousElementSibling;
    var icon = btn.querySelector('i');
    if (!field) return;
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
@endpush
@endsection
