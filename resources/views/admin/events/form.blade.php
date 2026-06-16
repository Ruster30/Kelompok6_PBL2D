@extends('layouts.admin')

@section('title', isset($event) ? 'Edit Event' : 'Buat Event Baru')
@section('page-title', isset($event) ? 'Edit Event' : 'Buat Event Baru')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>{{ isset($event) ? 'Edit Event' : 'Buat Event Baru' }}</h1>
        <p>{{ isset($event) ? 'Perbarui informasi event' : 'Isi detail event baru' }}</p>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:800px;">
    <div style="padding:28px;">
        <form action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST">
            @csrf
            @if(isset($event)) @method('PUT') @endif

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nama Event <span style="color:#f43f5e;">*</span></label>
                    <input type="text" name="name" class="form-input @error('name') error @enderror"
                           value="{{ old('name', $event->name ?? '') }}" placeholder="Masukkan nama event" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Klien</label>
                    <select name="client_id" class="form-input">
                        <option value="">-- Pilih Klien --</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $event->client_id ?? '') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Event</label>
                    <select name="type" class="form-input">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Wedding','Corporate','Birthday','Concert','Conference','Exhibition','Other'] as $type)
                        <option value="{{ $type }}" {{ old('type', $event->type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Event <span style="color:#f43f5e;">*</span></label>
                    <input type="date" name="event_date" class="form-input @error('event_date') error @enderror"
                           value="{{ old('event_date', isset($event) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '') }}" required>
                    @error('event_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location" class="form-input"
                           value="{{ old('location', $event->location ?? '') }}" placeholder="Lokasi acara">
                </div>

                <div class="form-group">
                    <label class="form-label">Anggaran (Rp)</label>
                    <input type="number" name="budget" class="form-input"
                           value="{{ old('budget', $event->budget ?? '') }}" placeholder="0">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        @foreach(['pending','aktif','selesai','batal'] as $s)
                        <option value="{{ $s }}" {{ old('status', $event->status ?? 'pending') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="4" placeholder="Deskripsi singkat event...">{{ old('description', $event->description ?? '') }}</textarea>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ isset($event) ? 'Simpan Perubahan' : 'Buat Event' }}
                </button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-label { font-size:13px; font-weight:600; color:#374151; }
.form-input {
    padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px;
    color:#334155; outline:none; font-family:inherit; background:white; width:100%;
}
.form-input:focus { border-color:#14b8a6; box-shadow:0 0 0 3px #ccfbf180; }
.form-input.error { border-color:#f43f5e; }
.form-error { font-size:12px; color:#f43f5e; }
</style>
@endpush
