@extends('layouts.client')
@section('title','Ajukan Event Baru')
@section('page-title','Ajukan Event Baru')

@section('content')

<div class="page-header">
    <a href="{{ url()->previous() }}"
       onclick="event.preventDefault(); history.back();"
       class="back-link">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>
</div>

<div style="max-width:680px;margin:0 auto;">

    <div class="page-header">
        <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:6px;">
            Ajukan Event Baru
        </h1>
        <p style="color:var(--text-muted);">
            Isi formulir di bawah untuk mulai merencanakan event Anda bersama kami.
        </p>
    </div>

    <form action="{{ route('client.event.store') }}" method="POST" id="form-event-create" onsubmit="return confirmEventSubmit(this)">
        @csrf
        <div class="form-card">

            {{-- 1. Detail Event --}}
            <div class="form-section-title">1. Detail Event</div>

            <div class="form-group">
                <label class="form-label">
                    Nama Event <span class="text-red">*</span>
                </label>
                <input type="text" name="nama_event" class="form-control"
                       placeholder="mis. Konferensi Teknologi Tahunan 2026"
                       value="{{ old('nama_event') }}" required>
                @error('nama_event')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Jenis Event <span class="text-red">*</span>
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
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Jumlah Tamu
                    </label>
                    <input type="number" name="jumlah_tamu" class="form-control"
                           placeholder="contoh: 250"
                           value="{{ old('jumlah_tamu') }}" min="1">
                    @error('jumlah_tamu')
                    <span class="form-error">{{ $message }}</span>
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
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Tanggal Mulai Event <span class="text-red">*</span>
                    </label>
                    <input type="date" name="tanggal_event" class="form-control"
                           value="{{ old('tanggal_event') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('tanggal_event')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Tanggal Selesai Event <small style="color:#64748b; font-weight:normal;">(Opsional)</small>
                    </label>
                    <input type="date" name="tanggal_selesai" class="form-control"
                           value="{{ old('tanggal_selesai') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('tanggal_selesai')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 24px;">

            {{-- 2. Lokasi --}}
            <div class="form-section-title">2. Lokasi Event</div>

            <div class="form-group">
                <label class="form-label">
                    Lokasi Event <span class="text-red">*</span>
                </label>
                <input type="text" name="lokasi_event" class="form-control"
                       placeholder="contoh: Basko Grand Mall, Padang"
                       value="{{ old('lokasi_event') }}" required>
                @error('lokasi_event')
                <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 24px;">

            {{-- 3. Informasi Tambahan --}}
            <div class="form-section-title">3. Informasi Tambahan</div>

            <div class="form-group">
                <label class="form-label">Detail Kebutuhan & Catatan Khusus</label>
                <textarea name="detail_kebutuhan" class="form-control" rows="5"
                    placeholder="Tuliskan kebutuhan event anda secara lengkap atau catatan khusus yang perlu kami ketahui...">{{ old('detail_kebutuhan') }}</textarea>
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
                <button type="submit" class="btn btn-accent btn-lg" id="btn-submit-event">
                    Kirim Permintaan <i class="bi bi-send-fill"></i>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
/**
 * Konfirmasi pengajuan event baru oleh client
 */
function confirmEventSubmit(formEl) {
    Swal.fire({
        title: 'Konfirmasi Pengajuan Event',
        html: 'Apakah Anda yakin ingin mengirim permintaan event ini?<br><br>' +
              '<small style="color:#64748b;">Setelah dikirim, permintaan akan diterima oleh Admin dan menunggu proses peninjauan.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#14b8a6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="bi bi-send-fill"></i> Ya, Kirim Permintaan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { 
            popup: 'swal-alpha-popup',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel'
        },
        buttonsStyling: false
    }).then(function(result) {
        if (result.isConfirmed) {
            // Disable submit button to prevent double submission
            const btnSubmit = document.getElementById('btn-submit-event');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
                btnSubmit.style.opacity = '0.6';
            }
            
            // Submit form
            var origOnsubmit = formEl.onsubmit;
            formEl.onsubmit = null;
            formEl.submit();
            formEl.onsubmit = origOnsubmit;
        }
    });
    
    return false; // prevent default form submission
}
</script>

<style>
.swal-btn-confirm {
    background: #14b8a6 !important;
    color: white !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 11px 24px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.swal-btn-confirm:hover {
    opacity: 0.9 !important;
}

.swal-btn-cancel {
    background: white !important;
    color: #64748b !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
}

.swal-btn-cancel:hover {
    background: #f8fafc !important;
}
</style>
@endpush
