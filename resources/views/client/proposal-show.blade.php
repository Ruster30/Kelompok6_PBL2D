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

    {{-- ── Header Card ─────────────────────────────── --}}
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
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Tanggal Event</div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->event->tanggal_event->isoFormat('D MMMM Y') }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Lokasi</div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->event->lokasi_event }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Jumlah Tamu</div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ number_format($proposal->event->jumlah_tamu) }} orang
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;
                            text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Tanggal Proposal</div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">
                    {{ $proposal->tanggal_proposal?->isoFormat('D MMMM Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── RAB ──────────────────────────────────────── --}}
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
                        <td colspan="4" style="text-align:right;font-weight:800;color:var(--dark);
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

    {{-- ── File PDF ─────────────────────────────────── --}}
    @if($proposal->file_url)
    <div class="settings-card" style="margin-bottom:20px;">
        <div class="settings-card-title">File Surat Penawaran</div>
        <div class="settings-card-desc">Unduh file PDF surat penawaran resmi dari tim kami.</div>
        <a href="{{ $proposal->file_url }}" target="_blank" class="btn btn-accent">
            <i class="bi bi-download"></i> Unduh Surat Penawaran (PDF)
        </a>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         TOMBOL AKSI — muncul jika proposal masih diajukan
    ══════════════════════════════════════════════════ --}}
    @if($proposal->status === 'diajukan')
    <div class="settings-card" style="margin-bottom:20px; border: 2px solid var(--accent);">
        <div class="settings-card-title">Respon Penawaran</div>
        <div class="settings-card-desc" style="margin-bottom:16px;">
            Silakan pilih salah satu opsi di bawah untuk merespon penawaran ini.
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            {{-- ── Terima Langsung ── --}}
            @php $hasNego = $negotiations->isNotEmpty(); @endphp

            @if(!$hasNego)
            {{-- Belum ada negosiasi → tombol Terima Langsung --}}
            <form action="{{ route('client.proposals.terima', $proposal->id) }}" method="POST"
                  onsubmit="return confirm('Yakin menerima penawaran ini? Timeline event akan otomatis diisi.')">
                @csrf
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-circle"></i> Terima Penawaran
                </button>
            </form>
            @else
            {{-- Sudah ada negosiasi sebelumnya → tombol Terima Setelah Negosiasi --}}
            <form action="{{ route('client.proposals.terima-setelah-negosiasi', $proposal->id) }}" method="POST"
                  onsubmit="return confirm('Yakin menerima penawaran revisi ini? Timeline event akan otomatis diisi.')">
                @csrf
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-circle"></i> Terima Penawaran Revisi
                </button>
            </form>
            @endif

            {{-- ── Ajukan Negosiasi ── --}}
            <button class="btn btn-outline" onclick="document.getElementById('negoModal').style.display='flex'">
                <i class="bi bi-chat-dots"></i> Ajukan Negosiasi
            </button>
        </div>
    </div>
    @endif

    {{-- ── Riwayat Negosiasi ───────────────────────── --}}
    @if($negotiations->isNotEmpty())
    <div class="settings-card" style="margin-bottom:20px;">
        <div class="settings-card-title">Riwayat Negosiasi</div>
        <div class="settings-card-desc">Negosiasi yang telah Anda ajukan untuk penawaran ini.</div>

        <div style="display:flex; flex-direction:column; gap:14px; margin-top:16px;">
            @foreach($negotiations as $nego)
            <div style="border:1px solid var(--border); border-radius:10px; padding:16px 20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span style="font-size:13px; font-weight:700; color:var(--dark);">
                        {{ $nego->user->name }}
                    </span>
                    <span style="font-size:12px; color:var(--text-muted);">
                        {{ $nego->created_at->isoFormat('D MMM Y, HH.mm') }}
                    </span>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-size:11px; font-weight:700; color:var(--accent); margin-bottom:3px; text-transform:uppercase; letter-spacing:.05em;">Pesan</div>
                    <div style="font-size:14px; color:var(--dark);">{{ $nego->pesan }}</div>
                </div>
                @if($nego->budget_diinginkan)
                <div style="margin-bottom:8px;">
                    <div style="font-size:11px; font-weight:700; color:var(--accent); margin-bottom:3px; text-transform:uppercase; letter-spacing:.05em;">Budget Diinginkan</div>
                    <div style="font-size:14px; color:var(--dark);">Rp {{ number_format($nego->budget_diinginkan, 0, ',', '.') }}</div>
                </div>
                @endif
                @if($nego->catatan_tambahan)
                <div>
                    <div style="font-size:11px; font-weight:700; color:var(--accent); margin-bottom:3px; text-transform:uppercase; letter-spacing:.05em;">Catatan Tambahan</div>
                    <div style="font-size:14px; color:var(--dark);">{{ $nego->catatan_tambahan }}</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Kontrak ──────────────────────────────────── --}}
    @if($proposal->event->contract)
    <div class="settings-card">
        <div class="settings-card-title">Kontrak</div>
        <div class="settings-card-desc">
            Kontrak ditandatangani pada
            {{ $proposal->event->contract->tanggal_kontrak?->isoFormat('D MMMM Y') ?? '-' }}
        </div>
        @if($proposal->event->contract->file_url)
        <a href="{{ $proposal->event->contract->file_url }}" target="_blank" class="btn btn-ghost-accent">
            <i class="bi bi-file-earmark-check"></i> Lihat Kontrak
        </a>
        @endif
    </div>
    @endif

</div>{{-- /max-width --}}

{{-- ══ Modal Negosiasi ══════════════════════════════════════ --}}
<div id="negoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; width:520px; max-width:95vw; max-height:90vh; overflow-y:auto;">
        <div style="padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:16px; font-weight:700;">Ajukan Negosiasi</span>
            <button onclick="document.getElementById('negoModal').style.display='none'"
                    style="background:none; border:none; cursor:pointer; font-size:20px; color:#64748b;">&times;</button>
        </div>
        <form action="{{ route('client.proposals.negosiasi', $proposal->id) }}" method="POST" style="padding:24px;">
            @csrf
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div>
                    <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
                        Pesan / Permintaan Negosiasi *
                    </label>
                    <textarea name="pesan" rows="4" required
                              style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; font-family:inherit; resize:vertical; outline:none;"
                              placeholder="Jelaskan permintaan negosiasi Anda..."></textarea>
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
                        Budget yang Diinginkan (Rp)
                    </label>
                    <input type="number" name="budget_diinginkan" min="0"
                           style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;"
                           placeholder="Kosongkan jika tidak ada perubahan budget">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
                        Catatan Tambahan
                    </label>
                    <textarea name="catatan_tambahan" rows="3"
                              style="width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; font-family:inherit; resize:vertical; outline:none;"
                              placeholder="Catatan atau permintaan khusus lainnya..."></textarea>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('negoModal').style.display='none'">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-send"></i> Kirim Negosiasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
