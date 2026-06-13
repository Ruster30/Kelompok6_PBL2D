@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Pengaturan</h1>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display:block;">
        <div class="card-title">Profil Pribadi</div>
        <p style="font-size:13px; color:#64748b; margin-top:3px;">Perbarui informasi pribadi Anda.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" style="padding:24px;">
        @csrf
        @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
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

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header" style="display:block;">
        <div class="card-title">Ubah Password</div>
        <p style="font-size:13px; color:#64748b; margin-top:3px;">Pastikan password baru aman dan mudah diingat.</p>
    </div>

    <form action="{{ route('admin.settings.updatePassword') }}" method="POST" style="padding:24px;">
        @csrf
        @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-input @error('current_password') error @enderror">
                @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-input @error('password') error @enderror">
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key"></i> Ubah Password
            </button>
        </div>
    </form>
</div>
@endsection