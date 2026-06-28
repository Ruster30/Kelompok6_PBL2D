@extends('layouts.admin')

@section('title', 'Edit Klien - ' . $user->name)
@section('page-title', 'Edit Klien')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Edit Klien</h1>
        <p>Perbarui informasi akun klien.</p>
    </div>
    <a href="{{ route('admin.kelola-klien.show', $user) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:560px;">
    <div class="card" style="padding:28px;">
        {{-- Avatar Header --}}
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #f1f5f9;">
            <div style="width:52px;height:52px;border-radius:50%;background:#14b8a6;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0;">
                {{ $user->initials }}
            </div>
            <div>
                <div style="font-weight:700;font-size:15px;color:#1e293b;">{{ $user->name }}</div>
                <div style="font-size:13px;color:#94a3b8;">{{ $user->email }}</div>
            </div>
        </div>

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#991b1b;font-size:13px;">
            <strong>Terdapat kesalahan:</strong>
            <ul style="margin:6px 0 0 16px;">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.kelola-klien.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Nama Lengkap <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:14px;color:#334155;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Email <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:14px;color:#334155;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Nomor Telepon
                </label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       placeholder="Cth: 0812-3456-7890"
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:14px;color:#334155;box-sizing:border-box;">
            </div>

            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.kelola-klien.show', $user) }}" class="btn btn-secondary" style="flex:1;text-align:center;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
