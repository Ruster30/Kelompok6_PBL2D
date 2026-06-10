@extends('layouts.client')
@section('title','Detail Penawaran')
@section('page-title','Surat Penawaran')

@section('content')

<div style="margin-bottom:20px;">
    <a href="{{ route('client.proposals') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:800px;">

    {{-- Header Card --}}
    <div class="settings-card" style="margin-bottom:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <div style="font-size:11px;font-weight:700;color:var(--accent);
                            text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
                    Surat Penawaran
                </div>
                <h2 style="font-size:22px;font-weight:800;color:var(--dark);margin-bottom:4px;">
                    {{ $proposal->event->nama_event }}
                </h2>
                <p style="color:var(--text-muted);font-size:13px;">
                    {{ $proposal->event->jenis_event }} • v{{ $proposal->versi }}
                </p>
            </div>
            <span class="badge {{ $proposal->badge_class }}" style="font-size:12px;padding:6px 14px;">
                {{ $proposal->status_label }}
            </span>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                    Tanggal Event
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->event->tanggal_event->isoFormat('D MMMM Y') }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                    Lokasi
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->event->lokasi_event }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                    Jumlah Tamu
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ number_format($proposal->event->jumlah_tamu) }} orang
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                    Tanggal Proposal
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->tanggal_proposal?->isoFormat('D MMMM Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- RAB / Rincian Anggaran --}}
    @if($proposal->event->rabs->isNotEmpty())
    <div class="settings-card" style="margin-bottom:20px;">
        <div class="settings-card-title">Rincian Anggaran Biaya (RAB)</div>
        <div class="settings-card-desc">Detail estimasi biaya untuk event Anda.</div>

        <div class="invoice-table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Nama Biaya</th>
                        <th>Kategori</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposal->event->rabs as $rab)
                    <tr>
                        <td style="font-weight:600;">{{ $rab->nama_biaya }}</td>
                        <td>
                            <span style="background:var(--accent-light);color:var(--accent-dark);
                                         padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                                {{ $rab->kategori_biaya }}
                            </span>
                        </td>
                        <td style="text-align:center;">{{ $rab->jumlah_item }}</td>
                        <td style="text-align:right;">{{ $rab->formatted_harga }}</td>
                        <td style="text-align:right;font-weight:700;">{{ $rab->formatted_subtotal }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"
                            style="text-align:right;font-weight:800;color:var(--dark);
                                   padding:14px 20px;background:var(--body-bg);">
                            Total Estimasi
                        </td>
                        <td style="text-align:right;font-weight:800;color:var(--accent);
                                   font-size:15px;padding:14px 20px;background:var(--body-bg);">
                            Rp {{ number_format($proposal->event->rabs->sum('subtotal_biaya'),0,',','.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- File Proposal --}}
    @if($proposal->file_url)
    <div class="settings-card" style="margin-bottom:20px;">
        <div class="settings-card-title">File Surat Penawaran</div>
        <div class="settings-card-desc">Unduh file PDF surat penawaran resmi dari tim kami.</div>
        <a href="{{ $proposal->file_url }}" target="_blank" class="btn btn-accent">
            <i class="bi bi-download"></i> Unduh Surat Penawaran (PDF)
        </a>
    </div>
    @endif

    {{-- Kontrak --}}
    @if($proposal->event->contract)
    <div class="settings-card">
        <div class="settings-card-title">Kontrak</div>
        <div class="settings-card-desc">
            Kontrak ditandatangani pada
            {{ $proposal->event->contract->tanggal_kontrak?->isoFormat('D MMMM Y') ?? '-' }}
        </div>
        @if($proposal->event->contract->file_url)
        <a href="{{ $proposal->event->contract->file_url }}" target="_blank"
           class="btn btn-ghost-accent">
            <i class="bi bi-file-earmark-check"></i> Lihat Kontrak
        </a>
        @endif
    </div>
    @endif

</div>

@endsection