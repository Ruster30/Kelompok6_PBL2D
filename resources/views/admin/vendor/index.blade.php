@extends('layouts.admin')

@section('title', 'Vendor')
@section('page-title', 'Vendor')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Vendor</h1>
        <p>Master data vendor &amp; manajemen akun login.</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addVendorModal').classList.add('show')">
        <i class="fas fa-plus"></i> Tambah Vendor
    </button>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="plain-stat">
        <div class="plain-stat-label">Total Vendor</div>
        <div class="plain-stat-value">{{ $totalVendors }}</div>
    </div>
    <div class="plain-stat">
        <div class="plain-stat-label">Partner Aktif</div>
        <div class="plain-stat-value">{{ $activeVendors }}</div>
    </div>
    <div class="plain-stat">
        <div class="plain-stat-label">Sedang Bertugas</div>
        <div class="plain-stat-value">{{ $busyVendors }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="border-bottom:none; padding-bottom:14px;">
        <div class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari vendor..." value="{{ request('search') }}">
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Vendor</th>
                <th>Kategori</th>
                <th>Kontak</th>
                <th>Akun</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
            <tr>
                <td style="font-weight:500;">{{ $vendor->nama_vendor }}</td>
                <td>{{ $vendor->jenis_vendor ?? '-' }}</td>
                <td>{{ $vendor->email ?? $vendor->user->email ?? '-' }}</td>
                <td>
                    @if($vendor->user_id)
                        <span class="badge badge-done">Terhubung</span>
                    @else
                        <span class="badge badge-gray">Belum ada</span>
                    @endif
                </td>
                <td>
                    @if($vendor->rating)
                        <i class="fas fa-star" style="color:#fbbf24; font-size:12px;"></i> {{ number_format($vendor->rating, 1) }}
                    @else
                        <span style="color:#94a3b8;">-</span>
                    @endif
                </td>
                <td>
                    @php
                        $busy = ($vendor->active_jobs_count ?? 0) > 0;
                    @endphp
                    <span class="badge {{ $busy ? 'badge-active' : 'badge-done' }}">{{ $busy ? 'Bertugas' : 'Tersedia' }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn" title="Edit" onclick='editVendor({{ json_encode($vendor) }})'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalDelete(this, {text: 'Vendor {{ addslashes($vendor->nama_vendor) }} akan dihapus. Tindakan ini tidak dapat dibatalkan.'})">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="6">
                    <div class="empty-state" style="padding:40px 20px;">
                        <div class="empty-state-icon"><i class="bi bi-truck" style="font-size:40px;"></i></div>
                        <h3 class="empty-state-title">Belum ada vendor.</h3>
                        <p class="empty-state-text">Tambahkan vendor baru untuk mulai bekerja sama.</p>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>

    @if($vendors->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $vendors->links() }}
    </div>
    @endif
</div>

{{-- Add/Edit Vendor Modal --}}
<div id="addVendorModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="vendorModalTitle">Tambah Vendor</span>
            <button class="modal-close" onclick="document.getElementById('addVendorModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="vendorForm" action="{{ route('admin.vendors.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="vendorFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Vendor *</label>
                    <input type="text" name="nama_vendor" id="nama_vendor" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Vendor</label>
                    <input type="text" name="jenis_vendor" id="jenis_vendor" class="form-input" placeholder="Contoh: Catering, Dekorasi, dll">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="2"></textarea>
                </div>
                <hr style="border:none; border-top:1px solid #f1f5f9;">
                <p style="font-size:13px; color:#64748b; margin:-8px 0 0;">
                    Email dapat digunakan sebagai kontak vendor. Isi password bila ingin sekaligus membuat akun login vendor.
                </p>
                <div class="form-group">
                    <label class="form-label">Email Kontak</label>
                    <input type="email" name="email" id="vendor_email" class="form-input" placeholder="email@vendor.com">
                </div>
                <div class="form-group" id="passwordGroup">
                    <label class="form-label">Password Akun (opsional)</label>

                    <div style="position:relative;">
                        <input type="password"
                            name="password"
                            id="vendor_password"
                            class="form-input"
                            placeholder="Minimal 8 karakter">

                        <i class="bi bi-eye"
                        id="togglePassword"
                        style="
                            position:absolute;
                            right:14px;
                            top:50%;
                            transform:translateY(-50%);
                            cursor:pointer;
                            color:#94a3b8;
                            font-size:14px;">
                        </i>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addVendorModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));

document.getElementById('togglePassword').addEventListener('click', function () {
    const password = document.getElementById('vendor_password');

    if (password.type === 'password') {
        password.type = 'text';
        this.classList.remove('bi-eye');
        this.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        this.classList.remove('bi-eye-slash');
        this.classList.add('bi-eye');
    }
});

function filterTable() {
    const search = document.getElementById('searchInput').value;
    window.location.href = `{{ route('admin.vendors.index') }}?search=${encodeURIComponent(search)}`;
}
function debounce(fn, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}

function editVendor(vendor) {
    document.getElementById('vendorModalTitle').innerText = 'Edit Vendor';
    document.getElementById('nama_vendor').value = vendor.nama_vendor;
    document.getElementById('jenis_vendor').value = vendor.jenis_vendor ?? '';
    document.getElementById('alamat').value = vendor.alamat ?? '';
    document.getElementById('deskripsi').value = vendor.deskripsi ?? '';
    document.getElementById('vendor_email').value = vendor.email ?? vendor.user?.email ?? '';
    document.getElementById('passwordGroup').style.display = vendor.user_id ? 'none' : 'block';
    document.getElementById('vendorForm').action = '{{ url("admin/vendors") }}/' + vendor.id;
    document.getElementById('vendorFormMethod').value = 'PUT';
    document.getElementById('addVendorModal').classList.add('show');
}
</script>
@endpush
