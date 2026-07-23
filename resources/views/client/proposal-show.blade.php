@extends('layouts.client')
@section('title','Surat Penawaran')
@section('page-title','Surat Penawaran')

@section('content')

{{-- Flash --}}
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Info revisi --}}
@if($proposal->versi > 1)
<div style="background:#eff6ff;border:1px solid #93c5fd;color:#1e40af;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px;">
    <i class="bi bi-info-circle-fill"></i>
    Ini adalah <strong>Revisi v{{ $proposal->versi }}</strong> penawaran terbaru yang telah diperbarui oleh tim kami.
</div>
@endif

{{-- Action Bar --}}
<div class="page-header" style="margin-bottom:24px;">
    <a href="{{ route('client.proposals') }}"
       class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke Penawaran
    </a>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        {{-- Badge status --}}
        <span class="badge {{ $proposal->badge_class }}" style="font-size:12px;padding:6px 14px;">
            {{ $proposal->status_label }}
        </span>

        {{-- Unduh PDF --}}
        <a href="{{ route('client.proposals.export-pdf', $proposal->id) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  border:1.5px solid var(--border);border-radius:8px;font-size:13px;
                  font-weight:600;color:var(--dark);text-decoration:none;background:white;">
            <i class="bi bi-download"></i> Unduh PDF
        </a>

        {{-- Tombol aksi respon â€” hanya jika status menunggu konfirmasi --}}
        @if(!in_array($proposal->status, ['diterima','ditolak']))

        <a href="{{ route('client.proposals.negosiasi.form', $proposal->id) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                  border:1.5px solid var(--accent);color:var(--accent);border-radius:8px;
                  font-size:13px;font-weight:600;text-decoration:none;background:white;">
            <i class="bi bi-chat-dots-fill"></i> Ajukan Negosiasi
        </a>

        <form action="{{ route('client.proposals.terima', $proposal->id) }}"
            method="POST"
            style="margin:0;">

            @csrf

            <button
                type="submit"
                onclick="return swalApprove(this.form, 'Terima Penawaran?', 'Timeline event akan otomatis dibuat setelah Anda menerima penawaran ini.')"

                style="
                display:inline-flex;
                align-items:center;
                gap:6px;
                padding:9px 18px;
                background:#16a34a;
                color:white;
                border:none;
                border-radius:8px;
                font-size:13px;
                font-weight:600;
                cursor:pointer;">

                <i class="bi bi-check-circle-fill"></i>

                {{ $proposal->status == 'direvisi'
                    ? 'Terima Penawaran Revisi'
                    : 'Terima Penawaran'
                }}

            </button>
        </form>
        @elseif($proposal->status === 'diterima')
        <button
            type="button"
            disabled
            title="Sudah Diterima"
            style="
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:9px 18px;
            background:#e2e8f0;
            color:#64748b;
            border:none;
            border-radius:8px;
            font-size:13px;
            font-weight:600;
            cursor:not-allowed;">
 
            <i class="bi bi-check-circle-fill"></i> Sudah Diterima
 
        </button>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SURAT PENAWARAN â€” format identik dengan tampilan admin
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div style="background:#f1f5f9;padding:24px;border-radius:12px;margin-bottom:24px;">
<div style="background:white;max-width:820px;margin:0 auto;padding:0;border-radius:8px;
            box-shadow:0 2px 12px rgba(0,0,0,0.08);font-family:'Times New Roman',serif;
            font-size:13px;line-height:1.7;color:#111;overflow:hidden;">

    {{-- KOP SURAT --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:20px 40px 14px;">
        <div>
            <div style="font-size:30px;font-weight:900;color:#1a6fa8;letter-spacing:3px;
                        font-family:'Arial Black',Arial,sans-serif;line-height:1;">ALPHA</div>
            <div style="font-size:10px;letter-spacing:5px;color:#1a6fa8;font-family:Arial,sans-serif;
                        font-weight:700;margin-top:2px;">ORGANIZER</div>
        </div>
        <div style="text-align:right;font-size:11.5px;font-family:Arial,sans-serif;color:#333;line-height:1.9;">
            <div>+62 822-3318-1883</div>
            <div>alphaorganizer1209@gmail.com</div>
            <div>Jl.Air Dingin No.25 Kec.Koto Tangah, Kota Padang</div>
        </div>
    </div>
    <div style="height:3px;background:#1a6fa8;margin:0;"></div>

    {{-- BADAN SURAT --}}
    <div style="padding:24px 40px 40px;">

        {{-- Tanggal --}}
        <div style="text-align:right;margin-bottom:16px;">
            Padang, {{ $proposal->tanggal_proposal
                ? \Carbon\Carbon::parse($proposal->tanggal_proposal)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y') }}
        </div>

        {{-- Meta surat --}}
        <table style="border:none;border-collapse:collapse;margin-bottom:16px;font-size:13px;">
            <tr>
                <td style="border:none;padding:1px 4px;min-width:90px;">No. Surat</td>
                <td style="border:none;padding:1px 8px 1px 4px;">:</td>
                <td style="border:none;padding:1px 4px;">
                    {{ $event->nomor_surat_override ?? $proposal->nomor_proposal ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="border:none;padding:1px 4px;">Lampiran</td>
                <td style="border:none;padding:1px 8px 1px 4px;">:</td>
                <td style="border:none;padding:1px 4px;">-</td>
            </tr>
            <tr>
                <td style="border:none;padding:1px 4px;">Perihal</td>
                <td style="border:none;padding:1px 8px 1px 4px;">:</td>
                <td style="border:none;padding:1px 4px;">{{ $event->perihal ?? '-' }}</td>
            </tr>
        </table>

        {{-- Kepada --}}
        <div style="margin-bottom:8px;">
            Kepada Yth<br>
            <strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong>
        </div>
        <div style="margin-bottom:16px;">
            Di,<br>
            <span style="padding-left:20px;">Tempat</span>
        </div>

        <hr style="border:none;border-top:0.5px solid #bbb;margin:10px 0 14px;">

        {{-- Pembuka --}}
        <p style="text-align:justify;margin-bottom:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dengan hormat, kami dari <strong>CV. Alpha Multi Organizer</strong>
            Perusahaan yang bergerak di bidang Event Organizer. Dengan ini menawarkan kegiatan <strong>{{ $event->jenis_event }}</strong> kepada <strong>{{ $event->client->name ?? 'Client' }}</strong>
            di <strong>{{ $event->lokasi_event ?? '-' }}</strong>, maka dengan ini kami mengajukan surat penawaran <strong>"Special Price"</strong> sebagai berikut :
        </p>

        {{-- Rincian --}}
        <table style="border:none;border-collapse:collapse;width:100%;font-size:13px;margin-bottom:14px;">
            {{-- I. Lokasi --}}
            <tr>
                <td style="border:none;padding:3px 4px;width:32px;font-weight:700;vertical-align:top;">I.</td>
                <td style="border:none;padding:3px 4px;width:120px;vertical-align:top;">Lokasi</td>
                <td style="border:none;padding:3px 4px;width:16px;vertical-align:top;text-align:center;">:</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">{{ $event->lokasi_event ?? '-' }}</td>
            </tr>
            {{-- II. Jenis Kegiatan --}}
            <tr>
                <td style="border:none;padding:3px 4px;font-weight:700;vertical-align:top;">II.</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">Jenis Kegiatan</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;text-align:center;">:</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">{{ $event->jenis_event ?? '-' }}</td>
            </tr>
            {{-- III. Jadwal --}}
            <tr>
                <td style="border:none;padding:3px 4px;font-weight:700;vertical-align:top;">III.</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">Jadwal</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;text-align:center;">:</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">
                    <table style="border:none;border-collapse:collapse;width:100%;">
                        <tr>
                            <td style="border:none;padding:2px 3px;width:18px;">a.</td>
                            <td style="border:none;padding:2px 3px;width:80px;">Jadwal</td>
                            <td style="border:none;padding:2px 3px;width:14px;text-align:center;">:</td>
                            <td style="border:none;padding:2px 3px;">
                                {{ $event->tanggal_event?->translatedFormat('d F Y') ?? '-' }}
                                @if($event->tanggal_selesai ?? null)
                                    s/d {{ $event->tanggal_selesai->translatedFormat('d F Y') }}
                                    ({{ $event->tanggal_event->diffInDays($event->tanggal_selesai) + 1 }} hari)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none;padding:2px 3px;">b.</td>
                            <td style="border:none;padding:2px 3px;">Luas Area</td>
                            <td style="border:none;padding:2px 3px;text-align:center;">:</td>
                            <td style="border:none;padding:2px 3px;">{{ $event->luas_area ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;padding:2px 3px;">c.</td>
                            <td style="border:none;padding:2px 3px;">Price</td>
                            <td style="border:none;padding:2px 3px;text-align:center;">:</td>
                            <td style="border:none;padding:2px 3px;">
                                <strong>{{ $event->rentang_anggaran ?? '-' }}
                                @if($event->include_ppn ?? true)
                                    <small>(Include PPN &amp; PPh)*</small>
                                @endif
                                </strong>
                                @if($event->terbilang ?? null)
                                <br><small>({{ $event->terbilang }})</small>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            {{-- IV. Fasilitas --}}
            <tr>
                <td style="border:none;padding:3px 4px;font-weight:700;vertical-align:top;">IV.</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">Fasilitas</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;text-align:center;">:</td>
                <td style="border:none;padding:3px 4px;vertical-align:top;">
                </td>
            </tr>
        </table>

        {{-- V. Ketentuan --}}
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

        {{-- Penutup --}}
        <p style="text-align:justify;margin-top:14px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Apabila ada hal-hal yang perlu dipertanyakan, silahkan menghubungi Sdra. Fajar Viliano
            0895-4013-00022 atau melalui email : alphaorganizer1209@gmail.com. Demikianlah surat
            penawaran ini kami buat, Atas perhatian dan kerjasamanya kami ucapkan terimakasih.
        </p>

        {{-- Tanda Tangan --}}
        <div style="margin-top:24px;">
            <div>Padang, {{ $proposal->tanggal_proposal
                ? \Carbon\Carbon::parse($proposal->tanggal_proposal)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y') }}</div>
            <div>Hormat kami,</div>
            <div style="height:64px;"></div>
            <div style="font-weight:700;text-decoration:underline;">CV. Alpha Multi Organizer</div>
            <div>Direktur</div>
        </div>

    </div>{{-- /badan-surat --}}
</div>{{-- /surat --}}
</div>{{-- /bg-wrapper --}}

{{-- â”€â”€ Riwayat Proposal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@if($proposal->event->proposals->count() > 1)

<div style="max-width:820px;margin:0 auto 24px;">

    <div class="settings-card">

        <div class="settings-card-title">
            Riwayat Proposal
        </div>

        <div class="settings-card-desc">
            Seluruh revisi surat penawaran yang pernah dikirim oleh Admin.
        </div>

        <div style="margin-top:18px;display:flex;flex-direction:column;gap:12px;">

            @foreach($proposal->event->proposals()->latest('versi')->get() as $history)

            <div style="display:flex;justify-content:space-between;align-items:center;
                        border:1px solid var(--border);
                        border-radius:10px;
                        padding:14px 18px;">

                <div>

                    <div style="font-weight:700;color:var(--dark);">

                        Proposal Versi {{ $history->versi }}

                        @if($history->is_active)
                            <span class="badge badge-success">
                                Aktif
                            </span>
                        @endif

                    </div>

                    <div style="font-size:13px;color:var(--text-muted);">

                        {{ $history->tanggal_proposal?->isoFormat('D MMMM Y') }}

                    </div>

                </div>

                <div>

                    <span class="badge {{ $history->badge_class }}">
                        {{ $history->status_label }}
                    </span>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>

@endif

{{-- â”€â”€ Riwayat Negosiasi â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@if($negotiations->isNotEmpty())
<div style="max-width:820px;margin:0 auto 24px;">
    <div class="settings-card">
        <div class="settings-card-title">Riwayat Negosiasi</div>
        <div class="settings-card-desc">Negosiasi yang telah Anda ajukan untuk penawaran ini.</div>

        <div style="display:flex;flex-direction:column;gap:14px;margin-top:16px;">
            @foreach($negotiations as $nego)
            <div style="border:1px solid var(--border);border-radius:10px;padding:16px 20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <span style="font-size:13px;font-weight:700;color:var(--dark);">{{ $nego->user->name }}</span>
                    <span style="font-size:12px;color:var(--text-muted);">{{ $nego->created_at->isoFormat('D MMM Y, HH.mm') }}</span>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-size:11px;font-weight:700;color:var(--accent);margin-bottom:3px;text-transform:uppercase;letter-spacing:.05em;">Pesan</div>
                    <div style="font-size:14px;color:var(--dark);">{{ $nego->pesan }}</div>
                </div>
                @if($nego->budget_diinginkan)
                <div style="margin-bottom:8px;">
                    <div style="font-size:11px;font-weight:700;color:var(--accent);margin-bottom:3px;text-transform:uppercase;letter-spacing:.05em;">Budget Diinginkan</div>
                    <div style="font-size:14px;color:var(--dark);">Rp {{ number_format($nego->budget_diinginkan,0,',','.') }}</div>
                </div>
                @endif
                @if($nego->catatan_tambahan)
                <div>
                    <div style="font-size:11px;font-weight:700;color:var(--accent);margin-bottom:3px;text-transform:uppercase;letter-spacing:.05em;">Catatan Tambahan</div>
                    <div style="font-size:14px;color:var(--dark);">{{ $nego->catatan_tambahan }}</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{--  Kontrak  --}}
@if($event->contract)
<div style="max-width:820px;margin:0 auto;">
    <div class="settings-card">
        <div class="settings-card-title">Kontrak</div>
        <div class="settings-card-desc">
            Kontrak ditandatangani pada
            {{ $proposal->tanggal_proposal ? \Carbon\Carbon::parse($proposal->tanggal_proposal)->isoFormat('D MMMM Y') : '-' }}
        </div>
        @if($event->contract->file_url)
        <a href="{{ $event->contract->file_url }}" target="_blank" class="btn btn-ghost-accent" style="margin-top:12px;">
            <i class="bi bi-file-earmark-check"></i> Lihat Kontrak
        </a>
        @endif
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .page-header { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
}
</style>
@endpush
