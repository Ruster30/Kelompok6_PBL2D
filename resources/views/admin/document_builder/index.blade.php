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
    <a href="{{ route('admin.proposals.index') }}" class="tab-link">Dokumen Umum</a>
    <a href="{{ route('admin.document_builder.index') }}" class="tab-link active">Document Builder</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="tab-content">

    {{-- ─── FORM GENERATE ─────────────────────────────────────── --}}
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
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;font-size:12px;">
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

    {{-- ─── AREA HASIL / AKSI ─────────────────────────────────── --}}
    <div id="resultPanel" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:28px 32px;">
        <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:18px;">
            <i class="fas fa-check-circle" style="color:#22c55e;margin-right:6px;"></i>
            Dokumen Berhasil Di-generate
        </h2>

        {{-- Preview iframe --}}
        <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:20px;background:#f1f5f9;">
            <div style="background:#e2e8f0;padding:8px 14px;font-size:12px;color:#475569;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-eye"></i> Preview Dokumen
            </div>
            <iframe id="pdfPreviewFrame"
                style="width:100%;height:600px;border:none;display:block;"
                src="about:blank">
            </iframe>
        </div>

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

@push('scripts')
<script>
    const PREVIEW_URL  = '{{ route('admin.document_builder.preview') }}';
    const DOWNLOAD_URL = '{{ route('admin.document_builder.download') }}';
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content
                         || '{{ csrf_token() }}';

    // ─── Info event ─────────────────────────────────────────────
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
    });

    // ─── Info jenis dokumen ─────────────────────────────────────
    const JENIS_DESC = {
        proposal: {
            icon: '📄', label: 'Proposal Event',
            desc: 'Dokumen proposal lengkap meliputi profil perusahaan, data client & event, konsep, layanan, timeline, vendor, RAB, dan syarat & ketentuan.',
            color: '#6366f1'
        },
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
        if (!this.value || !JENIS_DESC[this.value]) {
            card.style.display = 'none';
            return;
        }
        const d = JENIS_DESC[this.value];
        document.getElementById('jenisInfoContent').innerHTML = `
            <div style="background:${d.color}10;border:1px solid ${d.color}30;border-radius:8px;padding:12px 16px;font-size:13px;">
                <strong style="color:${d.color};">${d.icon} ${d.label}</strong>
                <p style="margin:6px 0 0;color:#475569;">${d.desc}</p>
            </div>`;
        card.style.display = 'block';
    });

    // ─── State untuk tombol aksi ────────────────────────────────
    let lastEventId = null;
    let lastJenis   = null;

    // ─── Generate ───────────────────────────────────────────────
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

        try {
            // POST ke preview endpoint, tampilkan di iframe
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = PREVIEW_URL;
            form.target = 'pdfPreviewFrame';

            [['_token', CSRF], ['event_id', eventId], ['jenis_dokumen', jenis]].forEach(([k, v]) => {
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

        } finally {
            document.getElementById('btnGenerate').disabled = false;
            document.getElementById('generateLoading').style.display = 'none';
        }
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
        return confirm('Kirim dokumen ini ke client?\n\nDokumen akan disimpan ke storage, dicatat di database, dan client akan menerima notifikasi (serta email jika dikonfigurasi).');
    }

    // Auto-trigger jika ada query params dari redirect
    @if($selectedEventId && $selectedJenis)
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('event_id').dispatchEvent(new Event('change'));
            document.getElementById('jenis_dokumen').dispatchEvent(new Event('change'));
        });
    @endif
</script>
@endpush
@endsection
