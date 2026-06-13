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
    <a href="{{ route('admin.cms.portfolio') }}" class="tab-link active">Portfolio</a>
    <a href="{{ route('admin.cms.team') }}" class="tab-link">Tim</a>
    <a href="{{ route('admin.cms.clients') }}" class="tab-link">Logo Klien</a>
</div>

<div class="tab-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h2 style="font-size:17px; font-weight:700; color:#0f172a;">Daftar Portfolio</h2>
        <button class="btn btn-primary" onclick="openPortfolioModal()">
            <i class="fas fa-plus"></i> Tambah Portfolio
        </button>
    </div>

    <div class="portfolio-grid">
        @forelse($portfolios as $portfolio)
        <div class="portfolio-card">
            <div class="portfolio-img-wrap">
                <img src="{{ asset('storage/' . $portfolio->gambar) }}" alt="{{ $portfolio->judul }}" class="portfolio-img">
                <div class="portfolio-img-actions">
                    <button class="action-btn" title="Edit"
                            onclick='openPortfolioModal({{ json_encode($portfolio) }})'
                            style="background:white;">
                        <i class="fas fa-edit" style="font-size:12px;"></i>
                    </button>
                    <form action="{{ route('admin.cms.portfolio.destroy', $portfolio->id) }}" method="POST" style="display:inline;"
                          onsubmit="return confirm('Hapus portfolio ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn danger" title="Hapus" style="background:white;">
                            <i class="fas fa-trash" style="font-size:12px;"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="portfolio-body">
                <div class="portfolio-category">{{ $portfolio->kategori }}</div>
                <div class="portfolio-title">{{ $portfolio->judul }}</div>
                <div style="margin-top:8px;">
                    <span class="badge {{ $portfolio->is_active ? 'badge-active' : 'badge-gray' }}">
                        {{ $portfolio->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="fas fa-images"></i>
            <h3>Belum ada portfolio</h3>
            <p>Tambahkan portfolio untuk ditampilkan di landing page.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal --}}
<div id="portfolioModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="portfolioModalTitle">Tambah Portfolio</span>
            <button class="modal-close" onclick="document.getElementById('portfolioModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="portfolioForm" action="{{ route('admin.cms.storePortfolio') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="portfolioFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul *</label>
                    <input type="text" name="judul" id="judul" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="kategori" id="kategori" class="form-input" required>
                        <option value="Korporat">Korporat</option>
                        <option value="Pernikahan">Pernikahan</option>
                        <option value="Konser">Konser</option>
                        <option value="Ulang Tahun">Ulang Tahun</option>
                        <option value="Konferensi">Konferensi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gambar <span id="gambarRequired">*</span></label>
                    <input type="file" name="gambar" id="gambar" class="form-input" accept="image/*">
                    <img id="gambarPreview" src="" alt="" style="display:none; max-height:120px; border-radius:8px; margin-top:6px;">
                </div>
                <div class="form-group">
                    <label class="form-label">File Tips (opsional)</label>
                    <input type="file" name="tips_file" id="tips_file" class="form-input">
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label for="is_active">Tampilkan di landing page (Aktif)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('portfolioModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPortfolioModal(portfolio = null) {
    const form = document.getElementById('portfolioForm');
    const preview = document.getElementById('gambarPreview');
    form.reset();
    preview.style.display = 'none';

    if (portfolio) {
        document.getElementById('portfolioModalTitle').innerText = 'Edit Portfolio';
        document.getElementById('judul').value = portfolio.judul;
        document.getElementById('kategori').value = portfolio.kategori;
        document.getElementById('is_active').checked = !!portfolio.is_active;
        document.getElementById('gambarRequired').innerText = '';
        preview.src = '{{ asset("storage") }}/' + portfolio.gambar;
        preview.style.display = 'block';
        form.action = '{{ url("admin/cms/portfolio") }}/' + portfolio.id;
        document.getElementById('portfolioFormMethod').value = 'PUT';
    } else {
        document.getElementById('portfolioModalTitle').innerText = 'Tambah Portfolio';
        document.getElementById('gambarRequired').innerText = '*';
        form.action = '{{ route("admin.cms.storePortfolio") }}';
        document.getElementById('portfolioFormMethod').value = 'POST';
    }
    document.getElementById('portfolioModal').classList.add('show');
}

document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('gambarPreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endpush