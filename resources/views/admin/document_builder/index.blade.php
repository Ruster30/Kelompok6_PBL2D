@extends('layouts.admin')

@section('title', 'Document Builder')
@section('page-title', 'Document Builder')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Dokumen</h1>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="tabs">
    <a href="{{ route('admin.documents.index') }}" class="tab-link">Dokumen Umum</a>
    <a href="{{ route('admin.document_builder.index') }}" class="tab-link active">Document Builder</a>
</div>


<div class="tab-content">

    {{-- FORM GENERATE--}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:28px 32px;margin-bottom:24px;">
        <h2 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:6px;">
            <i class="fas fa-file-alt" style="color:#6366f1;margin-right:6px;"></i>
            Buat Dokumen
        </h2>
        <p style="color:#64748b;font-size:13px;margin-bottom:24px;">
            Pilih event dan jenis dokumen, lalu klik <strong>Generate</strong> untuk membuat dokumen secara otomatis.
        </p>

        <form id="docBuilderForm">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

                {{-- Pilih Event --}}
                <div class="form-group">
                    <label class="form-label" for="event_id">
                        <i class="fas fa-calendar-alt" style="color:#6366f1;margin-right:4px;"></i>
                        Event
                    </label>
                    <select name="event_id" id="event_id" class="form-input" required>
                        <option value="">-- Pilih Event --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}"
                                @selected($selectedEventId === $event->id)
                                data-client="{{ $event->client->name ?? '-' }}"
                                data-tanggal="{{ $event->tanggal_event?->format('d M Y') ?? '-' }}"
                                data-lokasi="{{ $event->lokasi_event ?? '-' }}"
                                data-status="{{ $event->status_label ?? $event->status_event }}">
                                {{ $event->nama_event }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Dokumen --}}
                <div class="form-group">
                    <label class="form-label" for="jenis_dokumen">
                        <i class="fas fa-file-invoice" style="color:#6366f1;margin-right:4px;"></i>
                        Jenis Dokumen
                    </label>
                    <select name="jenis_dokumen" id="jenis_dokumen" class="form-input" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        <option value="surat_kontrak" @selected($selectedJenis === 'surat_kontrak')>📑 Surat Kontrak</option>
                        <option value="invoice"       @selected($selectedJenis === 'invoice')>🧾 Invoice</option>
                        <option value="rab"           @selected($selectedJenis === 'rab')>📊 RAB (Rencana Anggaran Biaya)</option>
                    </select>
                </div>
            </div>

            {{-- Info Card Event (muncul setelah pilih event) --}}
            <div id="eventInfoCard" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;margin-bottom:24px;">
                <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;font-size:12px;">
                    <div><span style="color:#64748b;">Client</span><br><strong id="infoClient">-</strong></div>
                    <div><span style="color:#64748b;">Tanggal Event</span><br><strong id="infoTanggal">-</strong></div>
                    <div><span style="color:#64748b;">Lokasi</span><br><strong id="infoLokasi">-</strong></div>
                    <div><span style="color:#64748b;">Status</span><br><strong id="infoStatus">-</strong></div>
                </div>
            </div>

            {{-- Info Card Jenis Dokumen --}}
            <div id="jenisInfoCard" style="display:none;margin-bottom:24px;">
                <div id="jenisInfoContent"></div>
            </div>

                        {{-- Skema Pembayaran (khusus Invoice) --}}
            <div id="paymentSchemeSection" style="display:none; background:#f0fdf9; border:1px solid #a7f3d0; border-radius:10px; padding:20px; margin-bottom:24px;">
                <h3 style="font-size:15px; font-weight:700; color:#065f46; margin-bottom:14px;">
                    <i class="fas fa-credit-card" style="margin-right:6px;"></i>
                    Skema Pembayaran
                </h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group">
                        <label class="form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran" id="doc_jenis_pembayaran" class="form-input" onchange="toggleDocPaymentScheme()">
                            <option value="full_payment">Full Payment</option>
                            <option value="dp_dan_pelunasan">DP + Pelunasan</option>
                        </select>
                    </div>
                    <div class="form-group" id="doc_dp_mode_group" style="display:none;">
                        <label class="form-label">Mode DP</label>
                        <select name="mode_dp" id="doc_mode_dp" class="form-input" onchange="toggleDocDpMode()">
                            <option value="persentase">Persentase (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                    </div>
                </div>
                <div id="doc_dp_persentase_group" style="display:none; margin-bottom:14px;">
                    <div class="form-group" style="max-width:200px;">
                        <label class="form-label">Persentase DP (%)</label>
                        <input type="number" name="persentase_dp" id="doc_persentase_dp" class="form-input" value="30" min="1" max="100" step="0.01" oninput="hitungDocSkema()">
                    </div>
                </div>
                <div id="doc_dp_nominal_group" style="display:none; margin-bottom:14px;">
                    <div class="form-group" style="max-width:250px;">
                        <label class="form-label">Nominal DP (Rp)</label>
                        <input type="number" name="nilai_dp" id="doc_nilai_dp" class="form-input" value="0" min="1" step="1" oninput="hitungDocSkema()">
                    </div>
                </div>
                <div id="doc_preview" style="display:none; background:#fff; border-radius:8px; padding:14px; border:1px solid #d1fae5;">
                    <div style="font-weight:600; font-size:13px; color:#065f46; margin-bottom:8px;">Preview</div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:13px;">
                        <span style="color:#64748b;">Total Dibayar Klien</span>
                        <span id="doc_preview_total" style="font-weight:600;">Rp 0</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:13px; border-top:1px solid #e2e8f0;">
                        <span style="color:#64748b;">DP</span>
                        <span id="doc_preview_dp" style="font-weight:600; color:#16a34a;">Rp 0</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:13px; border-top:1px solid #e2e8f0;">
                        <span style="color:#64748b;">Sisa Pelunasan</span>
                        <span id="doc_preview_sisa" style="font-weight:600; color:#0f766e;">Rp 0</span>
                    </div>
                </div>
                <input type="hidden" name="has_payment_scheme" id="has_payment_scheme" value="0">
            </div>

            {{-- Upload Denah/Layout (khusus Surat Kontrak) --}}
            <div id="denahSection" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:20px; margin-bottom:24px;">
                <h3 style="font-size:15px; font-weight:700; color:#166534; margin-bottom:14px;">
                    <i class="fas fa-map-marked-alt" style="margin-right:6px;"></i>
                    Upload Denah / Layout Lokasi
                </h3>
                <p style="color:#475569; font-size:13px; margin-bottom:14px;">
                    Unggah denah atau layout lokasi yang akan ditampilkan pada halaman terakhir PDF Surat Kontrak.
                </p>
                <div style="display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:200px;">
                        <input type="file" id="denahFileInput" accept=".jpg,.jpeg,.png,.webp" class="form-input" style="padding:8px; font-size:13px;">
                        <div style="margin-top:6px; font-size:11px; color:#94a3b8;">
                            Format: JPG, JPEG, PNG, WEBP. Maks: 5 MB.
                        </div>
                    </div>
                    <button type="button" id="btnUploadDenah"
                        style="background:#16a34a; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:600; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
                        onclick="uploadDenah()">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                    <button type="button" id="btnHapusDenah"
                        style="display:none; background:#dc2626; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:600; font-size:13px; cursor:pointer;"
                        onclick="hapusDenah()">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
                <div id="denahPreview" style="display:none; margin-top:14px; padding:12px; background:#fff; border-radius:8px; border:1px solid #e2e8f0; text-align:center;">
                    <img id="denahPreviewImg" src="" alt="Denah Preview" style="max-width:100%; max-height:240px; border-radius:6px;">
                </div>
                <div id="denahUploadStatus" style="display:none; margin-top:10px; font-size:13px;"></div>
                <input type="hidden" id="denahFilePath" value="{{ $selectedEventId ? $events->firstWhere('id', $selectedEventId)?->layout_denah : '' }}">
            </div>

            {{-- Mode DDMS / Non-DDMS --}}
            <div style="margin-bottom:24px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:{{ $ddmsEnabled ? 'pointer' : 'not-allowed' }};">
                    <input type="checkbox" id="uses_ddms" name="uses_ddms" value="1"
                           style="margin-top:3px;"
                           {{ $ddmsEnabled ? 'checked' : 'disabled' }}>
                    <div>
                        <div style="font-weight:600;font-size:13.5px;color:#0f172a;">Gunakan DDMS</div>
                        @if($ddmsEnabled)
                            <small style="color:#64748b;font-size:12px;">Dokumen ini akan melalui approval Director, PIN approval, QR, dan verifikasi publik.</small>
                        @else
                            <small style="color:#dc2626;font-size:12px;">DDMS sedang dinonaktifkan oleh administrator. Dokumen akan dibuat sebagai dokumen biasa.</small>
                        @endif
                    </div>
                </label>
            </div>

            {{-- Tombol Generate --}}
            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <button type="button" id="btnGenerate"
                    style="background:linear-gradient(135deg,#6366f1,#818cf8);color:#fff;border:none;padding:11px 28px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:opacity .2s;"
                    onclick="doGenerate()">
                    <i class="fas fa-magic"></i> Generate Dokumen
                </button>
                <span id="generateLoading" style="display:none;color:#6366f1;font-size:13px;">
                    <i class="fas fa-spinner fa-spin"></i> Sedang memproses...
                </span>
            </div>
        </form>
    </div>

    {{-- AREA HASIL / AKSI --}}
    <div id="resultPanel" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:28px 32px;">
        <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:18px;">
            <i class="fas fa-check-circle" style="color:#22c55e;margin-right:6px;"></i>
            Dokumen Berhasil Di-generate
        </h2>

        {{-- Tombol aksi --}}
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">

            {{-- Preview baru --}}
            <button type="button" onclick="openPreview()"
                style="background:#fff;border:1.5px solid #6366f1;color:#6366f1;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-external-link-alt"></i> Buka Tab Baru
            </button>

            {{-- Download --}}
            <button type="button" onclick="doDownload()"
                style="background:#0f172a;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-download"></i> Download PDF
            </button>

            {{-- Print --}}
            <button type="button" onclick="doPrint()"
                style="background:#f59e0b;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-print"></i> Print
            </button>

            {{-- Kirim ke Client --}}
            <form id="sendForm" action="{{ route('admin.document_builder.send') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="event_id" id="sendEventId">
                <input type="hidden" name="jenis_dokumen" id="sendJenis">
                <button type="submit" onclick="return confirmSend()"
                    style="background:#22c55e;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-paper-plane"></i> Kirim ke Client
                </button>
            </form>
        </div>
    </div>

</div>

@include('admin.document_builder.partials.latest-documents')

@push('scripts')
<script>
    const PREVIEW_URL  = '{{ route('admin.document_builder.preview-pdf') }}';
    const GENERATE_URL = '{{ route('admin.document_builder.generate') }}';
    const DOWNLOAD_URL = '{{ route('admin.document_builder.download') }}';
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content
                         || '{{ csrf_token() }}';

    // Default DDMS per jenis surat (hanya initial UI state).
    const DDMS_DEFAULTS  = @json($ddmsDefaults);
    const DDMS_ENABLED   = @json($ddmsEnabled);

    // â”€â”€â”€ Info event â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('event_id').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!this.value) {
            document.getElementById('eventInfoCard').style.display = 'none';
            return;
        }
        document.getElementById('infoClient').textContent  = opt.dataset.client  || '-';
        document.getElementById('infoTanggal').textContent = opt.dataset.tanggal || '-';
        document.getElementById('infoLokasi').textContent  = opt.dataset.lokasi  || '-';
        document.getElementById('infoStatus').textContent  = opt.dataset.status  || '-';
        document.getElementById('eventInfoCard').style.display = 'block';
        // Re-fetch total jika jenis dokumen sudah dipilih sebagai invoice
        const docJenis = document.getElementById('jenis_dokumen').value;
        if (docJenis === 'invoice') {
            fetchTotalDibayarKlien();
        }
        // Muat status denah jika jenis = surat_kontrak
        if (docJenis === 'surat_kontrak') {
            loadDenahStatus();
        }
    });

    // â”€â”€â”€ Info jenis dokumen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Sinkronisasi default DDMS per jenis (initial UI state only).
    function syncDdmsDefault(jenis) {
        if (!DDMS_ENABLED) return; // global OFF: checkbox disabled, jangan ubah.
        const chk = document.getElementById('uses_ddms');
        if (!chk || !DDMS_DEFAULTS.hasOwnProperty(jenis)) return;
        // Set initial state dari default; admin tetap dapat mengubah manual.
        chk.checked = !!DDMS_DEFAULTS[jenis];
    }

    const JENIS_DESC = {
        surat_kontrak: {
            icon: '📑', label: 'Surat Kontrak',
            desc: 'Kontrak resmi antara EO dan client mencakup nomor kontrak, nilai kontrak, hak & kewajiban, ketentuan pembayaran, masa berlaku, dan area tanda tangan.',
            color: '#0ea5e9'
        },
        invoice: {
            icon: '🧾', label: 'Invoice',
            desc: 'Invoice pembayaran berisi nomor invoice, daftar item RAB, harga, subtotal, total, dan status pembayaran.',
            color: '#f59e0b'
        },
        rab: {
            icon: '📊', label: 'RAB (Rencana Anggaran Biaya)',
            desc: 'Tabel rincian anggaran event: nama item, vendor, qty, harga satuan, subtotal, dan total keseluruhan.',
            color: '#22c55e'
        },
    };

    document.getElementById('jenis_dokumen').addEventListener('change', function () {
        const card = document.getElementById('jenisInfoCard');
        const schemeSection = document.getElementById('paymentSchemeSection');
        if (!this.value || !JENIS_DESC[this.value]) {
            card.style.display = 'none';
            return;
        }
        // Tampilkan skema pembayaran hanya jika jenis = invoice
        if (this.value === 'invoice') {
            schemeSection.style.display = 'block';
            document.getElementById('has_payment_scheme').value = '1';
            // Ambil total dari server via data attribute atau API
            fetchTotalDibayarKlien();
        } else {
            schemeSection.style.display = 'none';
            document.getElementById('has_payment_scheme').value = '0';
        }
        // Tampilkan upload denah hanya jika jenis = surat_kontrak
        const denahSection = document.getElementById('denahSection');
        if (this.value === 'surat_kontrak') {
            denahSection.style.display = 'block';
            loadDenahStatus();
        } else {
            denahSection.style.display = 'none';
        }

        // Sinkronkan checkbox "Gunakan DDMS" dengan default per jenis.
        // HANYA initial state: admin tetap dapat mengubahnya secara manual.
        // Jika global OFF, checkbox dinonaktifkan (forced Non-DDMS) — tidak diubah di sini.
        syncDdmsDefault(this.value);
        const d = JENIS_DESC[this.value];
        document.getElementById('jenisInfoContent').innerHTML = `
            <div style="background:${d.color}10;border:1px solid ${d.color}30;border-radius:8px;padding:12px 16px;font-size:13px;">
                <strong style="color:${d.color};">${d.icon} ${d.label}</strong>
                <p style="margin:6px 0 0;color:#475569;">${d.desc}</p>
            </div>`;
        card.style.display = 'block';
    });

    // â”€â”€â”€ State untuk tombol aksi â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    let lastEventId = null;
    let lastJenis   = null;

    // --- Denah Upload Functions -------------------------------------------------
    const DENAH_UPLOAD_URL = '{{ route('admin.document_builder.upload_denah') }}';

    function getEventIdForDenah() {
        return document.getElementById('event_id').value;
    }

    function loadDenahStatus() {
        const eventId = getEventIdForDenah();
        if (!eventId) { resetDenahUI(); return; }
        fetch('/admin/document-builder/denah-status/' + eventId)
            .then(r => r.json())
            .then(data => {
                if (data.has_denah && data.url) {
                    document.getElementById('denahPreviewImg').src = data.url;
                    document.getElementById('denahPreview').style.display = 'block';
                    document.getElementById('denahFilePath').value = data.file_path;
                    document.getElementById('btnHapusDenah').style.display = 'inline-flex';
                } else {
                    resetDenahUI();
                }
            })
            .catch(() => resetDenahUI());
    }

    function resetDenahUI() {
        document.getElementById('denahPreview').style.display = 'none';
        document.getElementById('denahFilePath').value = '';
        document.getElementById('btnHapusDenah').style.display = 'none';
        document.getElementById('denahUploadStatus').style.display = 'none';
    }

    function uploadDenah() {
        const fileInput = document.getElementById('denahFileInput');
        const eventId = getEventIdForDenah();
        if (!eventId) { alert('Pilih event terlebih dahulu.'); return; }
        if (!fileInput.files.length) { alert('Pilih file denah/layout terlebih dahulu.'); return; }

        const formData = new FormData();
        formData.append('event_id', eventId);
        formData.append('layout_denah', fileInput.files[0]);

        const btn = document.getElementById('btnUploadDenah');
        const status = document.getElementById('denahUploadStatus');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        status.style.display = 'block';
        status.style.color = '#6366f1';
        status.innerHTML = 'Mengupload...';

        fetch(DENAH_UPLOAD_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData,
        })
        .then(r => {
            console.log('[DEBUG] Response status:', r.status);
            console.log('[DEBUG] Content-Type:', r.headers.get('content-type'));
            const ct = r.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                return r.json().then(data => {
                    console.log('[DEBUG] JSON response:', data);
                    if (data.success) {
                        status.style.color = '#16a34a';
                        status.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        document.getElementById('denahPreviewImg').src = data.url;
                        document.getElementById('denahPreview').style.display = 'block';
                        document.getElementById('denahFilePath').value = data.file_path;
                        document.getElementById('btnHapusDenah').style.display = 'inline-flex';
                        fileInput.value = '';
                    } else {
                        status.style.color = '#dc2626';
                        status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal upload.';
                    }
                });
            } else {
                return r.text().then(text => {
                    console.log('[DEBUG] HTML response (first 1000 chars):', text.substring(0, 1000));
                    status.style.color = '#dc2626';
                    status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Server error (status: ' + r.status + '). Cek console.';
                });
            }
        })
        .catch(err => {
            console.error('[DEBUG] Fetch error:', err);
            status.style.color = '#dc2626';
            status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + err.message;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-upload"></i> Upload';
        });
    }

    function hapusDenah() {
        const eventId = getEventIdForDenah();
        if (!eventId || !confirm('Hapus denah/layout yang sudah diupload?')) return;

        fetch('/admin/document-builder/hapus-denah/' + eventId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            const status = document.getElementById('denahUploadStatus');
            status.style.display = 'block';
            if (data.success) {
                status.style.color = '#16a34a';
                status.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                resetDenahUI();
            } else {
                status.style.color = '#dc2626';
                status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal hapus.';
            }
        })
        .catch(() => {
            const status = document.getElementById('denahUploadStatus');
            status.style.display = 'block';
            status.style.color = '#dc2626';
            status.innerHTML = '<i class="fas fa-exclamation-circle"></i> Terjadi kesalahan.';
        });
    }

    // â”€â”€â”€ Generate â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    async function doGenerate() {
        const eventId = document.getElementById('event_id').value;
        const jenis   = document.getElementById('jenis_dokumen').value;

        if (!eventId || !jenis) {
            alert('Harap pilih Event dan Jenis Dokumen terlebih dahulu.');
            return;
        }

        lastEventId = eventId;
        lastJenis   = jenis;

        // Loading state
        document.getElementById('btnGenerate').disabled = true;
        document.getElementById('generateLoading').style.display = 'inline-flex';
        document.getElementById('resultPanel').style.display = 'none';

        // POST ke generate endpoint, redirect ke halaman preview
        const form = document.createElement('form');
            form.method = 'POST';
            form.action = GENERATE_URL;

            const ddmsCheck = document.getElementById('uses_ddms');
            const usesDdms = ddmsCheck && !ddmsCheck.disabled && ddmsCheck.checked ? 1 : 0;

            [['_token', CSRF], ['event_id', eventId], ['jenis_dokumen', jenis], ['uses_ddms', usesDdms]].forEach(([k, v]) => {
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = k; i.value = v;
                form.appendChild(i);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Isi hidden field kirim
            document.getElementById('sendEventId').value = eventId;
            document.getElementById('sendJenis').value   = jenis;

            // Tampilkan result panel setelah delay singkat
            setTimeout(() => {
                document.getElementById('resultPanel').style.display = 'block';
                document.getElementById('resultPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 800);
    }

    function openPreview() {
        if (!lastEventId || !lastJenis) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = PREVIEW_URL;
        form.target = '_blank';

        [['_token', CSRF], ['event_id', lastEventId], ['jenis_dokumen', lastJenis]].forEach(([k, v]) => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = k; i.value = v;
            form.appendChild(i);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function doDownload() {
        if (!lastEventId || !lastJenis) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = DOWNLOAD_URL;

        [['_token', CSRF], ['event_id', lastEventId], ['jenis_dokumen', lastJenis]].forEach(([k, v]) => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = k; i.value = v;
            form.appendChild(i);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function doPrint() {
        const frame = document.getElementById('pdfPreviewFrame');
        try {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        } catch (e) {
            // Fallback: buka tab baru lalu print
            openPreview();
        }
    }

    function confirmSend() {
        return swalSend(document.getElementById('sendForm'), 'Kirim Dokumen?', 'Dokumen akan disimpan ke storage, dicatat di database, dan client akan menerima notifikasi.');
    }

    // Auto-trigger jika ada query params dari redirect
    @if($selectedEventId && $selectedJenis)
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('event_id').dispatchEvent(new Event('change'));
            document.getElementById('jenis_dokumen').dispatchEvent(new Event('change'));
        });
@endif

    // Payment Scheme Functions (khusus Invoice)
    let docTotalDibayarKlien = 0;

    async function fetchTotalDibayarKlien() {
        const eventId = document.getElementById('event_id').value;
        if (!eventId) return;
        try {
            const resp = await fetch('/admin/rab/total-dibayar-klien/' + eventId);
            const data = await resp.json();
            docTotalDibayarKlien = data.total || 0;
            document.getElementById('doc_preview_total').innerText = 'Rp ' + docTotalDibayarKlien.toLocaleString('id-ID');
            hitungDocSkema();
            toggleDocPaymentScheme();
        } catch(e) {
            console.error('Gagal memuat total:', e);
        }
    }

    function toggleDocPaymentScheme() {
        const jenis = document.getElementById('doc_jenis_pembayaran').value;
        const dpModeGroup = document.getElementById('doc_dp_mode_group');
        const preview = document.getElementById('doc_preview');
        if (jenis === 'dp_dan_pelunasan') {
            dpModeGroup.style.display = 'block';
            preview.style.display = 'block';
            toggleDocDpMode();
        } else {
            dpModeGroup.style.display = 'none';
            document.getElementById('doc_dp_persentase_group').style.display = 'none';
            document.getElementById('doc_dp_nominal_group').style.display = 'none';
            preview.style.display = 'none';
        }
        hitungDocSkema();
    }

    function toggleDocDpMode() {
        const mode = document.getElementById('doc_mode_dp').value;
        document.getElementById('doc_dp_persentase_group').style.display = mode === 'persentase' ? 'block' : 'none';
        document.getElementById('doc_dp_nominal_group').style.display = mode === 'nominal' ? 'block' : 'none';
        hitungDocSkema();
    }

    function hitungDocSkema() {
        if (document.getElementById('doc_jenis_pembayaran').value !== 'dp_dan_pelunasan') return;
        const mode = document.getElementById('doc_mode_dp').value;
        let dpNominal = 0;
        if (mode === 'persentase') {
            const pct = parseFloat(document.getElementById('doc_persentase_dp').value) || 0;
            dpNominal = docTotalDibayarKlien * pct / 100;
        } else {
            dpNominal = parseFloat(document.getElementById('doc_nilai_dp').value) || 0;
        }
        const sisa = Math.max(0, docTotalDibayarKlien - dpNominal);
        document.getElementById('doc_preview_dp').innerText = 'Rp ' + Math.round(dpNominal).toLocaleString('id-ID');
        document.getElementById('doc_preview_sisa').innerText = 'Rp ' + Math.round(sisa).toLocaleString('id-ID');
    }

    function appendSchemeFields(form) {
        if (document.getElementById('has_payment_scheme').value !== '1') return;
        function add(name, value) {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = name; i.value = value;
            form.appendChild(i);
        }
        add('jenis_pembayaran', document.getElementById('doc_jenis_pembayaran').value);
        if (document.getElementById('doc_jenis_pembayaran').value === 'dp_dan_pelunasan') {
            add('mode_dp', document.getElementById('doc_mode_dp').value);
            if (document.getElementById('doc_mode_dp').value === 'persentase') {
                add('persentase_dp', document.getElementById('doc_persentase_dp').value);
            } else {
                add('nilai_dp', document.getElementById('doc_nilai_dp').value);
            }
        }
    }

    const _origDoGenerate = doGenerate;
    doGenerate = function() {
        if (document.getElementById('jenis_dokumen').value === 'invoice') {
            const eventId = document.getElementById('event_id').value;
            if (!eventId) { alert('Harap pilih Event terlebih dahulu.'); return; }
            lastEventId = eventId; lastJenis = 'invoice';
            const form = document.createElement('form');
            form.method = 'POST'; form.action = PREVIEW_URL; form.target = 'pdfPreviewFrame';
            [['_token', CSRF], ['event_id', eventId], ['jenis_dokumen', 'invoice']].forEach(function(p) {
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = p[0]; i.value = p[1];
                form.appendChild(i);
            });
            appendSchemeFields(form);
            document.body.appendChild(form); form.submit(); document.body.removeChild(form);
            document.getElementById('sendEventId').value = eventId;
            document.getElementById('sendJenis').value = 'invoice';
            document.getElementById('resultPanel').style.display = 'block';
            document.getElementById('resultPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }
        return _origDoGenerate();
    };

    const _origOpenPreview = openPreview;
    openPreview = function() {
        if (lastJenis === 'invoice') {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = PREVIEW_URL; form.target = '_blank';
            [['_token', CSRF], ['event_id', lastEventId], ['jenis_dokumen', 'invoice']].forEach(function(p) {
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = p[0]; i.value = p[1];
                form.appendChild(i);
            });
            appendSchemeFields(form);
            document.body.appendChild(form); form.submit(); document.body.removeChild(form);
            return;
        }
        return _origOpenPreview();
    };

    const _origDoDownload = doDownload;
    doDownload = function() {
        var jenis = document.getElementById('jenis_dokumen').value || lastJenis;
        if (jenis === 'invoice') {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = DOWNLOAD_URL;
            [['_token', CSRF], ['event_id', lastEventId], ['jenis_dokumen', jenis]].forEach(function(p) {
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = p[0]; i.value = p[1];
                form.appendChild(i);
            });
            appendSchemeFields(form);
            document.body.appendChild(form); form.submit(); document.body.removeChild(form);
            return;
        }
        return _origDoDownload();
    };

    document.getElementById('sendForm').addEventListener('submit', function(e) {
        if (document.getElementById('sendJenis').value === 'invoice' && document.getElementById('has_payment_scheme').value === '1') {
            appendSchemeFields(this);
        }
    });
</script>

@endpush
@endsection


