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
    <div style="display:flex; gap:10px;">
        {{-- Export PDF --}}
        <a href="{{ route('admin.requests.export-pdf', $event->id) }}"
           class="btn btn-outline" target="_blank">
            <i class="fas fa-download"></i> Export PDF
        </a>
        {{-- Print --}}
        <button class="btn btn-outline" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        {{-- Kirim ke Client --}}
        @if(!$event->latestProposal)
        <form action="{{ route('admin.requests.kirim-penawaran', $event->id) }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="nomor_surat" value="{{ $nomorSurat }}">
            <input type="hidden" name="tanggal_surat" value="{{ now()->format('Y-m-d') }}">
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Kirim surat penawaran ini ke client {{ $event->client->name ?? '' }}?')">
                <i class="fas fa-paper-plane"></i> Kirim Penawaran
            </button>
        </form>
        @else
        {{-- Sudah ada penawaran, tawarkan revisi --}}
        <form action="{{ route('admin.requests.revisi-penawaran', $event->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Kirim revisi penawaran ke client?')">
                <i class="fas fa-sync-alt"></i> Revisi Penawaran
            </button>
        </form>
        @endif
    </div>
</div>

{{-- Preview Surat --}}
<div style="background:#f1f5f9; padding:24px; border-radius:12px;">
<div id="surat-preview" style="background:white; max-width:820px; margin:0 auto; padding:48px 56px; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,0.08); font-family:'Times New Roman',serif; font-size:13px; line-height:1.7; color:#111;">

    {{-- Kop Surat --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #14b8a6; padding-bottom:16px; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:56px; height:56px; background:#14b8a6; border-radius:8px; display:flex; align-items:center; justify-content:center; color:white; font-weight:900; font-size:18px; font-family:sans-serif;">
                α
            </div>
            <div>
                <div style="font-size:18px; font-weight:900; color:#0f172a; font-family:sans-serif; letter-spacing:0.3px;">CV. ALPHA MULTI ORGANIZER</div>
                <div style="font-size:12px; color:#64748b; font-family:sans-serif;">Event Management &amp; Production</div>
            </div>
        </div>
        <div style="text-align:right; font-size:11.5px; color:#475569; line-height:1.6; font-family:sans-serif;">
            Jl. Kenangan Air Dingin No.25,<br>Padang<br>
            Telp: +62 812-3456-7890<br>
            Email: hello@alphacorp.events
        </div>
    </div>

    {{-- Info Surat --}}
    <table style="width:100%; border:none; font-size:13px; margin-bottom:18px;">
        <tr>
            <td style="width:28%; padding:3px 0; border:none;">No. Surat</td>
            <td style="width:2%; border:none;">:</td>
            <td style="border-bottom:1px solid #ccc; padding:3px 8px; border-top:none; border-left:none; border-right:none; width:35%;">{{ $nomorSurat }}</td>
            <td style="border:none; padding-left:20px;">Padang, <span style="border-bottom:1px solid #ccc; padding:0 60px;">{{ now()->translatedFormat('d F Y') }}</span></td>
        </tr>
        <tr>
            <td style="padding:3px 0; border:none;">Lampiran</td>
            <td style="border:none;">:</td>
            <td style="border:none; padding:3px 8px;">-</td>
            <td style="border:none;"></td>
        </tr>
        <tr>
            <td style="padding:3px 0; border:none;">Perihal</td>
            <td style="border:none;">:</td>
            <td style="border:none; padding:3px 8px;"><strong>Surat Penawaran Event</strong></td>
            <td style="border:none;"></td>
        </tr>
    </table>

    {{-- Kepada --}}
    <div style="margin-bottom:18px;">
        <div>Kepada Yth.</div>
        <div><strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong></div>
        <div>Di,</div>
        <div style="margin-left:20px;">Tempat</div>
    </div>

    {{-- Pembuka --}}
    <p>Dengan hormat,</p>
    <p style="text-align:justify;">
        Kami dari CV. Alpha Multi Organizer, perusahaan yang bergerak di bidang Event Organizer.
        Berdasarkan permintaan yang telah diajukan oleh pihak Bapak/Ibu, kami menawarkan kegiatan
        event dengan detail sebagai berikut:
    </p>

    {{-- Detail Event --}}
    <table style="width:90%; border:1px solid #e2e8f0; border-collapse:collapse; margin:16px auto; font-size:13px;">
        <tr>
            <td style="padding:8px 14px; border:1px solid #e2e8f0; width:35%;"><strong>I. Nama Event</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">: {{ $event->nama_event }}</td>
        </tr>
        <tr style="background:#f8fafc;">
            <td style="padding:8px 14px; border:1px solid #e2e8f0;"><strong>II. Jenis Kegiatan</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">: {{ $event->jenis_event ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;"><strong>III. Lokasi</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">: {{ $event->lokasi_event ?? '-' }}</td>
        </tr>
        <tr style="background:#f8fafc;">
            <td style="padding:8px 14px; border:1px solid #e2e8f0;"><strong>IV. Jadwal</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">
                : {{ $event->tanggal_event?->format('Y-m-d') ?? '-' }}
                @if($event->tanggal_selesai ?? null) s/d {{ $event->tanggal_selesai->format('Y-m-d') }} @endif
            </td>
        </tr>
        <tr>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;"><strong>V. Estimasi Tamu</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">: {{ $event->jumlah_tamu ? $event->jumlah_tamu . ' Orang' : '-' }}</td>
        </tr>
        <tr style="background:#f8fafc;">
            <td style="padding:8px 14px; border:1px solid #e2e8f0;"><strong>VI. Estimasi Anggaran</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0; font-weight:700; color:#0f766e;">: {{ $event->rentang_anggaran ?? '-' }}</td>
        </tr>
        @if($event->detail_kebutuhan)
        <tr>
            <td style="padding:8px 14px; border:1px solid #e2e8f0; vertical-align:top;"><strong>VII. Deskripsi Kebutuhan</strong></td>
            <td style="padding:8px 14px; border:1px solid #e2e8f0;">: {{ $event->detail_kebutuhan }}</td>
        </tr>
        @endif
    </table>

    {{-- Fasilitas Standar --}}
    <p><strong>VIII. Fasilitas Standar:</strong></p>
    <ul style="margin:6px 0 14px 24px; padding:0;">
        <li>Manajemen acara penuh (Full Event Management)</li>
        <li>Koordinasi vendor dan logistik</li>
        <li>Setup dan dekorasi standar sesuai tema</li>
        <li>Tim lapangan profesional selama acara berlangsung</li>
    </ul>

    {{-- Ketentuan --}}
    <p><strong>IX. Ketentuan:</strong></p>
    <ul style="margin:6px 0 14px 24px; padding:0;">
        <li>Pembayaran dilakukan melalui transfer bank ke rekening CV. Alpha Multi Organizer.</li>
        <li>Ketentuan event mengikuti aturan standar dari Alpha Organizer.</li>
        <li>Biaya di atas merupakan estimasi awal dan dapat disesuaikan kembali setelah diskusi lebih lanjut.</li>
        <li>Biaya belum termasuk pajak (jika ada).</li>
    </ul>

    {{-- Penutup --}}
    <p style="text-align:justify;">
        Apabila ada hal yang ingin ditanyakan atau didiskusikan lebih lanjut mengenai penawaran ini,
        silakan menghubungi kami melalui telepon atau email yang tertera di atas.
    </p>
    <p>Demikian surat penawaran ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>

    {{-- Tanda Tangan --}}
    <div style="text-align:right; margin-top:40px;">
        <div>Hormat kami,</div>
        <div style="margin-top:56px;">
            <strong>{{ auth()->user()->name }}</strong><br>
            <span style="font-size:12px; color:#64748b;">Direktur</span>
        </div>
    </div>

</div>{{-- end surat-preview --}}
</div>

@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .page-header, [style*="display:flex; align-items:center; justify-content:space-between"] {
        display: none !important;
    }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    #surat-preview {
        box-shadow: none !important;
        max-width: 100% !important;
        padding: 20px !important;
    }
}
</style>
@endpush
