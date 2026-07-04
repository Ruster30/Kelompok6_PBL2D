@extends('layouts.admin')

@section('title', 'Detail Klien - ' . $klien->name)
@section('page-title', 'Detail Klien')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Detail Klien</h1>
        <p>Profil, riwayat event, pembayaran, dan notifikasi.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <button onclick="openModalKirim({{ $klien->id }}, '{{ addslashes($klien->name) }}')"
                class="btn btn-secondary">
            <i class="fas fa-paper-plane"></i> Kirim Notifikasi
        </button>
        <a href="{{ route('admin.kelola-klien.edit', $klien) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Klien
        </a>
        <a href="{{ route('admin.kelola-klien.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="display:flex;align-items:center;gap:10px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#065f46;font-size:14px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start;">

    {{-- ── Kolom Kiri: Profil ───────────────────────────────────── --}}
    <div>
        {{-- Card Profil --}}
        <div class="card" style="padding:24px;text-align:center;margin-bottom:20px;">
            <div style="width:72px;height:72px;border-radius:50%;background:#14b8a6;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:700;margin:0 auto 14px;">
                {{ $klien->initials }}
            </div>
            <div style="font-size:17px;font-weight:700;color:#1e293b;margin-bottom:4px;">{{ $klien->name }}</div>
            @php
                $tanggal = $klien->last_active_at ?? $klien->updated_at;
                $isAktif = $tanggal && $tanggal->diffInDays(now()) <= 30;
            @endphp
            <span class="badge {{ $isAktif ? 'badge-active' : 'badge-gray' }}" style="margin-bottom:16px;">
                {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
            </span>

            <div style="border-top:1px solid #f1f5f9;padding-top:16px;text-align:left;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <i class="fas fa-envelope" style="color:#14b8a6;width:16px;font-size:13px;"></i>
                    <span style="font-size:13px;color:#334155;word-break:break-all;">{{ $klien->email }}</span>
                </div>
                @if($klien->phone)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <i class="fas fa-phone" style="color:#14b8a6;width:16px;font-size:13px;"></i>
                    <span style="font-size:13px;color:#334155;">{{ $klien->phone }}</span>
                </div>
                @endif
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <i class="fas fa-calendar-alt" style="color:#14b8a6;width:16px;font-size:13px;"></i>
                    <span style="font-size:13px;color:#334155;">Bergabung {{ $klien->created_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-clock" style="color:#14b8a6;width:16px;font-size:13px;"></i>
                    <span style="font-size:13px;color:#334155;">
                        Terakhir aktif: {{ $tanggal ? $tanggal->diffForHumans() : '-' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card Statistik --}}
        <div class="card" style="padding:20px;margin-bottom:20px;">
            <div style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px;">Statistik</div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#64748b;">Total Event</span>
                    <span style="font-size:14px;font-weight:700;color:#1e293b;">{{ $klien->events_count ?? $klien->events->count() }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#64748b;">Total Pembayaran</span>
                    <span style="font-size:14px;font-weight:700;color:#1e293b;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#64748b;">Notifikasi Diterima</span>
                    <span style="font-size:14px;font-weight:700;color:#1e293b;">{{ $notifikasi->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Toggle Status --}}
        <form action="{{ route('admin.kelola-klien.toggle-status', $klien) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit"
                    style="width:100%;padding:10px;border-radius:10px;border:1px solid {{ $isAktif ? '#f97316' : '#22c55e' }};background:{{ $isAktif ? '#fff7ed' : '#f0fdf4' }};color:{{ $isAktif ? '#ea580c' : '#16a34a' }};font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;">
                <i class="fas {{ $isAktif ? 'fa-ban' : 'fa-check-circle' }}"></i>
                {{ $isAktif ? 'Nonaktifkan Klien' : 'Aktifkan Klien' }}
            </button>
        </form>
    </div>

    {{-- ── Kolom Kanan: Tabs ────────────────────────────────────── --}}
    <div>
        {{-- Tab Header --}}
        <div style="display:flex;gap:0;border-bottom:2px solid #f1f5f9;margin-bottom:20px;">
            @foreach([['event','Event','fa-calendar'],['pembayaran','Pembayaran','fa-credit-card'],['notifikasi','Notifikasi','fa-bell']] as [$id,$label,$icon])
            <button onclick="switchTab('{{ $id }}')" id="tab-btn-{{ $id }}"
                    style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;color:#94a3b8;border-bottom:2px solid transparent;margin-bottom:-2px;transition:.2s;display:flex;align-items:center;gap:6px;">
                <i class="fas {{ $icon }}"></i> {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Tab: Event --}}
        <div id="tab-event">
            @forelse($klien->events as $event)
            <div class="card" style="padding:18px;margin-bottom:12px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:4px;">{{ $event->nama_event }}</div>
                        <div style="font-size:13px;color:#64748b;margin-bottom:8px;">
                            <i class="fas fa-map-marker-alt" style="color:#94a3b8;margin-right:4px;"></i>{{ $event->lokasi_event }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-calendar" style="color:#94a3b8;margin-right:4px;"></i>
                            {{ $event->tanggal_event ? $event->tanggal_event->format('d M Y') : '-' }}
                        </div>
                        <div style="font-size:12px;color:#94a3b8;">{{ $event->jenis_event }}</div>
                    </div>
                    <span class="badge badge-{{ $event->badge_class ?? 'gray' }}">{{ $event->status_label }}</span>
                </div>
                @if($event->total_invoice > 0)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;font-size:13px;color:#64748b;">
                    <i class="fas fa-file-invoice" style="color:#94a3b8;margin-right:4px;"></i>
                    Total invoice: <strong style="color:#1e293b;">Rp {{ number_format($event->total_invoice, 0, ',', '.') }}</strong>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:48px;color:#94a3b8;">
                <i class="fas fa-calendar-times" style="font-size:28px;margin-bottom:10px;display:block;"></i>
                Belum ada event terdaftar.
            </div>
            @endforelse
        </div>

        {{-- Tab: Pembayaran --}}
        <div id="tab-pembayaran" style="display:none;">
            @php
                $eventIds   = $klien->events->pluck('id');
                $payments   = \App\Models\Payment::whereHas('invoice', fn($q) => $q->whereIn('event_id', $eventIds))
                                ->with(['invoice.event'])
                                ->latest()
                                ->take(20)
                                ->get();
            @endphp
            @forelse($payments as $p)
            <div class="card" style="padding:16px;margin-bottom:10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1e293b;">{{ $p->invoice->event->nama_event ?? '-' }}</div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                            {{ $p->created_at->format('d M Y H:i') }} WIB
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:14px;font-weight:700;color:#1e293b;">
                            Rp {{ number_format($p->nominal, 0, ',', '.') }}
                        </div>
                        @php
                            $statusColor = match($p->status_pembayaran ?? '') {
                                'diverifikasi' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                'menunggu'     => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'ditolak'      => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                default        => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                            };
                        @endphp
                        <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                            {{ $p->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:48px;color:#94a3b8;">
                <i class="fas fa-receipt" style="font-size:28px;margin-bottom:10px;display:block;"></i>
                Belum ada riwayat pembayaran.
            </div>
            @endforelse
        </div>

        {{-- Tab: Notifikasi --}}
        <div id="tab-notifikasi" style="display:none;">
            @forelse($notifikasi as $n)
            <div class="card" style="padding:16px;margin-bottom:10px;">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-envelope" style="color:#8b5cf6;font-size:13px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;color:#1e293b;">{{ $n->judul }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $n->pesan }}</div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:6px;">
                            {{ $n->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;flex-shrink:0;">
                        Terkirim
                    </span>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:48px;color:#94a3b8;">
                <i class="fas fa-bell-slash" style="font-size:28px;margin-bottom:10px;display:block;"></i>
                Belum ada notifikasi terkirim ke klien ini.
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ─── Modal Kirim Notifikasi ──────────────────────────────────────── --}}
<div id="modalKirimNotif"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">Kirim Notifikasi</h3>
                <p style="font-size:13px;color:#94a3b8;margin:4px 0 0;">Kepada: {{ $klien->name }}</p>
            </div>
            <button onclick="closeModalKirim()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;padding:4px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.kelola-klien.kirim-notifikasi') }}" method="POST">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $klien->id }}">

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Tipe</label>
                <select name="tipe" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;">
                    <option value="info">📢 Info Umum</option>
                    <option value="promo">🎉 Promo</option>
                    <option value="pengingat">⏰ Pengingat</option>
                    <option value="pembayaran">💳 Pembayaran</option>
                    <option value="event">📅 Event</option>
                    <option value="peringatan">⚠️ Peringatan</option>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Judul <span style="color:#ef4444;">*</span></label>
                <input type="text" name="judul" required maxlength="255" placeholder="Judul notifikasi..."
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Pesan <span style="color:#ef4444;">*</span></label>
                <textarea name="pesan" required rows="4" maxlength="2000" placeholder="Isi pesan..."
                          style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;resize:vertical;box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" onclick="closeModalKirim()" class="btn btn-secondary" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-paper-plane"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switcher
const tabs = ['event', 'pembayaran', 'notifikasi'];
const colors = { active: '#14b8a6', inactive: '#94a3b8' };

function switchTab(name) {
    tabs.forEach(t => {
        document.getElementById('tab-' + t).style.display = t === name ? 'block' : 'none';
        const btn = document.getElementById('tab-btn-' + t);
        btn.style.color     = t === name ? colors.active : colors.inactive;
        btn.style.borderBottomColor = t === name ? colors.active : 'transparent';
    });
}
switchTab('event'); // default

function openModalKirim() {
    document.getElementById('modalKirimNotif').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModalKirim() {
    document.getElementById('modalKirimNotif').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('modalKirimNotif').addEventListener('click', function(e) {
    if (e.target === this) closeModalKirim();
});
</script>
@endsection
