@extends('layouts.admin')

@section('title', 'Landing Page CMS')
@section('page-title', 'Landing Page CMS')

@section('content')
<div class="page-header mb-4">
    <div class="page-header-left">
        <h1>Landing Page CMS</h1>
        <p>Kelola konten landing page secara dinamis. Perubahan langsung tampil di halaman publik.</p>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.cms.index') }}" class="tab-link active">Layanan</a>
    <a href="{{ route('admin.cms.portfolio') }}" class="tab-link">Portfolio</a>
    <a href="{{ route('admin.cms.team') }}" class="tab-link">Tim</a>
    <a href="{{ route('admin.cms.clients') }}" class="tab-link">Logo Klien</a>
</div>

<div class="tab-content">
    <div class="page-header mb-4">
        <h2 class="text-lg font-bold text-dark">Daftar Layanan</h2>
        <button class="btn btn-primary" onclick="openServiceModal()">
            <i class="fas fa-plus"></i> Tambah Layanan
        </button>
    </div>

    <div class="cms-grid">
        @forelse($services as $service)
        <div class="cms-card">
            <div class="cms-card-actions">
                <button class="action-btn" title="Edit" onclick='openServiceModal({{ json_encode($service) }})'>
                    <i class="fas fa-edit" style="font-size:12px;"></i>
                </button>
                <form action="{{ route('admin.cms.destroyService', $service->id) }}" method="POST" style="display:inline;"
                      onsubmit="return swalDelete(this, {text: 'Layanan {{ addslashes($service->nama_layanan) }} akan dihapus dari landing page.'})">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn danger" title="Hapus">
                        <i class="fas fa-trash" style="font-size:12px;"></i>
                    </button>
                </form>
            </div>
            <div class="cms-icon-circle">
                <i class="{{ $service->icon ?? 'fas fa-star' }}"></i>
            </div>
            <h3>{{ $service->nama_layanan }}</h3>
            <p>{{ $service->deskripsi }}</p>
            <div class="mt-3 flex items-center gap-3">
                <span class="badge {{ $service->is_active ? 'badge-active' : 'badge-gray' }}">
                    {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                <span class="text-sm text-muted">Urutan: {{ $service->urutan }}</span>
            </div>
        </div>
        @empty
        <div class="empty-state full-width">
            <i class="fas fa-briefcase"></i>
            <h3>Belum ada layanan</h3>
            <p>Tambahkan layanan untuk ditampilkan di landing page.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal --}}
<div id="serviceModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="serviceModalTitle">Tambah Layanan</span>
            <button class="modal-close" onclick="document.getElementById('serviceModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="serviceForm" action="{{ route('admin.cms.storeService') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="serviceFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Layanan *</label>
                    <input type="text" name="nama_layanan" id="nama_layanan" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Icon (Font Awesome class) *</label>
                    <input type="text" name="icon" id="icon" class="form-input" placeholder="contoh: fas fa-heart" required>
                    <span class="text-sm text-muted">Lihat referensi di fontawesome.com/icons</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi *</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="3" required></textarea>
                </div>
                <div class="form-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" id="urutan" class="form-input" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" id="is_active" class="form-input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('serviceModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openServiceModal(service = null) {
    const form = document.getElementById('serviceForm');
    if (service) {
        document.getElementById('serviceModalTitle').innerText = 'Edit Layanan';
        document.getElementById('nama_layanan').value = service.nama_layanan;
        document.getElementById('icon').value = service.icon;
        document.getElementById('deskripsi').value = service.deskripsi;
        document.getElementById('urutan').value = service.urutan;
        document.getElementById('is_active').value = service.is_active ? '1' : '0';
        form.action = '{{ url("admin/cms/services") }}/' + service.id;
        document.getElementById('serviceFormMethod').value = 'PUT';
    } else {
        document.getElementById('serviceModalTitle').innerText = 'Tambah Layanan';
        form.reset();
        form.action = '{{ route("admin.cms.storeService") }}';
        document.getElementById('serviceFormMethod').value = 'POST';
    }
    document.getElementById('serviceModal').classList.add('show');
}
</script>
@endpush

