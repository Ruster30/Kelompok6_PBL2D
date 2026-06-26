<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.6; }

  /* ─── Cover ─── */
  .cover { width: 100%; min-height: 270mm; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(160deg, #4338ca, #6366f1); color: #fff; padding: 60px 40px; text-align: center; page-break-after: always; }
  .cover .logo-wrap { width: 80px; height: 80px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 28px; font-weight: 900; color: #4338ca; }
  .cover h1 { font-size: 32px; font-weight: 900; margin-bottom: 10px; letter-spacing: -0.5px; }
  .cover .subtitle { font-size: 16px; opacity: .85; margin-bottom: 36px; }
  .cover .badge { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.4); border-radius: 999px; padding: 6px 20px; font-size: 13px; display: inline-block; margin-bottom: 8px; }
  .cover .meta { font-size: 12px; opacity: .7; margin-top: 40px; }

  /* ─── Umum ─── */
  .section { padding: 24px 32px; margin-bottom: 8px; }
  .section-title { font-size: 15px; font-weight: 700; color: #4338ca; border-bottom: 2px solid #c7d2fe; padding-bottom: 6px; margin-bottom: 14px; }
  .section-title i { margin-right: 6px; }

  /* ─── Info Grid ─── */
  .info-grid { display: table; width: 100%; }
  .info-row { display: table-row; }
  .info-label { display: table-cell; width: 38%; color: #64748b; padding: 3px 0; vertical-align: top; }
  .info-value { display: table-cell; font-weight: 600; padding: 3px 0; }

  /* ─── Tabel ─── */
  table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10.5px; }
  thead th { background: #4338ca; color: #fff; padding: 8px 10px; text-align: left; }
  tbody tr:nth-child(even) { background: #f8faff; }
  tbody td { border-bottom: 1px solid #e2e8f0; padding: 7px 10px; }
  .text-right { text-align: right; }
  .total-row td { background: #eef2ff; font-weight: 700; }

  /* ─── Badge ─── */
  .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 10px; font-weight: 600; }
  .badge-info { background: #dbeafe; color: #1d4ed8; }

  /* ─── Footer ─── */
  .footer { margin-top: 20px; font-size: 9.5px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }

  /* ─── Syarat ─── */
  .syarat ol { padding-left: 20px; }
  .syarat li { margin-bottom: 6px; }

  /* ─── Page break ─── */
  .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- ══════════ COVER ══════════ --}}
<div class="cover">
    <div class="logo-wrap">AMO</div>
    <h1>PROPOSAL EVENT</h1>
    <div class="subtitle">CV. Alpha Multi Organizer</div>
    <div class="badge">{{ $event->jenis_event ?? 'Event Organizer' }}</div>
    <div style="font-size:22px; font-weight:800; margin-top:24px;">{{ $event->nama_event }}</div>
    <div style="font-size:13px; opacity:.75; margin-top:8px;">
        {{ $event->tanggal_event?->format('d F Y') }} &nbsp;|&nbsp; {{ $event->lokasi_event ?? '-' }}
    </div>
    <div class="meta">Disiapkan untuk: <strong>{{ $event->client->name ?? '-' }}</strong> &nbsp;|&nbsp; {{ now()->format('d M Y') }}</div>
</div>

{{-- ══════════ PROFIL PERUSAHAAN ══════════ --}}
<div class="section page-break">
    <div class="section-title">Profil Perusahaan</div>
    <p style="margin-bottom:12px;">
        <strong>CV. Alpha Multi Organizer</strong> adalah perusahaan yang bergerak di bidang <em>event organizer</em>
        profesional dengan pengalaman mengelola berbagai jenis acara, mulai dari pernikahan, seminar, konser, hingga
        acara korporat. Kami berkomitmen menghadirkan pengalaman acara yang berkesan dengan manajemen yang terstruktur
        dan tim yang berpengalaman.
    </p>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Nama Perusahaan</div>
            <div class="info-value">CV. Alpha Multi Organizer</div>
        </div>
        <div class="info-row">
            <div class="info-label">Bidang Usaha</div>
            <div class="info-value">Event Organizer &amp; Entertainment</div>
        </div>
        <div class="info-row">
            <div class="info-label">Alamat</div>
            <div class="info-value">Padang, Sumatera Barat</div>
        </div>
    </div>
</div>

{{-- ══════════ DATA CLIENT ══════════ --}}
<div class="section">
    <div class="section-title">Data Client</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Nama Client</div>
            <div class="info-value">{{ $event->client->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $event->client->email ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Telepon</div>
            <div class="info-value">{{ $event->client->phone ?? '-' }}</div>
        </div>
    </div>
</div>

{{-- ══════════ DATA EVENT ══════════ --}}
<div class="section">
    <div class="section-title">Data Event</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Nama Event</div>
            <div class="info-value">{{ $event->nama_event }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jenis Event</div>
            <div class="info-value">{{ $event->jenis_event ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Pelaksanaan</div>
            <div class="info-value">{{ $event->tanggal_event?->format('d F Y') ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lokasi</div>
            <div class="info-value">{{ $event->lokasi_event ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jumlah Tamu</div>
            <div class="info-value">{{ $event->jumlah_tamu ? number_format($event->jumlah_tamu) . ' orang' : '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Estimasi Anggaran</div>
            <div class="info-value">{{ $event->rentang_anggaran ?? '-' }}</div>
        </div>
    </div>
</div>

{{-- ══════════ KONSEP EVENT ══════════ --}}
<div class="section">
    <div class="section-title">Konsep Event</div>
    <p>{{ $event->detail_kebutuhan ?: 'Detail konsep event belum diisi.' }}</p>
</div>

{{-- ══════════ LAYANAN ══════════ --}}
@if($services->count())
<div class="section page-break">
    <div class="section-title">Layanan Kami</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->nama_layanan }}</td>
                <td>{{ $s->deskripsi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ══════════ TIMELINE ══════════ --}}
@if($timelines->count())
<div class="section">
    <div class="section-title">Timeline Kegiatan</div>
    <table>
        <thead>
            <tr>
                <th>Kegiatan</th>
                <th>Tanggal</th>
                <th>Penanggung Jawab</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timelines as $t)
            <tr>
                <td>{{ $t->nama_kegiatan }}</td>
                <td>{{ $t->tanggal_kegiatan?->format('d M Y') ?? '-' }}</td>
                <td>{{ $t->penanggung_jawab ?? '-' }}</td>
                <td>{{ $t->status_label ?? $t->status_kegiatan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ══════════ VENDOR ══════════ --}}
@if($vendors->count())
<div class="section page-break">
    <div class="section-title">Daftar Vendor</div>
    <table>
        <thead>
            <tr>
                <th>Nama Vendor</th>
                <th>Jenis</th>
                <th>Jadwal</th>
                <th>Harga Vendor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $v)
            <tr>
                <td>{{ $v->nama_vendor }}</td>
                <td>{{ $v->jenis_vendor ?? '-' }}</td>
                <td>{{ $v->pivot->jadwal_vendor ? \Carbon\Carbon::parse($v->pivot->jadwal_vendor)->format('d M Y') : '-' }}</td>
                <td class="text-right">{{ $v->pivot->harga_vendor ? 'Rp ' . number_format($v->pivot->harga_vendor, 0, ',', '.') : '-' }}</td>
                <td>{{ $v->pivot->status_vendor ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ══════════ RAB ══════════ --}}
@if($rabItems->count())
<div class="section">
    <div class="section-title">Rencana Anggaran Biaya (RAB)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Vendor</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rabItems as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->nama_biaya }}</td>
                <td>{{ $r->kategori_biaya ?? '-' }}</td>
                <td>{{ $r->vendor->nama_vendor ?? '-' }}</td>
                <td class="text-right">{{ $r->jumlah_item }}</td>
                <td class="text-right">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($r->subtotal_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalRab, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- ══════════ SYARAT & KETENTUAN ══════════ --}}
<div class="section page-break syarat">
    <div class="section-title">Syarat &amp; Ketentuan</div>
    <ol>
        <li>Proposal ini berlaku selama <strong>14 (empat belas) hari kalender</strong> sejak tanggal diterbitkan.</li>
        <li>Pembayaran uang muka (DP) sebesar <strong>50%</strong> dari total anggaran dilakukan setelah penandatanganan kontrak.</li>
        <li>Pelunasan dilakukan <strong>7 hari sebelum</strong> pelaksanaan event.</li>
        <li>Perubahan pada konsep atau anggaran event harus diajukan secara tertulis dan disetujui kedua belah pihak.</li>
        <li>Pembatalan oleh client yang dilakukan kurang dari 14 hari sebelum event dikenakan biaya pembatalan sebesar <strong>30%</strong> dari total anggaran.</li>
        <li>CV. Alpha Multi Organizer berhak menolak permintaan tambahan yang tidak tercantum dalam proposal ini tanpa pemberitahuan sebelumnya.</li>
        <li>Force Majeure (bencana alam, kerusuhan, dll.) membebaskan kedua belah pihak dari kewajiban kontrak.</li>
        <li>Sengketa diselesaikan secara musyawarah mufakat; jika tidak tercapai, diserahkan ke pengadilan yang berwenang.</li>
    </ol>

    <div style="margin-top:40px;display:table;width:100%;">
        <div style="display:table-cell;width:50%;text-align:center;vertical-align:bottom;">
            <div>Padang, {{ now()->format('d F Y') }}</div>
            <div style="margin-top:4px;color:#64748b;">CV. Alpha Multi Organizer</div>
            <div style="margin-top:60px;border-top:1px solid #1e293b;display:inline-block;padding-top:4px;min-width:140px;">
                Direktur
            </div>
        </div>
        <div style="display:table-cell;width:50%;text-align:center;vertical-align:bottom;">
            <div>Padang, {{ now()->format('d F Y') }}</div>
            <div style="margin-top:4px;color:#64748b;">Client</div>
            <div style="margin-top:60px;border-top:1px solid #1e293b;display:inline-block;padding-top:4px;min-width:140px;">
                {{ $event->client->name ?? '_________________' }}
            </div>
        </div>
    </div>
</div>

<div class="footer">
    Dokumen ini digenerate otomatis oleh sistem Alpha Organizer pada {{ now()->format('d M Y, H:i') }} WIB.
    Dokumen ini sah tanpa tanda tangan basah jika telah disetujui secara digital.
</div>

</body>
</html>
