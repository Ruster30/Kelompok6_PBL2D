{{-- resources/views/client/settings.blade.php --}}
@extends('layouts.client')
@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px; font-weight:800; color:var(--dark);">Pengaturan Akun</h1>
</div>

<div style="max-width:860px;">

    {{-- Profil Perusahaan --}}
    <div class="settings-card">
        <div class="settings-card-title">Profil Perusahaan</div>
        <div class="settings-card-desc">Perbarui detail perusahaan dan informasi kontak utama Anda.</div>

        <form action="{{ route('client.settings.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" name="company_name" class="form-control"
                       value="{{ old('company_name', Auth::user()->company_name ?? '') }}"
                       placeholder="Nama perusahaan Anda">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Kontak Utama</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', Auth::user()->name ?? '') }}"
                           placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Kontak</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', Auth::user()->email ?? '') }}"
                           placeholder="email@perusahaan.com" required>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Keamanan Akun --}}
    <div class="settings-card">
        <div class="settings-card-title">Keamanan Akun</div>
        <div class="settings-card-desc">Perbarui kata sandi akun Anda secara berkala untuk keamanan.</div>

        <form action="{{ route('client.settings.password') }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control"
                       placeholder="Masukkan password saat ini">
                @error('current_password')
                    <span style="color:#dc2626; font-size:12px; display:block; margin-top:4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Min. 8 karakter">
                    @error('password')
                        <span style="color:#dc2626; font-size:12px; display:block; margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-lock"></i> Ubah Password
                </button>
            </div>
        </form>
    </div>


</div>
@endsection

@push('styles')
<style>
.toggle-ui {
    width: 40px; height: 22px;
    background: var(--border);
    border-radius: 999px;
    display: inline-block;
    position: relative;
    transition: background .2s;
    cursor: pointer;
    flex-shrink: 0;
}
.toggle-ui::after {
    content: '';
    position: absolute;
    width: 16px; height: 16px;
    background: #fff;
    border-radius: 50%;
    top: 3px; left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.toggle-cb:checked ~ .toggle-ui,
.toggle-cb:checked + .toggle-ui { background: var(--accent); }
.toggle-cb:checked ~ .toggle-ui::after,
.toggle-cb:checked + .toggle-ui::after { transform: translateX(18px); }
.toggle-cb { display: none; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.toggle-ui').forEach(ui => {
    ui.addEventListener('click', function () {
        const cb = document.getElementById(this.dataset.for);
        if (cb) {
            cb.checked = !cb.checked;
            this.style.background = cb.checked ? 'var(--accent)' : 'var(--border)';
            this.querySelector ? null : null;
            // trigger CSS
            cb.dispatchEvent(new Event('change'));
        }
    });
});
// Init toggle visuals on load
document.querySelectorAll('.toggle-cb').forEach(cb => {
    const ui = document.querySelector(`.toggle-ui[data-for="${cb.id}"]`);
    if (ui) ui.style.background = cb.checked ? 'var(--accent)' : 'var(--border)';
});
</script>
@endpush