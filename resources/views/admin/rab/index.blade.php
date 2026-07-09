@extends('layouts.admin')

@section('title', 'Anggaran (RAB)')
@section('page-title', 'Anggaran (RAB)')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Rencana Anggaran Biaya (RAB)</h1>
        <p>Kelola anggaran detail per event.</p>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <select class="select-filter" id="eventSelect" onchange="changeEvent(this.value)" style="min-width:220px;">
            <option value="">Belum ada event</option>
            @foreach($events as $event)
            <option value="{{ $event->id }}" {{ $selectedEvent && $selectedEvent->id == $event->id ? 'selected' : '' }}>
                {{ $event->nama_event }}
            </option>
            @endforeach
        </select>
        @if($selectedEvent)
        <button class="btn btn-primary" onclick="openAddRab()">
            <i class="fas fa-plus"></i> Tambah Item
        </button>
        @endif
    </div>
</div>

@if(!$selectedEvent)
<div class="card">
    <div class="empty-state">
        <i class="fas fa-exclamation-circle"></i>
        <h3>Pilih Event</h3>
        <p>Silakan pilih event dari dropdown di atas untuk mengelola RAB.</p>
    </div>
</div>
@else

{{-- Summary Cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-list stat-icon"></i><span class="stat-badge">Items</span></div>
        <div class="stat-value">{{ $rabItems->count() }}</div>
        <div class="stat-label">Total Item RAB</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-calculator stat-icon"></i><span class="stat-badge green">Total</span></div>
        <div class="stat-value" id="statTotalAnggaran" style="font-size:18px;">Rp {{ number_format($rabItems->sum('subtotal_biaya'), 0, ',', '.') }}</div>
        <div class="stat-label">Total Anggaran RAB</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-users stat-icon"></i><span class="stat-badge blue">Vendor</span></div>
        <div class="stat-value">{{ $rabItems->whereNotNull('vendor_id')->unique('vendor_id')->count() }}</div>
        <div class="stat-label">Vendor Terlibat</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Detail RAB &mdash; {{ $selectedEvent->nama_event }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Biaya</th>
                <th>Kategori</th>
                <th>Vendor</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rabItems as $i => $item)
            <tr>
                <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="font-weight:500;">{{ $item->nama_biaya }}</td>
                <td>{{ $item->kategori_biaya ?? '-' }}</td>
                <td>{{ $item->vendor->nama_vendor ?? '-' }}</td>
                <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                <td>{{ $item->jumlah_item }}</td>
                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td style="font-weight:600;">Rp {{ number_format($item->subtotal_biaya, 0, ',', '.') }}</td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn" title="Edit" onclick='openEditRab({{ json_encode($item) }})'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.rab.destroy', $item->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalDelete(this, {text: 'Item RAB {{ addslashes($item->nama_biaya) }} akan dihapus.'})">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="9">Belum ada item RAB. Klik &quot;Tambah Item&quot; untuk mulai.</td></tr>
            @endforelse
        </tbody>
        @if($rabItems->count())
        <tfoot>
            <tr style="background:#f8fafc; font-weight:700;">
                <td colspan="6" style="padding:14px 24px; color:#374151; text-align:right;">TOTAL</td>
                <td style="padding:14px 24px; color:#0f766e;">Rp {{ number_format($rabItems->sum('subtotal_biaya'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Rincian Tambahan --}}
<div style="display:grid; grid-template-columns:1fr 380px; gap:20px; margin-bottom:24px;">
    {{-- Left: Rincian Tambahan Table --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <span class="card-title">Rincian Tambahan</span>
        </div>
        <form id="additionalDetailsForm" action="{{ route('admin.rab.additional-details') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:30%;">Komponen</th>
                            <th style="width:20%;">Status</th>
                            <th style="width:25%;">Persentase (%)</th>
                            <th style="width:25%;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Fee EO --}}
                        <tr>
                            <td style="font-weight:600;">Fee EO</td>
                            <td>
                                <div class="form-check form-switch" style="display:flex; align-items:center; gap:8px; margin:0; padding:0; min-height:auto;">
                                            <input type="hidden" name="fee_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                           id="fee_enabled" name="fee_enabled" value="1"
                                           style="cursor:pointer; width:40px; height:22px; margin:0;"
                                           {{ $additionalDetail && $additionalDetail->fee_enabled ? 'checked' : '' }}
                                           onchange="toggleComponent('fee')">
                                    <span id="fee_status_label" style="font-size:12px; font-weight:600; {{ $additionalDetail && $additionalDetail->fee_enabled ? 'color:#10b981;' : 'color:#94a3b8;' }}">
                                        {{ $additionalDetail && $additionalDetail->fee_enabled ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="fee_percent" id="fee_percent"
                                       class="form-input" style="width:80px; padding:6px 10px; text-align:center;"
                                       value="{{ $additionalDetail?->fee_percent ?? 10 }}"
                                       min="0" max="100" step="0.01"
                                       {{ $additionalDetail && $additionalDetail->fee_enabled ? '' : 'disabled' }}
                                       oninput="hitungRincian()">
                            </td>
                            <td>
                                <span id="fee_nominal" style="font-weight:600; color:#0f766e;">Rp 0</span>
                            </td>
                        </tr>
                        {{-- PPN --}}
                        <tr>
                            <td style="font-weight:600;">PPN</td>
                            <td>
                                <div class="form-check form-switch" style="display:flex; align-items:center; gap:8px; margin:0; padding:0; min-height:auto;">                                            <input type="hidden" name="ppn_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                           id="ppn_enabled" name="ppn_enabled" value="1"
                                           style="cursor:pointer; width:40px; height:22px; margin:0;"
                                           {{ $additionalDetail && $additionalDetail->ppn_enabled ? 'checked' : '' }}
                                           onchange="toggleComponent('ppn')">
                                    <span id="ppn_status_label" style="font-size:12px; font-weight:600; {{ $additionalDetail && $additionalDetail->ppn_enabled ? 'color:#10b981;' : 'color:#94a3b8;' }}">
                                        {{ $additionalDetail && $additionalDetail->ppn_enabled ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="ppn_percent" id="ppn_percent"
                                       class="form-input" style="width:80px; padding:6px 10px; text-align:center;"
                                       value="{{ $additionalDetail?->ppn_percent ?? 11 }}"
                                       min="0" max="100" step="0.01"
                                       {{ $additionalDetail && $additionalDetail->ppn_enabled ? '' : 'disabled' }}
                                       oninput="hitungRincian()">
                            </td>
                            <td>
                                <span id="ppn_nominal" style="font-weight:600; color:#0f766e;">Rp 0</span>
                            </td>
                        </tr>
                        {{-- PPh --}}
                        <tr>
                            <td style="font-weight:600;">PPh</td>
                            <td>
                                <div class="form-check form-switch" style="display:flex; align-items:center; gap:8px; margin:0; padding:0; min-height:auto;">                                            <input type="hidden" name="pph_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                           id="pph_enabled" name="pph_enabled" value="1"
                                           style="cursor:pointer; width:40px; height:22px; margin:0;"
                                           {{ $additionalDetail && $additionalDetail->pph_enabled ? 'checked' : '' }}
                                           onchange="toggleComponent('pph')">
                                    <span id="pph_status_label" style="font-size:12px; font-weight:600; {{ $additionalDetail && $additionalDetail->pph_enabled ? 'color:#10b981;' : 'color:#94a3b8;' }}">
                                        {{ $additionalDetail && $additionalDetail->pph_enabled ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="pph_percent" id="pph_percent"
                                       class="form-input" style="width:80px; padding:6px 10px; text-align:center;"
                                       value="{{ $additionalDetail?->pph_percent ?? 2 }}"
                                       min="0" max="100" step="0.01"
                                       {{ $additionalDetail && $additionalDetail->pph_enabled ? '' : 'disabled' }}
                                       oninput="hitungRincian()">
                            </td>
                            <td>
                                <span id="pph_nominal" style="font-weight:600; color:#0f766e;">Rp 0</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:12px 20px; text-align:right; border-top:1px solid #e2e8f0;">
                <button type="submit" class="btn btn-primary" style="padding:8px 20px; font-size:13px;">
                    <i class="fas fa-save"></i> Simpan Rincian
                </button>
            </div>
        </form>
    </div>

    {{-- Right: Ringkasan Total Card --}}
    <div class="card" style="margin-bottom:0; align-self:start;">
        <div class="card-header">
            <span class="card-title">Ringkasan Total</span>
        </div>
        <div style="padding:16px 20px;">
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;">
                <span style="color:#64748b;">Subtotal Vendor</span>
                <span id="ringkasan_subtotal" style="font-weight:600; color:#0f172a;">Rp {{ number_format($rabItems->sum('subtotal_biaya'), 0, ',', '.') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;">
                <span style="color:#64748b;">Fee EO</span>
                <span id="ringkasan_fee" style="font-weight:500;">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:2px solid #cbd5e1; font-size:14px; font-weight:600;">
                <span style="color:#0f172a;">DPP</span>
                <span id="ringkasan_dpp" style="color:#0f172a;">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px;">
                <span style="color:#64748b;">PPN</span>
                <span id="ringkasan_ppn" style="font-weight:500; color:#16a34a;">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:2px solid #0f172a; font-size:14px;">
                <span style="color:#64748b;">PPh</span>
                <span id="ringkasan_pph" style="font-weight:500; color:#dc2626;">Rp 0</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:12px 0 0 0; font-size:16px;">
                <span style="font-weight:700; color:#0f172a;">TOTAL DIBAYAR KLIEN</span>
                <span id="ringkasan_total" style="font-weight:800; font-size:18px; color:#0f766e;">Rp 0</span>
            </div>
        </div>
    </div>
</div>

{{-- Modal Add/Edit RAB --}}
<div class="modal-overlay" id="rabModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="rabModalTitle">Tambah Item RAB</h3>
            <button type="button" class="modal-close" onclick="closeRabModal()">&times;</button>
        </div>
        <form id="rabForm" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
            <input type="hidden" name="_method" id="rabFormMethod" value="POST">
            <div class="modal-body" style="grid-template-columns:1fr 1fr;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nama Biaya *</label>
                    <input type="text" name="nama_biaya" id="nama_biaya" class="form-input" placeholder="Contoh: Sewa Gedung" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori Biaya</label>
                    <select name="kategori_biaya" id="kategori_biaya" class="form-input">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['Venue','Dekorasi','Catering','Hiburan','Dokumentasi','Transportasi','Peralatan','Lainnya'] as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vendor (Opsional)</label>
                    <select name="vendor_id" id="vendor_id" class="form-input">
                        <option value="">-- Tidak ada vendor --</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan (Unit)</label>
                    <select name="satuan" id="satuan" class="form-input">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="Package">Package</option>
                        <option value="Unit">Unit</option>
                        <option value="pcs">pcs</option>
                        <option value="Set">Set</option>
                        <option value="Org">Org</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Item *</label>
                    <input type="number" name="jumlah_item" id="jumlah_item" class="form-input" min="1" value="1" required
                           oninput="hitungSubtotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp) *</label>
                    <input type="number" name="harga_satuan" id="harga_satuan" class="form-input" placeholder="0" required
                           oninput="hitungSubtotal()">
                </div>
                <div class="form-group" style="grid-column:1/-1; background:#f0fdf9; padding:12px; border-radius:8px;">
                    <label class="form-label" style="color:#0f766e;">Subtotal</label>
                    <div id="subtotalDisplay" style="font-size:18px; font-weight:700; color:#0f766e;">Rp 0</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeRabModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function changeEvent(id) {
    window.location.href = '{{ route("admin.rab.index") }}' + (id ? '?event_id=' + id : '');
}

function openAddRab() {
    document.getElementById('rabModalTitle').innerText = 'Tambah Item RAB';
    document.getElementById('rabForm').reset();
    document.getElementById('rabForm').action = '{{ route("admin.rab.store") }}';
    document.getElementById('rabFormMethod').value = 'POST';
    document.getElementById('subtotalDisplay').innerText = 'Rp 0';
    document.getElementById('rabModal').classList.add('show');
}

function openEditRab(item) {
    document.getElementById('rabModalTitle').innerText = 'Edit Item RAB';
    document.getElementById('nama_biaya').value = item.nama_biaya;
    document.getElementById('kategori_biaya').value = item.kategori_biaya ?? '';
    document.getElementById('vendor_id').value = item.vendor_id ?? '';
    document.getElementById('satuan').value = item.satuan ?? '';
    document.getElementById('jumlah_item').value = item.jumlah_item;
    document.getElementById('harga_satuan').value = item.harga_satuan;
    document.getElementById('rabForm').action = '{{ url("admin/rab") }}/' + item.id;
    document.getElementById('rabFormMethod').value = 'PUT';
    hitungSubtotal();
    document.getElementById('rabModal').classList.add('show');
}

function closeRabModal() {
    document.getElementById('rabModal').classList.remove('show');
}

function hitungSubtotal() {
    const qty = parseInt(document.getElementById('jumlah_item').value) || 0;
    const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
    const subtotal = qty * harga;
    document.getElementById('subtotalDisplay').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
}

// Rincian Tambahan Functions
var subtotalRAB = {{ $rabItems->sum('subtotal_biaya') }};

function toggleComponent(type) {
    var enabled = document.getElementById(type + '_enabled').checked;
    var percentInput = document.getElementById(type + '_percent');
    var statusLabel = document.getElementById(type + '_status_label');

    percentInput.disabled = !enabled;

    if (enabled) {
        statusLabel.innerText = 'AKTIF';
        statusLabel.style.color = '#10b981';
    } else {
        statusLabel.innerText = 'NONAKTIF';
        statusLabel.style.color = '#94a3b8';
    }

    hitungRincian();
}

function hitungRincian() {
    var fee_enabled = document.getElementById('fee_enabled').checked;
    var ppn_enabled = document.getElementById('ppn_enabled').checked;
    var pph_enabled = document.getElementById('pph_enabled').checked;

    var fee_pct = parseFloat(document.getElementById('fee_percent').value) || 0;
    var ppn_pct = parseFloat(document.getElementById('ppn_percent').value) || 0;
    var pph_pct = parseFloat(document.getElementById('pph_percent').value) || 0;

    // Fee EO berdasarkan subtotal vendor
    var fee_nominal = fee_enabled ? (subtotalRAB * fee_pct / 100) : 0;

    // DPP = Subtotal Vendor + Fee EO
    var dpp = subtotalRAB + fee_nominal;

    // PPN dan PPh dihitung dari DPP
    var ppn_nominal = ppn_enabled ? (dpp * ppn_pct / 100) : 0;
    var pph_nominal = pph_enabled ? (dpp * pph_pct / 100) : 0;

    // Nominal per komponen (tabel kiri)
    document.getElementById('fee_nominal').innerText = 'Rp ' + fee_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('ppn_nominal').innerText = 'Rp ' + ppn_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('pph_nominal').innerText = 'Rp ' + pph_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});

    // Ringkasan Total (kanan)
    document.getElementById('ringkasan_fee').innerText = 'Rp ' + fee_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
    if (fee_enabled) {
        document.getElementById('ringkasan_fee').style.color = '#16a34a';
    } else {
        document.getElementById('ringkasan_fee').style.color = '#94a3b8';
    }

    document.getElementById('ringkasan_dpp').innerText = 'Rp ' + dpp.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});

    document.getElementById('ringkasan_ppn').innerText = 'Rp ' + ppn_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('ringkasan_pph').innerText = 'Rp ' + pph_nominal.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});

    // Total = DPP + PPN - PPh
    var total = dpp + ppn_nominal - pph_nominal;
    document.getElementById('ringkasan_total').innerText = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    hitungRincian();
});
</script>
@endpush