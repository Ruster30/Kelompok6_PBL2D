@extends('layouts.admin')

@section('title', 'Kelola Klien')
@section('page-title', 'Kelola Klien')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Kelola Klien</h1>
        <p>Manajemen akun klien &amp; riwayat pengiriman notifikasi.</p>
    </div>
    <button class="btn btn-primary" onclick="openModalKirimAll()">
        <i class="fas fa-paper-plane"></i> Kirim Notifikasi
    </button>
</div>

{{-- â”€â”€â”€ Flash Messages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@if(session('success'))
<div class="alert alert-success" style="display:flex;align-items:center;gap:10px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#065f46;font-size:14px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- â”€â”€â”€ Stats Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <i class="fas fa-users" style="color:#3b82f6;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="margin-bottom:10px;">{{ $stats['total'] }}</div>
            <div class="stat-label" style="margin-bottom:6px;">Total Klien</div>
            <div class="stat-sub">Semua klien terdaftar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <i class="fas fa-user-check" style="color:#22c55e;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="margin-bottom:10px;">{{ $stats['aktif'] }}</div>
            <div class="stat-label" style="margin-bottom:6px;">Klien Aktif</div>
            <div class="stat-sub">Aktif dalam 30 hari</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;">
            <i class="fas fa-user-times" style="color:#f97316;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="margin-bottom:10px;">{{ $stats['nonaktif'] }}</div>
            <div class="stat-label" style="margin-bottom:6px;">Klien Nonaktif</div>
            <div class="stat-sub">Tidak aktif > 30 hari</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;">
            <i class="fas fa-envelope" style="color:#8b5cf6;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="margin-bottom:10px;">{{ $stats['notif_terkirim'] }}</div>
            <div class="stat-label" style="margin-bottom:6px;">Notifikasi Terkirim</div>
            <div class="stat-sub">7 hari terakhir</div>
        </div>
    </div>
</div>

{{-- â”€â”€â”€ Table Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="card">
    {{-- Toolbar --}}
    <div class="card-header" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;border-bottom:1px solid #f1f5f9;padding-bottom:16px;margin-bottom:0;">
        <form method="GET" action="{{ route('admin.kelola-klien.index') }}" id="filterForm"
              style="display:flex;align-items:center;gap:12px;flex:1;flex-wrap:wrap;">

            {{-- Pencarian --}}
            <div class="search-wrap" style="min-width:240px; margin-top:18px;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" id="searchInput"
                       placeholder="Cari nama klien, email, atau telepon..."
                       value="{{ $filters['search'] ?? '' }}">
            </div>

            {{-- Filter Status --}}
            <div style="display:flex;flex-direction:column;gap:2px;">
                <label style="font-size:11px;color:#94a3b8;font-weight:600;">Status</label>
                <select name="status" onchange="document.getElementById('filterForm').submit()"
                        style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 32px 8px 12px;font-size:13px;color:#334155;background:#fff;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;">
                    <option value="" {{ empty($filters['status']) ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ ($filters['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Sorting --}}
            <div style="display:flex;flex-direction:column;gap:2px;">
                <label style="font-size:11px;color:#94a3b8;font-weight:600;">Urutkan</label>
                <select name="sort" onchange="document.getElementById('filterForm').submit()"
                        style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 32px 8px 12px;font-size:13px;color:#334155;background:#fff;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;">
                    <option value="terbaru" {{ ($filters['sort'] ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($filters['sort'] ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="nama_az" {{ ($filters['sort'] ?? '') === 'nama_az' ? 'selected' : '' }}>Nama Aâ€“Z</option>
                    <option value="nama_za" {{ ($filters['sort'] ?? '') === 'nama_za' ? 'selected' : '' }}>Nama Zâ€“A</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary" style="margin-top:18px;">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(!empty($filters['search']) || !empty($filters['status']))
            <a href="{{ route('admin.kelola-klien.index') }}" class="btn btn-secondary" style="margin-top:18px;">
                <i class="fas fa-times"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive-wrap card-view-mobile"><table>
        <thead>
            <tr>
                <th style="width:40px;">NO</th>
                <th>NAMA KLIEN</th>
                <th>KONTAK</th>
                <th>STATUS</th>
                <th style="text-align:center;">TOTAL EVENT</th>
                <th>TERAKHIR AKTIF</th>
                <th style="text-align:center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kliens as $klien)
            @php
                $tanggal    = $klien->last_active_at ?? $klien->updated_at;
                $isAktif    = $tanggal && $tanggal->diffInDays(now()) <= 30;
                $initials   = $klien->initials;
            @endphp
            <td data-label="No" style="color:#94a3b8;font-size:13px;">{{ $kliens->firstItem() + $loop->index }}</td>

                {{-- Avatar + Nama --}}
                <td> data-label="Nama Klien"
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:#14b8a6;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                            {{ $initials }}
                        </div>
                        <div>
                            <div style="font-weight:600;color:#1e293b;font-size:14px;">{{ $klien->name }}</div>
                            @if($klien->events_count > 0)
                            <div style="font-size:12px;color:#94a3b8;">{{ $klien->events_count }} event terdaftar</div>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Kontak --}}
                <td> data-label="Kontak"
                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <span style="font-size:13px;color:#334155;display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-envelope" style="color:#94a3b8;font-size:11px;"></i>
                            {{ $klien->email }}
                        </span>
                        @if($klien->phone)
                        <span style="font-size:13px;color:#334155;display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-phone" style="color:#94a3b8;font-size:11px;"></i>
                            {{ $klien->phone }}
                        </span>
                        @endif
                    </div>
                </td>

                {{-- Status --}}
                <td> data-label="Status"
                    <span class="badge {{ $isAktif ? 'badge-active' : 'badge-gray' }}">
                        {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>

                {{-- Total Event --}}
                <td style="text-align:center;"> data-label="Total Event"
                    <span style="font-weight:600;color:#1e293b;">{{ $klien->events_count }}</span>
                </td>

                {{-- Terakhir Aktif --}}
                <td> data-label="Terakhir Aktif"
                    <div style="font-size:13px;color:#334155;">
                        {{ $tanggal ? $tanggal->format('d/m/Y') : '-' }}
                    </div>
                    @if($tanggal)
                    <div style="font-size:11px;color:#94a3b8;">
                        {{ $tanggal->format('H:i') }} WIB
                    </div>
                    @endif
                </td>

                {{-- Aksi --}}
                <td> data-label="Aksi"
                    <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                        {{-- Kirim Notifikasi --}}
                        <button onclick="openModalKirim({{ $klien->id }}, '{{ addslashes($klien->name) }}')"
                                title="Kirim Notifikasi"
                                style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6366f1;transition:.2s;"
                                onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='#fff'">
                            <i class="fas fa-envelope" style="font-size:13px;"></i>
                        </button>

                        {{-- Lihat Detail --}}
                        <button onclick="window.location.href='{{ route('admin.kelola-klien.show', $klien) }}'"
                           class="btn btn-primary"
                           style="padding:6px 14px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;border-radius:8px;white-space:nowrap;">
                            <i class="fas fa-eye" style="font-size:12px;"></i> Lihat Detail
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">
                    <i class="fas fa-users" style="font-size:32px;margin-bottom:12px;display:block;"></i>
                    Belum ada klien terdaftar.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table></div>

    {{-- Pagination --}}
    @if($kliens->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-top:1px solid #f1f5f9;">
        <span style="font-size:13px;color:#64748b;">
            Menampilkan {{ $kliens->firstItem() }} hingga {{ $kliens->lastItem() }} dari {{ $kliens->total() }} data
        </span>
        <div style="display:flex;gap:6px;align-items:center;">
            @if($kliens->onFirstPage())
            <span style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:13px;">
                <i class="fas fa-chevron-left"></i>
            </span>
            @else
            <a href="{{ $kliens->previousPageUrl() }}"
               style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;font-size:13px;transition:.2s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <i class="fas fa-chevron-left"></i>
            </a>
            @endif

            @foreach($kliens->getUrlRange(1, $kliens->lastPage()) as $page => $url)
            <a href="{{ $url }}"
               style="width:32px;height:32px;border-radius:8px;border:1px solid {{ $page == $kliens->currentPage() ? '#14b8a6' : '#e2e8f0' }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:{{ $page == $kliens->currentPage() ? '700' : '400' }};background:{{ $page == $kliens->currentPage() ? '#14b8a6' : '#fff' }};color:{{ $page == $kliens->currentPage() ? '#fff' : '#64748b' }};text-decoration:none;transition:.2s;">
                {{ $page }}
            </a>
            @endforeach

            @if($kliens->hasMorePages())
            <a href="{{ $kliens->nextPageUrl() }}"
               style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;font-size:13px;transition:.2s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <i class="fas fa-chevron-right"></i>
            </a>
            @else
            <span style="width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:13px;">
                <i class="fas fa-chevron-right"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- â”€â”€â”€ Notifikasi Terkirim Terbaru â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@php
    $recentNotifs = \App\Models\AdminClientNotification::with('recipient')
        ->where('sender_id', auth()->id())
        ->latest()
        ->take(5)
        ->get();
@endphp
@if($recentNotifs->isNotEmpty())
<div class="card" style="margin-top:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 20px 16px;border-bottom:1px solid #f1f5f9;">
        <h3 style="font-size:15px;font-weight:600;color:#1e293b;margin:0;">Notifikasi Terbaru</h3>
        <a href="{{ route('admin.notifications.index') }}" style="font-size:13px;color:#14b8a6;text-decoration:none;font-weight:500;">
            Lihat Semua
        </a>
    </div>
    <div>
        @foreach($recentNotifs as $n)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f8fafc;">
            <div style="width:38px;height:38px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-envelope" style="color:#8b5cf6;font-size:14px;"></i>
            </div>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;">{{ $n->judul }}</div>
                <div style="font-size:12px;color:#94a3b8;">Dikirim ke {{ $n->recipient->name ?? '-' }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-size:12px;color:#64748b;">{{ $n->created_at->format('d/m/Y') }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $n->created_at->format('H:i') }} WIB</div>
            </div>
            <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;flex-shrink:0;">
                Terkirim
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- â”€â”€â”€ Modal Kirim Notifikasi â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div id="modalKirimNotif"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">Kirim Notifikasi</h3>
                <p id="modalKirimSubtitle" style="font-size:13px;color:#94a3b8;margin:4px 0 0;"></p>
            </div>
            <button onclick="closeModalKirim()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;padding:4px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.kelola-klien.kirim-notifikasi') }}" method="POST">
            @csrf
            <input type="hidden" name="recipient_id" id="modalRecipientId">

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Tipe Notifikasi
                </label>
                <select name="tipe" required
                        style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;">
                    <option value="info">ðŸ“¢ Info Umum</option>
                    <option value="promo">ðŸŽ‰ Promo</option>
                    <option value="pengingat">â° Pengingat</option>
                    <option value="pembayaran">ðŸ’³ Pembayaran</option>
                    <option value="event">ðŸ“… Event</option>
                    <option value="peringatan">âš ï¸ Peringatan</option>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Judul Notifikasi <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="judul" required maxlength="255"
                       placeholder="Cth: Pengingat pembayaran DP Event Konferensi"
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Isi Pesan <span style="color:#ef4444;">*</span>
                </label>
                <textarea name="pesan" required rows="4" maxlength="2000"
                          placeholder="Tulis pesan notifikasi untuk klien..."
                          style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;resize:vertical;box-sizing:border-box;"></textarea>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Maks. 2000 karakter</div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" onclick="closeModalKirim()"
                        class="btn btn-secondary" style="flex:1;">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-paper-plane"></i> Kirim Notifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// â”€â”€â”€ Search dengan debounce â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
let searchTimer;
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 600);
});

// â”€â”€â”€ Modal Notifikasi â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function openModalKirim(id, nama) {
    document.getElementById('modalRecipientId').value = id;
    document.getElementById('modalKirimSubtitle').textContent = 'Kepada: ' + nama;
    document.getElementById('modalKirimNotif').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openModalKirimAll() {
    // Kosongkan recipient_id â†’ semua klien
    document.getElementById('modalRecipientId').value = '';
    document.getElementById('modalKirimSubtitle').textContent = 'Pilih klien tertentu atau kosongkan untuk semua.';
    document.getElementById('modalKirimNotif').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModalKirim() {
    document.getElementById('modalKirimNotif').style.display = 'none';
    document.body.style.overflow = '';
}

// Tutup modal saat klik overlay
document.getElementById('modalKirimNotif').addEventListener('click', function (e) {
    if (e.target === this) closeModalKirim();
});

</script>
@endsection



