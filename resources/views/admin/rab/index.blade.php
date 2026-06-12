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
                {{ $event->name }}
            </option>
            @endforeach
        </select>
        @if($selectedEvent)
        <button class="btn btn-primary" onclick="document.getElementById('addItemModal').style.display='flex'">
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
{{-- Summary cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-list stat-icon"></i><span class="stat-badge">Items</span></div>
        <div class="stat-value">{{ $rabItems->count() }}</div>
        <div class="stat-label">Total Item RAB</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-calculator stat-icon"></i><span class="stat-badge green">Total</span></div>
        <div class="stat-value" style="font-size:18px;">Rp {{ number_format($rabItems->sum('total_price'), 0, ',', '.') }}</div>
        <div class="stat-label">Total Anggaran</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-wallet stat-icon"></i><span class="stat-badge blue">Budget</span></div>
        <div class="stat-value" style="font-size:18px;">Rp {{ number_format($selectedEvent->budget ?? 0, 0, ',', '.') }}</div>
        <div class="stat-label">Budget Event</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Detail RAB — {{ $selectedEvent->name }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rabItems as $i => $item)
            <tr>
                <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="font-weight:500;">{{ $item->item_name }}</td>
                <td>{{ $item->category ?? '-' }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->unit ?? '-' }}</td>
                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="font-weight:600;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                <td style="color:#64748b;">{{ $item->notes ?? '-' }}</td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn" onclick="editItem({{ $item->id }})" title="Edit">
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
                <td style="padding:14px 24px; color:#0f766e;">Rp {{ number_format($rabItems->sum('total_price'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Add Item Modal --}}
<div id="addItemModal" style="display:none; position:fixed; inset:0; background:#00000060; z-index:200; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:560px; max-width:95vw;">
        <div style="padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:16px; font-weight:600;">Tambah Item RAB</span>
            <button onclick="document.getElementById('addItemModal').style.display='none'"
                    style="background:none; border:none; cursor:pointer; color:#64748b; font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.rab.store') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
            <div style="padding:24px; display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nama Item *</label>
                    <input type="text" name="item_name" class="form-input" placeholder="Contoh: Sewa Gedung" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach(['Venue','Dekorasi','Catering','Hiburan','Dokumentasi','Transportasi','Peralatan','Lainnya'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="unit" class="form-input" placeholder="unit/hari/buah">
                </div>
                <div class="form-group">
                    <label class="form-label">Qty *</label>
                    <input type="number" name="qty" class="form-input" min="1" value="1" required id="qtyInput">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp) *</label>
                    <input type="number" name="unit_price" class="form-input" placeholder="0" required id="priceInput">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="notes" class="form-input" placeholder="Opsional">
                </div>
            </div>
            <div style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline"
                        onclick="document.getElementById('addItemModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-label { font-size:13px; font-weight:600; color:#374151; }
.form-input {
    padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px;
    color:#334155; outline:none; font-family:inherit; background:white; width:100%;
}
.form-input:focus { border-color:#14b8a6; box-shadow:0 0 0 3px #ccfbf180; }
</style>
@endpush

@push('scripts')
<script>
function changeEvent(id) {
    window.location.href = '{{ route("admin.rab.index") }}' + (id ? '?event_id=' + id : '');
}
</script>
@endpush
