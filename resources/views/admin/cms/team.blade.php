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
    <a href="{{ route('admin.cms.team') }}" class="tab-link active">Tim</a>
    <a href="{{ route('admin.cms.clients') }}" class="tab-link">Logo Klien</a>
</div>

<div class="tab-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h2 style="font-size:17px; font-weight:700; color:#0f172a;">Anggota Tim</h2>
        <button class="btn btn-primary" onclick="openTeamModal()">
            <i class="fas fa-plus"></i> Tambah Anggota
        </button>
    </div>

    <div class="team-grid">
        @forelse($teams as $team)
        <div class="team-card">
            <div class="team-photo-wrap">
                <img src="{{ asset('images/landing/team/'.$team->foto) }}" alt="{{ $team->nama }}" class="team-photo">
                <div class="team-photo-actions">
                    <button class="action-btn" title="Edit"
                            onclick='openTeamModal({{ json_encode($team) }})'
                            style="background:white;">
                        <i class="fas fa-edit" style="font-size:12px;"></i>
                    </button>
                    <form action="{{ route('admin.cms.destroyTeam', $team->id) }}" method="POST" style="display:inline;"
                          onsubmit="return swalDelete(this, {text: 'Anggota tim {{ addslashes($team->nama) }} akan dihapus dari landing page.'})">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn danger" title="Hapus" style="background:white;">
                            <i class="fas fa-trash" style="font-size:12px;"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="team-body">
                <div class="team-name">{{ $team->nama }}</div>
                <div class="team-role">{{ $team->jabatan }}</div>
                @if($team->deskripsi)
                <div class="team-desc">{{ $team->deskripsi }}</div>
                @endif
                <div style="margin-top:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span class="badge {{ $team->is_active ? 'badge-active' : 'badge-gray' }}">
                        {{ $team->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span style="font-size:12px; color:#94a3b8;">Urutan: {{ $team->urutan }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="fas fa-users"></i>
            <h3>Belum ada anggota tim</h3>
            <p>Tambahkan anggota tim untuk ditampilkan di landing page.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal --}}
<div id="teamModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="teamModalTitle">Tambah Anggota</span>
            <button class="modal-close" onclick="document.getElementById('teamModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="teamForm" action="{{ route('admin.cms.storeTeam') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="teamFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama *</label>
                    <input type="text" name="nama" id="nama" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan *</label>
                    <input type="text" name="jabatan" id="jabatan" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto <span id="fotoRequired">*</span></label>
                    <input type="file" name="foto" id="foto" class="form-input" accept="image/*">
                    <img id="fotoPreview" src="" alt="" style="display:none; max-height:120px; border-radius:8px; margin-top:6px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="2"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
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
                <button type="button" class="btn btn-outline" onclick="document.getElementById('teamModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openTeamModal(team = null) {
    const form = document.getElementById('teamForm');
    const preview = document.getElementById('fotoPreview');
    form.reset();
    preview.style.display = 'none';

    if (team) {
        document.getElementById('teamModalTitle').innerText = 'Edit Anggota';
        document.getElementById('nama').value = team.nama;
        document.getElementById('jabatan').value = team.jabatan;
        document.getElementById('deskripsi').value = team.deskripsi ?? '';
        document.getElementById('urutan').value = team.urutan;
        document.getElementById('is_active').value = team.is_active ? '1' : '0';
        document.getElementById('fotoRequired').innerText = '';
        preview.src = '{{ asset("images/landing/team") }}/' + team.foto;
        preview.style.display = 'block';
        form.action = '{{ url("admin/cms/team") }}/' + team.id;
        document.getElementById('teamFormMethod').value = 'PUT';
    } else {
        document.getElementById('teamModalTitle').innerText = 'Tambah Anggota';
        document.getElementById('fotoRequired').innerText = '*';
        form.action = '{{ route("admin.cms.storeTeam") }}';
        document.getElementById('teamFormMethod').value = 'POST';
    }
    document.getElementById('teamModal').classList.add('show');
}

document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('fotoPreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endpush