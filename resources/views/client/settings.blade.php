@extends('layouts.client')
@section('title','Pengaturan Akun')
@section('page-title','Pengaturan Akun')

@section('content')
<style>
    .pw-wrap { position: relative; }
    .pw-wrap input { padding-right: 42px !important; }
    .pw-toggle {
        position: absolute; right: 4px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer; padding: 8px 12px;
        color: #94a3b8; font-size: 16px; display: flex; align-items: center;
        transition: color 0.2s; line-height: 1; z-index: 2;
    }
    .pw-toggle:hover { color: #64748b; }
</style>

<div class="page-header">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);">Pengaturan Akun</h1>
</div>

<div style="max-width:720px;">

    {{-- Profil --}}
    <div class="settings-card">
        <div class="settings-card-title">Profil Saya</div>
        <div class="settings-card-desc">Perbarui informasi kontak dan profil akun Anda.</div>

        <form action="{{ route('client.settings.profile') }}" method="POST">
            @csrf @method('PUT')

            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--dark);
                            color:#fff;font-size:20px;font-weight:700;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {{ $user->initials }}
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--dark);">
                        {{ $user->name }}
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);">{{ $user->email }}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="tel" name="phone" class="form-control"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="+62 xxx xxxx xxxx">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Keamanan --}}
    <div class="settings-card">
        <div class="settings-card-title">Keamanan Akun</div>
        <div class="settings-card-desc">Perbarui kata sandi Anda secara berkala.</div>

        <form action="{{ route('client.settings.password') }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <div class="pw-wrap">
                    <input type="password" name="current_password" class="form-control"
                           placeholder="Masukkan password saat ini" required>
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                @error('current_password')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div class="pw-wrap">
                        <input type="password" name="password" class="form-control"
                               placeholder="Min. 8 karakter" required>
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                    <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="pw-wrap">
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Ulangi password baru" required>
                    <button type="button" class="pw-toggle" onclick="togglePw(this)" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-lock"></i> Ubah Password
                </button>
            </div>
        </form>
    </div>

    {{-- Info Akun --}}
    <div class="settings-card">
        <div class="settings-card-title">Informasi Akun</div>
        <div class="settings-card-desc">Detail akun Anda di sistem ALPHA.COM.</div>

        <div style="display:grid;gap:12px;">
            <div style="display:flex;justify-content:space-between;padding:10px 0;
                        border-bottom:1px solid var(--border);">
                <span style="font-size:13px;color:var(--text-muted);">Role</span>
                <span class="badge badge-mendatang" style="font-size:11px;">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;
                        border-bottom:1px solid var(--border);">
                <span style="font-size:13px;color:var(--text-muted);">Bergabung</span>
                <span style="font-size:13px;font-weight:600;color:var(--dark);">
                    {{ $user->created_at->isoFormat('D MMMM Y') }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;">
                <span style="font-size:13px;color:var(--text-muted);">Email Terverifikasi</span>
                @if($user->email_verified_at)
                <span class="badge badge-aktif" style="font-size:11px;">
                    <i class="bi bi-check-circle-fill" style="margin-right:3px;"></i> Terverifikasi
                </span>
                @else
                <span class="badge badge-pending" style="font-size:11px;">Belum Terverifikasi</span>
                @endif
            </div>
        </div>
    </div>

</div>
@push('scripts')
<script>
function togglePw(btn) {
    var field = btn.previousElementSibling;
    var icon = btn.querySelector('i');
    if (!field) return;
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}
</script>
@endpush
@endsection

