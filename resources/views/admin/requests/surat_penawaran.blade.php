@extends('layouts.admin')

@section('title', 'Surat Penawaran')
@section('page-title', 'Surat Penawaran')

@section('content')


{{-- Action Bar --}}
<div class="page-header" style="margin-bottom:24px;">
    <a href="{{ route('admin.requests.index') }}"
       style="display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:14px; text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Kembali ke Request
    </a>
    <div style="display:flex; gap:10px;" id="action-bar-buttons">
        {{-- Tombol mode VIEW (default) --}}
        <div id="btn-group-view" style="display:flex; gap:10px; align-items:center;">
            {{-- Tombol Lihat RAB --}}
            <a href="{{ route('admin.rab.index', ['event_id' => $event->id]) }}"
               class="btn btn-outline"
               style="border-color:#14b8a6; color:#14b8a6;">
                <i class="fas fa-calculator"></i> Lihat RAB
            </a>
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
                {{-- ── PEMBUATAN AWAL (v1) ── --}}
                @php
                    $ddmsMode = $ddmsEnabled && $ddmsDefaultPenawaran;
                @endphp
                <div style="display:inline-flex; align-items:center; gap:6px; margin-right:10px; vertical-align:middle;">
                    <input type="checkbox" name="uses_ddms_toggle" id="chk-penawaran-ddms" value="1"
                           {{ $ddmsMode ? 'checked' : '' }}
                           {{ $ddmsEnabled ? '' : 'disabled' }}
                           onchange="toggleDdmsEntry(this.checked)"
                           style="width:15px; height:15px; accent-color:#14b8a6; cursor:pointer;">
                    <label for="chk-penawaran-ddms" style="font-size:12px; color:#334155; cursor:{{ $ddmsEnabled ? 'pointer' : 'not-allowed' }};">
                        Gunakan DDMS untuk Surat Penawaran
                    </label>
                </div>

                {{-- NON-DDMS: Kirim Penawaran langsung ke Client (nomor dari form) --}}
                <form action="{{ route('admin.requests.kirim-penawaran', $event->id) }}" method="POST"
                      style="{{ $ddmsMode ? 'display:none;' : 'display:inline;' }}" id="form-kirim-penawaran">
                    @csrf
                    <input type="hidden" name="nomor_surat" value="{{ $nomorSurat }}">
                    <input type="hidden" name="tanggal_surat" value="{{ now()->format('Y-m-d') }}">
                    <input type="hidden" name="uses_ddms" value="0">
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirmKirimPenawaran(this.form)">
                        <i class="fas fa-paper-plane"></i> Kirim Penawaran
                    </button>
                </form>

                {{-- DDMS: Masuk ke DDMS (buat Proposal + Document draft).
                     Nomor surat TIDAK dikirim dari form — dikelola via Document Builder. --}}
                <form action="{{ route('admin.requests.masuk-ke-ddms', $event->id) }}" method="POST"
                      style="{{ $ddmsMode ? 'display:inline;' : 'display:none;' }}" id="form-masuk-ddms">
                    @csrf
                    <input type="hidden" name="uses_ddms" value="1">
                    <input type="hidden" name="tanggal_surat" value="{{ now()->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary"
                            style="background:#14b8a6; border-color:#14b8a6;"
                            title="Nomor dokumen dikelola melalui Document Builder (DDMS).">
                        <i class="fas fa-layer-group"></i> Masuk ke DDMS
                    </button>
                </form>
            @else
                {{-- ── PROPOSAL SUDAH ADA ── --}}
                @if($usesDdmsActive)
                    {{-- Banner status & nomor dokumen DDMS (sumber: DocumentNumbering) --}}
                    <div style="display:inline-block; vertical-align:middle; margin-right:10px; padding:6px 12px; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; font-size:12px; color:#065f46;">
                        <strong>DDMS: AKTIF</strong><br>
                        Status: {{ $ddmsStatusLabel ?? '-' }}<br>
                        Nomor Dokumen: {{ $ddmsDocNumber ?? 'Belum diatur (atur di Document Builder)' }}
                    </div>

                    {{-- Buka DDMS: navigasi ke Document Builder existing --}}
                    <a href="{{ route('admin.document_builder.preview', $ddmsDocument->id) }}"
                       class="btn btn-outline" style="border-color:#14b8a6; color:#0d9488;">
                        <i class="fas fa-external-link-alt"></i> Buka DDMS
                    </a>

                    @if($ddmsApproved)
                        {{-- approved/published → Kirim ke Client AKTIF --}}
                        <form action="{{ route('admin.requests.kirim-revisi-penawaran', $event->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="uses_ddms" value="1">
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirmKirimPenawaran(this.form)">
                                <i class="fas fa-paper-plane"></i> Kirim ke Client
                            </button>
                        </form>
                        {{-- Masuk ke DDMS (untuk membuat revisi v2 → Document B) --}}
                        <form action="{{ route('admin.requests.masuk-ke-ddms', $event->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="uses_ddms" value="1">
                            <input type="hidden" name="tanggal_surat" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline"
                                    style="border-color:#14b8a6; color:#0d9488;"
                                    title="Nomor dokumen dikelola melalui Document Builder (DDMS).">
                                <i class="fas fa-layer-group"></i> Masuk ke DDMS (Revisi)
                            </button>
                        </form>
                    @else
                        {{-- Document belum approved → Kirim ke Client DISABLED --}}
                        <button type="button" class="btn btn-primary" disabled data-ddms-locked
                                style="background:#cbd5e1; border-color:#cbd5e1; color:#64748b; cursor:not-allowed;"
                                title="Surat Penawaran dapat dikirim ke Client setelah Document DDMS disetujui Director.">
                            <i class="fas fa-paper-plane"></i> Kirim ke Client
                        </button>
                    @endif
                @else
                    {{-- NON-DDMS (revisi) ── --}}
                    <form action="{{ route('admin.requests.kirim-revisi-penawaran', $event->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="uses_ddms" value="0">
                        <button type="submit" class="btn btn-primary"
                                onclick="return swalSend(this.form, 'Kirim Revisi Penawaran?', 'Revisi penawaran akan dikirim ke client.')">
                            <i class="fas fa-sync-alt"></i> Revisi Penawaran
                        </button>
                    </form>
                @endif
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

