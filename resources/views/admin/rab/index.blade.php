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
        <div class="stat-value" style="font-size:18px;">Rp {{ number_format($rabItems->sum('subtotal_biaya'), 0, ',', '.') }}</div>
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
        <span class="card-title">Detail RAB — {{ $selectedEvent->nama_event }}</span>
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
                              onsubmit="return confirm('Hapus item ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="9">Belum ada item RAB. Klik "Tambah Item" untuk mulai.</td></tr>
            @endforelse
        </tbody>
        @if($rabItems->count())
        <tfoot>
            <tr style="background:#f8fafc; font-weight:700;">
                <td colspan="6" style="padding:14px 24px; color:#374151; text-align:right;">TOTAL</td>
                <td style="padding:14px 24px; color:#0f766e;">Rp {{ number_format($rabItems->sum('subtotal_biaya'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Add/Edit Modal --}}
<div id="rabModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="rabModalTitle">Tambah Item RAB</span>
            <button class="modal-close" onclick="closeRabModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="rabForm" action="{{ route('admin.rab.store') }}" method="POST">
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
</script>
@endpush