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