{{-- ── Kebutuhan Event dari Client (Read Only) ── --}}
<div style="margin-bottom:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
    <div style="display:flex; align-items:center; gap:10px; padding:14px 20px; background:#fff; border-bottom:1px solid #e2e8f0;">
        <div style="width:34px; height:34px; background:#eff6ff; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#3b82f6; font-size:15px; flex-shrink:0;">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div>
            <div style="font-size:14px; font-weight:700; color:#0f172a; line-height:1.2;">Kebutuhan Event dari Client</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:1px;">Informasi ini diisi oleh client saat mengajukan event &mdash; hanya baca.</div>
        </div>
    </div>
    <div style="padding:16px 20px;">
        @if(!empty($event->detail_kebutuhan))
            <div style="font-size:13.5px; color:#334155; line-height:1.75; white-space:pre-wrap;">{{ $event->detail_kebutuhan }}</div>
        @else
            <div style="font-size:13px; color:#94a3b8; font-style:italic;">Belum ada kebutuhan tambahan dari client.</div>
        @endif
    </div>
</div>

{{-- Preview Surat --}}
<div style="background:#f1f5f9; padding:24px; border-radius:12px;">
<div id="surat-preview" style="background:white; max-width:820px; margin:0 auto; padding:0; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,0.08); font-family:'Times New Roman',serif; font-size:13px; line-height:1.7; color:#111; overflow:hidden;">

    {{-- KOP SURAT (tidak dapat diedit - template tetap) --}}
    @include('admin.pdf_templates.partials.header_web')

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
                    <span class="field-view" id="view-nomor_surat">{{ $usesDdmsActive ? ($ddmsDocNumber ?? $nomorSurat) : $nomorSurat }}</span>
                    {{-- EDIT --}}
                    @if($usesDdmsActive)
                        <span class="field-edit" style="display:none; font-size:12px; color:#0d9488;">
                            Nomor dokumen dikelola melalui Document Builder (DDMS aktif).
                        </span>
                    @else
                        <input class="field-edit surat-input" id="edit-nomor_surat"
                               type="text" name="nomor_surat_override"
                               value="{{ $nomorSurat }}"
                               style="display:none;">
                    @endif
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
            <strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong>
        </div>
        <div style="margin-bottom:16px;">
            Di,<br>
            <span style="padding-left:20px;">Tempat</span>
        </div>

        <hr style="border:none; border-top:0.5px solid #bbb; margin:10px 0 14px;">

        {{-- Pembuka (tidak dapat diedit - template tetap) --}}
        <p style="text-align:justify;margin-bottom:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dengan hormat, kami dari <strong>CV. Alpha Multi Organizer</strong>
            Perusahaan yang bergerak di bidang Event Organizer. Dengan ini menawarkan kegiatan <strong>{{ $event->jenis_event }}</strong> kepada <strong>{{ $event->client->name ?? 'Client' }}</strong>
            di <strong>{{ $event->lokasi_event ?? '-' }}</strong>, maka dengan ini kami mengajukan surat penawaran <strong>"Special Price"</strong> sebagai berikut :
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
                                    <strong>{{ $event->rentang_anggaran ?? '-' }}
                                    @if($event->include_ppn ?? true)
                                        <small>(Include PPN &amp; PPh)*</small>
                                    @endif
                                    </strong><br>
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
                                    {{-- Checkbox Include PPN: hidden + checkbox agar false pun terkirim --}}
                                    <input type="hidden" name="include_ppn" value="0">
                                    <label style="display:flex; align-items:center; gap:6px; margin-top:6px; font-size:12px; color:#334155; cursor:pointer; font-family:Arial,sans-serif;">
                                        <input type="checkbox" name="include_ppn" value="1" id="chk-include-ppn"
                                               {{ ($event->include_ppn ?? true) ? 'checked' : '' }}
                                               style="width:14px; height:14px; accent-color:#1a6fa8; cursor:pointer;">
                                        Include PPN &amp; PPh
                                    </label>
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
                    </span>
                    {{-- EDIT --}}
                    <textarea class="field-edit surat-input" id="edit-detail_kebutuhan"
                              name="detail_kebutuhan"
                              rows="5"
                              style="display:none; width:100%; resize:vertical;"
                              placeholder="Tulis fasilitas/kebutuhan (satu per baris)..."></textarea>
                </td>
            </tr>
        </table>

        {{-- Ketentuan lain (tidak dapat diedit - template tetap) --}}
        <div style="font-weight:700; margin-bottom:6px; display:flex; gap: 11px;">
            <div style="border:none; padding:3px 4px; font-weight:700; vertical-align:top;">V.</div>
            <div style="border:none; padding:3px 4px; vertical-align:top;">Ketentuan lain :</div>
            <div style="border:none; padding:3px 4px; vertical-align:top; text-align:center;">:</div>
        </div>
        <ol style="padding-left:48px; margin-bottom:14px; list-style-type:lower-alpha; font-size:13px;">
            <li style="margin-bottom:5px; text-align:justify;">Loading In dan Out Barang Jam 22.00 wib sd selesai dan wajib diberitahukan kepada manajemen Alpha Organizer.</li>
            <li style="margin-bottom:5px; text-align:justify;">Segala bentuk izin dan pajak diurus sendiri oleh penyewa.</li>
            <li style="margin-bottom:5px; text-align:justify;">Pembayaran dilakukan melalui Transfer <strong>Bank BRI A.n CV ALPHA MULTI ORGANIZER No Rek. 005801006983568</strong>.</li>
            <li style="margin-bottom:5px; text-align:justify;">Biaya yang tersebut diatas belum termasuk biaya SPSI (jika ada)</li>
            <li style="margin-bottom:5px; text-align:justify;">Pemakai Jasa Penyelenggara wajib mematuhi semua peraturan dan tata tertib yang berlaku di {{ $event->lokasi_event ?? '-' }}.</li>
            <li style="margin-bottom:5px; text-align:justify;">Pemakai Jasa Penyelenggara wajib mengasuransikan produknya selama pameran berlangsung. Kerusakan dan kehilangan barang di saat pameran yang diakibatkan oleh human error dan forced majure bukan tanggung jawab dari pemakai jasa penyelenggara.</li>
        </ol>

        {{-- Penutup (tidak dapat diedit) --}}
        <p style="text-align:justify;margin-bottom:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dengan hormat, kami dari <strong>CV. Alpha Multi Organizer</strong>
            Perusahaan yang bergerak di bidang Event Organizer. Dengan ini menawarkan kegiatan <strong>{{ $event->jenis_event }}</strong> kepada <strong>{{ $event->client->name ?? 'Client' }}</strong>
            di <strong>{{ $event->lokasi_event ?? '-' }}</strong>, maka dengan ini kami mengajukan surat penawaran <strong>"Special Price"</strong> sebagai berikut :
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
/* ─── HEADER ─── */
.logo-img { width: 130px; }
.contact-col { text-align: right; font-size: 11.5px; line-height: 2.1; color: #333; font-family: Arial, sans-serif; }
.divider-thick { margin-top: 7px; border-top: 4px solid #00b8b0; }
.divider-thin  { border-top: 1.5px solid #00b8b0; margin-top: 2px; }

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
 * Konfirmasi kirim surat penawaran oleh Admin
 */
function confirmKirimPenawaran(formEl) {
    Swal.fire({
        title: 'Kirim Surat Penawaran',
        html: 'Apakah Anda yakin ingin mengirim surat penawaran kepada client?<br><br>' +
              '<small style="color:#64748b;">Client akan menerima notifikasi dan dapat melihat surat penawaran pada dashboard mereka.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#14b8a6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kirim Penawaran',
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
            const btnSubmit = formEl.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-hourglass-half"></i> Mengirim...';
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

/**
 * Toggle mode view â†” edit pada halaman Preview Surat Penawaran.
 * Hanya mengubah visibilitas elemen â€” desain surat tidak berubah.
 */
/**
 * Toggle antara form "Kirim Penawaran" (NON-DDMS) dan "Masuk ke DDMS"
 * (DDMS) pada pembuatan awal Surat Penawaran, mengikuti checkbox.
 */
function toggleDdmsEntry(checked) {
    const fKirim = document.getElementById('form-kirim-penawaran');
    const fDdms  = document.getElementById('form-masuk-ddms');
    if (fKirim) fKirim.style.display = checked ? 'none' : 'inline';
    if (fDdms)  fDdms.style.display  = checked ? 'inline' : 'none';
}

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
