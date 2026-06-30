@extends('layouts.client')
@section('title','Ajukan Negosiasi')
@section('page-title','Negosiasi')

@section('content')

{{-- Tombol kembali --}}
<div style="margin-bottom:24px;">
    <a href="{{ route('client.proposals.show', $proposal->id) }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:14px;color:var(--text-muted);text-decoration:none;font-weight:500;">
        <i class="bi bi-arrow-left"></i> Kembali ke Penawaran
    </a>
</div>

<div style="max-width:700px; margin:0 auto;">

    {{-- ── Judul Halaman ── --}}
    <div style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <div style="width:32px;height:32px;background:var(--accent-light);border-radius:8px;
                        display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-chat-left-dots-fill" style="color:var(--accent);font-size:15px;"></i>
            </div>
            <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin:0;">Ajukan Negosiasi</h1>
        </div>
        <p style="color:var(--text-muted);font-size:14px;margin:0;padding-left:42px;">
            Diskusikan penyesuaian anggaran atau fasilitas untuk event {{ $proposal->event->nama_event }}.
        </p>
    </div>

    {{-- ── Card: Detail Penawaran Saat Ini ── --}}
    <div style="background:white;border:1px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--dark);margin-bottom:14px;">
            Detail Penawaran Saat Ini
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:500;margin-bottom:4px;">
                    Anggaran Ditawarkan
                </div>
                <div style="font-size:15px;font-weight:700;color:var(--dark);">
                    {{ $proposal->event->rentang_anggaran ?? 'Di bawah Rp 100 Juta' }}
                </div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-muted);font-weight:500;margin-bottom:4px;">
                    No. Surat
                </div>
                <div style="font-size:15px;font-weight:700;color:var(--dark);">
                    {{ $proposal->nomor_proposal ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── Card: Form Negosiasi ── --}}
    <div style="background:white;border:1px solid var(--border);border-radius:14px;padding:28px 24px;">

        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right:6px;"></i>
            @foreach($errors->all() as $err)
                {{ $err }}<br>
            @endforeach
        </div>
        @endif

        <form action="{{ route('client.proposals.negosiasi', $proposal->id) }}" method="POST">
            @csrf

            {{-- Pesan Negosiasi --}}
            <div style="margin-bottom:22px;">
                <label style="display:block;font-size:14px;font-weight:600;color:var(--dark);margin-bottom:8px;">
                    Pesan Negosiasi <span style="color:#ef4444;">*</span>
                </label>
                <textarea name="pesan" rows="5" required
                    style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:10px;
                           font-size:14px;font-family:inherit;resize:vertical;outline:none;color:var(--dark);
                           transition:border-color .15s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
                    placeholder="Jelaskan bagian mana dari penawaran yang ingin Anda negosiasikan (misal: penyesuaian anggaran, tambahan fasilitas, perubahan jadwal, dll.)...">{{ old('pesan') }}</textarea>
            </div>

            {{-- Budget yang Diinginkan --}}
            <div style="margin-bottom:22px;">
                <label style="display:block;font-size:14px;font-weight:600;color:var(--dark);margin-bottom:8px;">
                    Budget yang Diinginkan <span style="font-size:12px;color:var(--text-muted);font-weight:400;">(Opsional)</span>
                </label>
                <input type="text" name="budget_diinginkan" value="{{ old('budget_diinginkan') }}"
                    style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:10px;
                           font-size:14px;font-family:inherit;outline:none;color:var(--dark);
                           transition:border-color .15s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
                    placeholder="mis. Rp 150.000.000">
                <p style="font-size:12px;color:var(--text-muted);margin-top:5px;">Kosongkan jika tidak ada perubahan budget yang diinginkan.</p>
            </div>

            {{-- Catatan Tambahan --}}
            <div style="margin-bottom:28px;">
                <label style="display:block;font-size:14px;font-weight:600;color:var(--dark);margin-bottom:8px;">
                    Catatan Tambahan <span style="font-size:12px;color:var(--text-muted);font-weight:400;">(Opsional)</span>
                </label>
                <textarea name="catatan_tambahan" rows="4"
                    style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:10px;
                           font-size:14px;font-family:inherit;resize:vertical;outline:none;color:var(--dark);
                           transition:border-color .15s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
                    placeholder="Informasi tambahan lainnya...">{{ old('catatan_tambahan') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('client.proposals.show', $proposal->id) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:11px 22px;
                          border:1.5px solid var(--border);border-radius:8px;font-size:14px;
                          font-weight:600;color:var(--dark);text-decoration:none;
                          background:white;transition:all .15s;"
                   onmouseover="this.style.background='var(--body-bg)'" onmouseout="this.style.background='white'">
                    Batal
                </a>
                <button type="submit"
                    style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;
                           background:var(--accent);color:white;border:none;border-radius:8px;
                           font-size:14px;font-weight:600;cursor:pointer;transition:opacity .15s;"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    Kirim Negosiasi <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection