{{-- resources/views/client/event-create.blade.php --}}
@extends('client.layouts.app')
@section('title', 'Ajukan Event Baru')
@section('page-title', 'Ajukan Event Baru')

@push('styles')
<style>
/* Sembunyikan topbar title di halaman ini, pakai heading sendiri */
.topbar .topbar-title { display: none; }
</style>
@endpush

@section('content')

<div style="max-width:680px; margin:0 auto;">
    <div class="page-header">
        <h1 style="font-size:26px; font-weight:800; color:var(--dark); margin-bottom:6px;">Ajukan Event Baru</h1>
        <p style="color:var(--text-muted);">Isi formulir di bawah untuk mulai merencanakan event Anda bersama kami.</p>
    </div>

    <form action="{{ route('client.event.store') }}" method="POST">
        @csrf
        <div class="form-card">

            {{-- 1. Detail Event --}}
            <div class="form-section-title">1. Detail Event</div>

            <div class="form-group">
                <label class="form-label">Nama Event</label>
                <input type="text" name="name" class="form-control"
                       placeholder="mis. Konferensi Teknologi Tahunan 2026"
                       value="{{ old('name') }}" required>
                @error('name')
                    <span style="color:#dc2626; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jenis Event</label>
                    <select name="event_type" class="form-control">
                        <option value="">Pilih jenis event</option>
                        <option value="Acara Korporat"    {{ old('event_type')=='Acara Korporat'    ? 'selected':'' }}>Acara Korporat</option>
                        <option value="Pernikahan"        {{ old('event_type')=='Pernikahan'        ? 'selected':'' }}>Pernikahan</option>
                        <option value="Konferensi"        {{ old('event_type')=='Konferensi'        ? 'selected':'' }}>Konferensi</option>
                        <option value="Peluncuran Produk" {{ old('event_type')=='Peluncuran Produk' ? 'selected':'' }}>Peluncuran Produk</option>
                        <option value="Gala Dinner"       {{ old('event_type')=='Gala Dinner'       ? 'selected':'' }}>Gala Dinner</option>
                        <option value="Festival & Konser" {{ old('event_type')=='Festival & Konser' ? 'selected':'' }}>Festival & Konser</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Tamu</label>
                    <input type="number" name="guest_count" class="form-control"
                           placeholder="mis. 250" value="{{ old('guest_count') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="event_date" class="form-control"
                           value="{{ old('event_date') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="event_end_date" class="form-control"
                           value="{{ old('event_end_date') }}">
                </div>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:8px 0 24px;">

            {{-- 2. Lokasi & Anggaran --}}
            <div class="form-section-title">2. Lokasi & Anggaran</div>

            <div class="form-group">
                <label class="form-label">Preferensi Lokasi</label>
                <input type="text" name="location" class="form-control"
                       placeholder="mis. Pusat Kota Jakarta, atau nama venue spesifik"
                       value="{{ old('location') }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe Venue</label>
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="venue_type" value="Indoor"
                                   {{ old('venue_type', 'Indoor') == 'Indoor' ? 'checked' : '' }}>
                            Indoor
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="venue_type" value="Outdoor"
                                   {{ old('venue_type') == 'Outdoor' ? 'checked' : '' }}>
                            Outdoor
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="venue_type" value="Keduanya"
                                   {{ old('venue_type') == 'Keduanya' ? 'checked' : '' }}>
                            Keduanya
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimasi Anggaran (Rp)</label>
                    <input type="text" name="budget" class="form-control"
                           placeholder="mis. 50.000.000" value="{{ old('budget') }}">
                </div>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:8px 0 24px;">

            {{-- 3. Informasi Tambahan --}}
            <div class="form-section-title">3. Informasi Tambahan</div>

            <div class="form-group">
                <label class="form-label">Deskripsi Event & Kebutuhan Khusus</label>
                <textarea name="description" class="form-control" rows="5"
                    placeholder="Ceritakan tentang tujuan event ini, tema spesifik, atau kebutuhan khusus yang perlu kami ketahui...">{{ old('description') }}</textarea>
            </div>

            {{-- Info box --}}
            <div class="form-info-box">
                <i class="bi bi-info-circle-fill"></i>
                <span>Setelah mengirimkan permintaan ini, tim kami akan meninjau detailnya dan menghubungi Anda dalam waktu 24 jam dengan proposal awal dan jadwal panggilan konsultasi.</span>
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <a href="{{ route('client.dashboard') }}" class="btn btn-outline" style="margin-right:8px;">Batal</a>
                <button type="submit" class="btn btn-accent btn-lg">
                    Kirim Permintaan <i class="bi bi-send-fill"></i>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection