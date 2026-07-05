@extends('layouts.admin')

@section('title', 'Surat Penawaran')
@section('page-title', 'Surat Penawaran')

@section('content')


{{-- Action Bar --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <a href="{{ route('admin.requests.index') }}"
       style="display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:14px; text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Kembali ke Request
    </a>
    <div style="display:flex; gap:10px;" id="action-bar-buttons">
        {{-- Tombol mode VIEW (default) --}}
        <div id="btn-group-view">
            <a href="{{ route('admin.requests.export-pdf', $event->id) }}"
               class="btn btn-outline" target="_blank">
                <i class="fas fa-download"></i> Export PDF
            </a>
            <button class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            @if($isLocked)

            {{-- Surat sudah diterima client --}}
            <button type="button"
                    class="btn btn-outline"
                    disabled
                    style="border-color:#cbd5e1;
                        color:#94a3b8;
                        background:#f8fafc;
                        cursor:not-allowed;"
                    title="Surat penawaran telah diterima oleh client sehingga tidak dapat diedit.">

                <i class="fas fa-lock"></i>
                Edit Surat

            </button>

            <button type="button"
                    class="btn btn-secondary"
                    disabled
                    style="background:#cbd5e1;
                        border-color:#cbd5e1;
                        color:#64748b;
                        cursor:not-allowed;"
                    title="Surat penawaran telah diterima oleh client sehingga tidak dapat direvisi.">

                <i class="fas fa-lock"></i>
                Revisi Penawaran
            </button>
        @else
            {{-- Tombol Edit Surat --}}
            <button type="button" class="btn btn-outline" id="btn-edit-surat" onclick="toggleEditMode(true)"
                    style="border-color:#f59e0b; color:#b45309;">
                <i class="fas fa-pen"></i> Edit Surat
            </button>
            @if(!$event->latestProposal)
            <form action="{{ route('admin.requests.kirim-penawaran', $event->id) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="nomor_surat" value="{{ $nomorSurat }}">
                <input type="hidden" name="tanggal_surat" value="{{ now()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-primary"
                        onclick="return swalSend(this.form, 'Kirim Surat Penawaran?', 'Surat penawaran akan dikirim ke client {{ addslashes($event->client->name ?? '') }}.'))">
                    <i class="fas fa-paper-plane"></i> Kirim Penawaran
                </button>
            </form>
            @else
            <form action="{{ route('admin.requests.kirim-revisi-penawaran', $event->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary"
                        onclick="return swalSend(this.form, 'Kirim Revisi Penawaran?', 'Revisi penawaran akan dikirim ke client.')">
                    <i class="fas fa-sync-alt"></i> Revisi Penawaran
                </button>
            </form>
            @endif
        @endif
        </div>

        {{-- Tombol mode EDIT (tersembunyi saat view) --}}
        <div id="btn-group-edit" style="display:none; gap:10px;">
            <button type="button" class="btn btn-outline" onclick="toggleEditMode(false)"
                    style="border-color:#94a3b8; color:#64748b;">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" class="btn btn-primary" onclick="submitEditForm()"
                    style="background:#16a34a; border-color:#16a34a;">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

{{-- Form edit (method PATCH) --}}
<form id="form-edit-surat"
      action="{{ route('admin.requests.update-surat-penawaran', $event->id) }}"
      method="POST" style="display:contents;">
    @csrf
    @method('PATCH')

{{-- Preview Surat --}}
<div style="background:#f1f5f9; padding:24px; border-radius:12px;">
<div id="surat-preview" style="background:white; max-width:820px; margin:0 auto; padding:0; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,0.08); font-family:'Times New Roman',serif; font-size:13px; line-height:1.7; color:#111; overflow:hidden;">

    {{-- KOP SURAT (tidak dapat diedit - template tetap) --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; padding:20px 40px 14px;">
        <div>
            <div style="font-size:30px; font-weight:900; color:#1a6fa8; letter-spacing:3px; font-family:'Arial Black',Arial,sans-serif; line-height:1;">ALPHA</div>
            <div style="font-size:10px; letter-spacing:5px; color:#1a6fa8; font-family:Arial,sans-serif; font-weight:700; margin-top:2px;">ORGANIZER</div>
        </div>
        <div style="text-align:right; font-size:11.5px; font-family:Arial,sans-serif; color:#333; line-height:1.9;">
            <div>+62 822-3318-1883</div>
            <div>alphaorganizer1209@gmail.com</div>
            <div>Jl.Air Dingin No.25 Kec.Koto Tangah, Kota Padang</div>
        </div>
    </div>
    <div style="height:3px; background:#1a6fa8; margin:0;"></div>

    {{-- BADAN SURAT --}}
    <div style="padding:24px 40px 40px;">

        <div style="text-align:right; margin-bottom:16px;">Padang, {{ now()->translatedFormat('d F Y') }}</div>

        {{-- Meta surat --}}
        <table style="border:none; border-collapse:collapse; margin-bottom:16px; font-size:13px;">
            <tr>
                <td style="border:none; padding:1px 4px; min-width:90px;">No. Surat</td>
                <td style="border:none; padding:1px 8px 1px 4px;">:</td>
                <td style="border:none; padding:1px 4px;">
                    {{-- VIEW --}}
                    <span class="field-view" id="view-nomor_surat">{{ $nomorSurat }}</span>
                    {{-- EDIT --}}
                    <input class="field-edit surat-input" id="edit-nomor_surat"
                           type="text" name="nomor_surat_override"
                           value="{{ $nomorSurat }}"
                           style="display:none;">
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:1px 4px;">Lampiran</td>
                <td style="border:none; padding:1px 8px 1px 4px;">:</td>
                <td style="border:none; padding:1px 4px;">-</td>
            </tr>
            <tr>
                <td style="border:none; padding:1px 4px;">Perihal</td>
                <td style="border:none; padding:1px 8px 1px 4px;">:</td>
                <td style="border:none; padding:1px 4px;">

                    {{-- VIEW --}}
                    <span class="field-view" id="view-perihal">
                        {{ $event->perihal ?? 'Surat Penawaran Pameran Otomotif' }}
                    </span>
                    {{-- EDIT --}}
                    <input
                        class="field-edit surat-input"
                        id="edit-perihal"
                        type="text"
                        name="perihal"
                        value="{{ old('perihal', $event->perihal ?? 'Surat Penawaran Pameran Otomotif') }}"
                        style="display:none;">

                </td>
            </tr>
        </table>

        {{-- Kepada (tidak dapat diedit) --}}
        <div style="margin-bottom:8px;">
            Kepada Yth<br>
            Kepala Cabang Dealer Mobil <strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong>
        </div>
        <div style="margin-bottom:16px;">
            Di,<br>
            <span style="padding-left:20px;">Tempat</span>
        </div>

        <hr style="border:none; border-top:0.5px solid #bbb; margin:10px 0 14px;">

        {{-- Pembuka (tidak dapat diedit - template tetap) --}}
        <p style="text-align:justify; margin-bottom:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dengan hormat, kami dari <strong>CV. Alpha Multi Organizer</strong>
            Perusahaan yang bergerak di bidang Event Organizer. Dengan ini menawarkan kegiatan pameran
            pameran otomotif mobil kepada <strong>{{ $event->client->name ?? 'Client' }}</strong>
            di Basko City Mall Padang, maka dengan ini kami mengajukan surat penawaran <strong>"Special Price"</strong> sebagai berikut :
        </p>

        {{-- Rincian --}}
        <table style="border:none; border-collapse:collapse; width:100%; font-size:13px; margin-bottom:14px;">
            {{-- I. Lokasi --}}
            <tr>
                <td style="border:none; padding:3px 4px; width:32px; font-weight:700; vertical-align:top;">I.</td>
                <td style="border:none; padding:3px 4px; width:120px; vertical-align:top;">Lokasi</td>
                <td style="border:none; padding:3px 4px; width:16px; vertical-align:top; text-align:center;">:</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">
                    <span class="field-view" id="view-lokasi_event">{{ $event->lokasi_event ?? '-' }}</span>
                    <input class="field-edit surat-input" id="edit-lokasi_event"
                           type="text" name="lokasi_event"
                           value="{{ $event->lokasi_event }}"
                           style="display:none;">
                </td>
            </tr>
            {{-- II. Jenis Kegiatan --}}
            <tr>
                <td style="border:none; padding:3px 4px; font-weight:700; vertical-align:top;">II.</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">Jenis Kegiatan</td>
                <td style="border:none; padding:3px 4px; vertical-align:top; text-align:center;">:</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">
                    <span class="field-view" id="view-jenis_event">{{ $event->jenis_event ?? '-' }}</span>
                    <input class="field-edit surat-input" id="edit-jenis_event"
                           type="text" name="jenis_event"
                           value="{{ $event->jenis_event }}"
                           style="display:none;">
                </td>
            </tr>
            {{-- III. Jadwal --}}
            <tr>
                <td style="border:none; padding:3px 4px; font-weight:700; vertical-align:top;">III.</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">Jadwal</td>
                <td style="border:none; padding:3px 4px; vertical-align:top; text-align:center;">:</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">
                    <table style="border:none; border-collapse:collapse; width:100%;">
                        <tr>
                            <td style="border:none; padding:2px 3px; width:18px;">a.</td>
                            <td style="border:none; padding:2px 3px; width:80px;">Jadwal</td>
                            <td style="border:none; padding:2px 3px; width:14px; text-align:center;">:</td>
                            <td style="border:none; padding:2px 3px;">
                                {{-- VIEW --}}
                                <span class="field-view" id="view-jadwal">
                                    {{ $event->tanggal_event?->translatedFormat('d F Y') ?? '-' }}
                                    @if($event->tanggal_selesai ?? null)
                                        s/d {{ $event->tanggal_selesai->translatedFormat('d F Y') }}
                                        ({{ $event->tanggal_event->diffInDays($event->tanggal_selesai) + 1 }} hari)
                                    @endif
                                </span>
                                {{-- EDIT --}}
                                <span class="field-edit" style="display:none;">
                                    <input type="date" name="tanggal_event" class="surat-input"
                                           value="{{ $event->tanggal_event?->format('Y-m-d') }}"
                                           style="width:140px;">
                                    <span style="font-size:12px; margin:0 4px;">s/d</span>
                                    <input type="date" name="tanggal_selesai" class="surat-input"
                                           value="{{ $event->tanggal_selesai?->format('Y-m-d') }}"
                                           style="width:140px;">
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none; padding:2px 3px;">b.</td>
                            <td style="border:none; padding:2px 3px;">Luas Area</td>
                            <td style="border:none; padding:2px 3px; text-align:center;">:</td>
                            <td style="border:none; padding:2px 3px;">
                                <span class="field-view" id="view-luas_area">{{ $event->luas_area ?? '-' }}</span>
                                <input class="field-edit surat-input" id="edit-luas_area"
                                       type="text" name="luas_area"
                                       value="{{ $event->luas_area }}"
                                       placeholder="cth: 6x6 M"
                                       style="display:none;">
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none; padding:2px 3px;">c.</td>
                            <td style="border:none; padding:2px 3px;">Price</td>
                            <td style="border:none; padding:2px 3px; text-align:center;">:</td>
                            <td style="border:none; padding:2px 3px;">
                                {{-- VIEW --}}
                                <span class="field-view" id="view-price">
                                    <strong>{{ $event->rentang_anggaran ?? '-' }} <small>(Exclude Ppn &amp; Pph)*</small></strong><br>
                                    @if($event->terbilang ?? null)
                                    <small>({{ $event->terbilang }})</small>
                                    @endif
                                </span>
                                {{-- EDIT --}}
                                <span class="field-edit" style="display:none;">
                                    <input type="text" name="rentang_anggaran" class="surat-input"
                                           value="{{ $event->rentang_anggaran }}"
                                           placeholder="cth: Rp 15.000.000,-"
                                           style="width:200px;">
                                    <small style="display:block; margin-top:4px; color:#64748b;">(Exclude Ppn &amp; Pph)*</small>
                                    <input type="text" name="terbilang" class="surat-input"
                                           value="{{ $event->terbilang }}"
                                           placeholder="Terbilang (opsional)"
                                           style="width:200px; margin-top:4px;">
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            {{-- IV. Fasilitas --}}
            <tr>
                <td style="border:none; padding:3px 4px; font-weight:700; vertical-align:top;">IV.</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">Fasilitas</td>
                <td style="border:none; padding:3px 4px; vertical-align:top; text-align:center;">:</td>
                <td style="border:none; padding:3px 4px; vertical-align:top;">
                    {{-- VIEW --}}
                    <span class="field-view" id="view-detail_kebutuhan">
                        @if($event->detail_kebutuhan ?? null)
                            {!! nl2br(e($event->detail_kebutuhan)) !!}
                        @else
                            1. Manajemen acara penuh (Full Event Management)<br>
                            2. Koordinasi vendor dan logistik<br>
                            3. Setup dan dekorasi standar sesuai tema<br>
                            4. Tim lapangan profesional selama acara berlangsung
                        @endif
                    </span>
                    {{-- EDIT --}}
                    <textarea class="field-edit surat-input" id="edit-detail_kebutuhan"
                              name="detail_kebutuhan"
                              rows="5"
                              style="display:none; width:100%; resize:vertical;"
                              placeholder="Tulis fasilitas/kebutuhan (satu per baris)...">{{ $event->detail_kebutuhan }}</textarea>
                </td>
            </tr>
        </table>

        {{-- Ketentuan lain (tidak dapat diedit - template tetap) --}}
        <div style="font-weight:700; margin-bottom:6px;">V.&nbsp;&nbsp;Ketentuan lain :</div>
        <ol style="padding-left:22px; margin-bottom:14px; list-style-type:lower-alpha; font-size:13px;">
            <li style="margin-bottom:5px; text-align:justify;">Loading In dan Out Barang Jam 22.00 wib sd selesai dan wajib diberitahukan kepada manajemen Alpha Organizer.</li>
            <li style="margin-bottom:5px; text-align:justify;">Segala bentuk izin dan pajak diurus sendiri oleh penyewa.</li>
            <li style="margin-bottom:5px; text-align:justify;">Pembayaran dilakukan melalui Transfer <strong>Bank BRI A.n CV ALPHA MULTI ORGANIZER No Rek. 005801006983568</strong>.</li>
            <li style="margin-bottom:5px; text-align:justify;">Biaya yang tersebut diatas belum termasuk biaya SPSI (jika ada)</li>
            <li style="margin-bottom:5px; text-align:justify;">Pemakai Jasa Penyelenggara wajib mematuhi semua peraturan dan tata tertib yang berlaku di Basko City Mall Padang.</li>
            <li style="margin-bottom:5px; text-align:justify;">Pemakai Jasa Penyelenggara wajib mengasuransikan produknya selama pameran berlangsung. Kerusakan dan kehilangan barang di saat pameran yang diakibatkan oleh human error dan forced majure bukan tanggung jawab dari pemakai jasa penyelenggara.</li>
        </ol>

        {{-- Penutup (tidak dapat diedit) --}}
        <p style="text-align:justify; margin-top:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Apabila ada hal-hal yang perlu dipertanyakan, silahkan menghubungi Sdra. Fajar Viliano
            0895-4013-00022 atau melalui email : alphaorganizer1209@gmail.com. Demikianlah surat
            penawaran ini kami buat, Atas perhatian dan kerjasamanya kami ucapkan terimakasih.
        </p>

        {{-- Tanda Tangan (tidak dapat diedit) --}}
        <div style="margin-top:24px;">
            <div>Padang, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Hormat kami,</div>
            <div style="height:64px;"></div>
            <div style="font-weight:700; text-decoration:underline;">{{ auth()->user()->name }}</div>
            <div>Direktur</div>
        </div>

    </div>{{-- /badan-surat --}}
</div>{{-- /surat-preview --}}
</div>

</form>{{-- /form-edit-surat --}}

@endsection

@push('styles')
<style>
/* ── Input dalam surat ── */
.surat-input {
    font-family: 'Times New Roman', serif;
    font-size: 13px;
    line-height: 1.7;
    color: #111;
    border: 1.5px solid #f59e0b;
    border-radius: 4px;
    padding: 2px 6px;
    background: #fffbeb;
    outline: none;
    width: 100%;
    box-sizing: border-box;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.surat-input:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
}
/* Mode edit: highlight border surat */
#surat-preview.edit-mode {
    outline: 2px dashed #f59e0b;
    outline-offset: 4px;
}
/* Banner edit mode */
#edit-mode-banner {
    display: none;
    background: #fef3c7;
    border: 1px solid #fcd34d;
    color: #92400e;
    font-size: 12.5px;
    padding: 7px 16px;
    border-radius: 6px;
    margin-bottom: 12px;
    text-align: center;
}
#edit-mode-banner.visible { display: block; }

@media print {
    .sidebar, .topbar, .page-header,
    [style*="display:flex; align-items:center; justify-content:space-between"],
    #edit-mode-banner {
        display: none !important;
    }
    .field-edit { display: none !important; }
    .field-view { display: inline !important; }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    #surat-preview {
        box-shadow: none !important;
        max-width: 100% !important;
        border-radius: 0 !important;
        outline: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
/**
 * Toggle mode view ↔ edit pada halaman Preview Surat Penawaran.
 * Hanya mengubah visibilitas elemen — desain surat tidak berubah.
 */
function toggleEditMode(editOn) {
    const viewEls   = document.querySelectorAll('.field-view');
    const editEls   = document.querySelectorAll('.field-edit');
    const preview   = document.getElementById('surat-preview');
    const banner    = document.getElementById('edit-mode-banner');
    const btnView   = document.getElementById('btn-group-view');
    const btnEdit   = document.getElementById('btn-group-edit');

    if (editOn) {
        // Sembunyikan teks, tampilkan input
        viewEls.forEach(el => el.style.display = 'none');
        editEls.forEach(el => el.style.display  = '');
        // Tombol action bar
        btnView.style.display = 'none';
        btnEdit.style.display = 'flex';
        // Visual indicator
        preview.classList.add('edit-mode');
        if (banner) banner.classList.add('visible');
    } else {
        // Kembali ke mode view
        viewEls.forEach(el => el.style.display = '');
        editEls.forEach(el => el.style.display  = 'none');
        btnView.style.display = 'flex';
        btnEdit.style.display = 'none';
        preview.classList.remove('edit-mode');
        if (banner) banner.classList.remove('visible');
    }
}

function submitEditForm() {
    swalSave(
        function() { document.getElementById('form-edit-surat').submit(); },
        'Simpan Perubahan?',
        'Perubahan data surat penawaran akan disimpan.'
    );
}
</script>
@endpush