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

<div class="card max-w-800">
    <div class="p-6">
        <form action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST">
            @csrf
            @if(isset($event)) @method('PUT') @endif

            <div class="form-grid">
                <div class="form-group full-width">
                    <x-bs-input name="nama_event" label="Nama Event" :value="$event->nama_event ?? ''" placeholder="Masukkan nama event" required />
                </div>

                <div class="form-group">
                    <x-bs-select name="client_id" label="Klien" :value="$event->client_id ?? ''" placeholder="-- Pilih Klien --" :options="$clients->pluck('name', 'id')" />
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Event</label>
                    <select name="jenis_event" class="form-input">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Wedding','Corporate','Birthday','Concert','Conference','Exhibition','Other'] as $type)
                        <option value="{{ $type }}" {{ old('jenis_event', $event->jenis_event ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Event <span class="text-red">*</span></label>
                    <input type="date" name="tanggal_event" class="form-input @error('tanggal_event') error @enderror"
                           value="{{ old('tanggal_event', isset($event) ? $event->tanggal_event->format('Y-m-d') : '') }}" required>
                    @error('tanggal_event')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi_event" class="form-input"
                           value="{{ old('lokasi_event', $event->lokasi_event ?? '') }}" placeholder="Lokasi acara">
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Tamu</label>
                    <input type="number" name="jumlah_tamu" class="form-input"
                           value="{{ old('jumlah_tamu', $event->jumlah_tamu ?? '') }}" placeholder="0" min="0">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status_event" class="form-input">
                        @foreach(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status_event', $event->status_event ?? 'menunggu') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Detail Kebutuhan</label>
                    <textarea name="detail_kebutuhan" class="form-input" rows="4" placeholder="Detail kebutuhan event...">{{ old('detail_kebutuhan', $event->detail_kebutuhan ?? '') }}</textarea>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary" id="submitBtn" onclick="this.disabled=true; this.innerHTML='<i class=\"fas fa-spinner fa-spin\"></i> Menyimpan...'; this.form.submit();">
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
