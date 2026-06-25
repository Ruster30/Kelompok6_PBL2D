@extends('layouts.client')
@section('title','Ajukan Event Baru')
@section('page-title','Ajukan Event Baru')

@section('content')
<div style="max-width:680px;margin:0 auto;">

    <div class="page-header">
        <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:6px;">
            Ajukan Event Baru
        </h1>
        <p style="color:var(--text-muted);">
            Isi formulir di bawah untuk mulai merencanakan event Anda bersama kami.
        </p>
    </div>

    <form action="{{ route('client.event.store') }}" method="POST">
        @csrf
        <div class="form-card">

            {{-- 1. Detail Event --}}
            <div class="form-section-title">1. Detail Event</div>

            <div class="form-group">
                <label class="form-label">
                    Nama Event <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="nama_event" class="form-control"
                       placeholder="mis. Konferensi Teknologi Tahunan 2026"
                       value="{{ old('nama_event') }}" required>
                @error('nama_event')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Jenis Event <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="jenis_event" class="form-control" required>
                        <option value="">Pilih jenis event</option>
                        @foreach([
                            'Acara Korporat','Pernikahan','Konferensi',
                            'Peluncuran Produk','Gala Dinner','Festival & Konser'
                        ] as $jenis)
                        <option value="{{ $jenis }}"
                                {{ old('jenis_event') === $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                        @endforeach
                    </select>
                    @error('jenis_event')
                    <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Jumlah Tamu <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="number" name="jumlah_tamu" class="form-control"
                           placeholder="mis. 250"
                           value="{{ old('jumlah_tamu') }}" min="1" required>
                    @error('jumlah_tamu')
                    <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Rentang Anggaran</label>
                <select name="rentang_anggaran" class="form-control">
                    <option value="">Pilih rentang anggaran</option>
                    @foreach(['Di bawah Rp 100 Juta', 'Rp 100 Juta - Rp 250 Juta', 'Rp 250 Juta - Rp 500 Juta', 'Di atas Rp 500 Juta'] as $anggaran)
                    <option value="{{ $anggaran }}" {{ old('rentang_anggaran') === $anggaran ? 'selected' : '' }}>
                        {{ $anggaran }}
                    </option>
                    @endforeach
                </select>
                @error('rentang_anggaran')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Tanggal Event <span style="color:#dc2626;">*</span>
                </label>
                <input type="date" name="tanggal_event" class="form-control"
                       value="{{ old('tanggal_event') }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                @error('tanggal_event')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 24px;">

            {{-- 2. Lokasi --}}
            <div class="form-section-title">2. Lokasi Event</div>

            <div class="form-group">
                <label class="form-label">
                    Lokasi Event <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="lokasi_event" class="form-control"
                       placeholder="mis. Basko Grand Mall, Padang"
                       value="{{ old('lokasi_event') }}" required>
                @error('lokasi_event')
                <span style="color:#dc2626;font-size:12px;display:block;margin-top:4px;">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 24px;">

            {{-- 3. Informasi Tambahan --}}
            <div class="form-section-title">3. Informasi Tambahan</div>

            <div class="form-group">
                <label class="form-label">Detail Kebutuhan & Catatan Khusus</label>
                <textarea name="detail_kebutuhan" class="form-control" rows="5"
                    placeholder="Ceritakan tentang konsep, tema, kebutuhan teknis, atau catatan khusus yang perlu kami ketahui...">{{ old('detail_kebutuhan') }}</textarea>
            </div>

            {{-- Info box --}}
            <div class="form-info-box">
                <i class="bi bi-info-circle-fill"></i>
                <span>
                    Setelah mengirimkan permintaan ini, tim kami akan meninjau detailnya dan
                    menghubungi Anda dalam waktu <strong>24 jam</strong> dengan proposal awal
                    dan jadwal konsultasi.
                </span>
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <a href="{{ route('client.dashboard') }}" class="btn btn-outline"
                   style="margin-right:8px;">Batal</a>
                <button type="submit" class="btn btn-accent btn-lg">
                    Kirim Permintaan <i class="bi bi-send-fill"></i>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
