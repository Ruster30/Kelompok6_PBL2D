@extends('layouts.admin')

@section('title', 'Landing Page CMS')
@section('page-title', 'Landing Page CMS')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Landing Page CMS</h1>
        <p>Kelola konten landing page secara dinamis. Perubahan langsung tampil di halaman publik.</p>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.cms.index') }}" class="tab-link">Layanan</a>
    <a href="{{ route('admin.cms.portfolio') }}" class="tab-link">Portfolio</a>
    <a href="{{ route('admin.cms.team') }}" class="tab-link">Tim</a>
    <a href="{{ route('admin.cms.clients') }}" class="tab-link active">Logo Klien</a>
</div>

<div class="tab-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h2 style="font-size:17px; font-weight:700; color:#0f172a;">Logo Klien &amp; Partner</h2>
        <button class="btn btn-primary" onclick="openClientModal()">
            <i class="fas fa-plus"></i> Tambah Logo
        </button>
    </div>

    <div class="logo-grid">
        @forelse($clients as $client)
        <div class="logo-card">
            <div class="logo-card-actions">
                <button class="action-btn" title="Edit" onclick='openClientModal({{ json_encode($client) }})'>
                    <i class="fas fa-edit" style="font-size:12px;"></i>
                </button>
                <form action="{{ route('admin.cms.destroyClient', $client->id) }}" method="POST" style="display:inline;"
                      onsubmit="return swalDelete(this, {text: 'Logo {{ addslashes($client->nama_client) }} akan dihapus dari landing page.'})">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn danger" title="Hapus">
                        <i class="fas fa-trash" style="font-size:12px;"></i>
                    </button>
                </form>
            </div>
            <img src="{{ asset('images/landing/clients/' . $client->logo) }}" alt="{{ $client->nama_client }}">
            <div class="logo-card-name">{{ $client->nama_client }}</div>
            <span class="badge {{ $client->is_active ? 'badge-active' : 'badge-gray' }}" style="font-size:11px;">
                {{ $client->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="fas fa-building"></i>
            <h3>Belum ada logo klien</h3>
            <p>Tambahkan logo klien/partner untuk ditampilkan di landing page.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal --}}
<div id="clientModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="clientModalTitle">Tambah Logo Klien</span>
            <button class="modal-close" onclick="document.getElementById('clientModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="clientForm" action="{{ route('admin.cms.storeClient') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="clientFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Client/Partner *</label>
                    <input type="text" name="nama_client" id="nama_client" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Logo <span id="logoRequired">*</span></label>
                    <input type="file" name="logo" id="logo" class="form-input" accept="image/*">
                    <img id="logoPreview" src="" alt="" style="display:none; max-height:80px; border-radius:8px; margin-top:6px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" id="website" class="form-input" placeholder="https://...">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tampilkan</label>
                        <select name="is_active" id="is_active" class="form-input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('clientModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openClientModal(client = null) {
    const form = document.getElementById('clientForm');
    const preview = document.getElementById('logoPreview');
    form.reset();
    preview.style.display = 'none';

    if (client) {
        document.getElementById('clientModalTitle').innerText = 'Edit Logo Klien';
        document.getElementById('nama_client').value = client.nama_client;
        document.getElementById('website').value = client.website ?? '';
        document.getElementById('status').value = client.status ?? 'partner';
        document.getElementById('is_active').value = client.is_active ? '1' : '0';
        document.getElementById('logoRequired').innerText = '';
        preview.src = '{{ asset("images/landing/clients/") }}/' + client.logo;
        preview.style.display = 'block';
        form.action = '{{ url("admin/cms/clients") }}/' + client.id;
        document.getElementById('clientFormMethod').value = 'PUT';
    } else {
        document.getElementById('clientModalTitle').innerText = 'Tambah Logo Klien';
        document.getElementById('logoRequired').innerText = '*';
        form.action = '{{ route("admin.cms.storeClient") }}';
        document.getElementById('clientFormMethod').value = 'POST';
    }
    document.getElementById('clientModal').classList.add('show');
}

document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('logoPreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endpush