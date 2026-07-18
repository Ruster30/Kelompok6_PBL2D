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
    <div class="page-header" style="margin-bottom:18px;">
        <h2 style="font-size:17px; font-weight:700; color:#0f172a;">Daftar Portfolio</h2>
        <button class="btn btn-primary" onclick="openPortfolioModal()">
            <i class="fas fa-plus"></i> Tambah Portfolio
        </button>
    </div>

    <div class="portfolio-grid">
        @forelse($portfolios as $portfolio)
        <div class="portfolio-card">
            <div class="portfolio-img-wrap">
                @if($portfolio->gambar)
                <img src="{{ asset('images/landing/portofolio/'.$portfolio->gambar) }}" alt="{{ $portfolio->judul }}" class="portfolio-img">
                @else
                <div class="portfolio-img" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-image" style="font-size:32px; color:#cbd5e1;"></i>
                </div>
                @endif
                <div class="portfolio-img-actions">
                    <button class="action-btn" title="Edit"
                            onclick='openPortfolioModal({{ json_encode($portfolio) }})'
                            style="background:white;">
                        <i class="fas fa-edit" style="font-size:12px;"></i>
                    </button>
                    <form action="{{ route('admin.cms.destroyPortfolio', $portfolio->id) }}" method="POST" style="display:inline;"
                          onsubmit="return swalDelete(this, {text: 'Portfolio {{ addslashes($portfolio->judul) }} akan dihapus dari landing page.'})">
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
                @if($portfolio->event)
                <div style="font-size:12px; color:#14b8a6; margin-top:4px;">
                    <i class="fas fa-link" style="font-size:11px;"></i> {{ $portfolio->event->nama_event }}
                </div>
                @endif
                @if($portfolio->tanggal_event)
                <div style="font-size:12px; color:#94a3b8; margin-top:2px;">
                    {{ \Carbon\Carbon::parse($portfolio->tanggal_event)->format('d M Y') }}
                </div>
                @endif
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
    <div class="modal-box" style="width:620px;">
        <div class="modal-header">
            <span id="portfolioModalTitle">Tambah Portfolio</span>
            <button class="modal-close" onclick="closePortfolioModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="portfolioForm" action="{{ route('admin.cms.storePortfolio') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="portfolioFormMethod" value="POST">
            <div class="modal-body form-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Judul *</label>
                    <input type="text" name="judul" id="judul" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="kategori" id="kategori" class="form-input" required>
                        @foreach(['Wedding','Corporate','Concert','Seminar','Launching','Expo','Lainnya'] as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Terhubung ke Event</label>
                    <select name="event_id" id="event_id" class="form-input">
                        <option value="">-- Tidak terhubung --</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Event</label>
                    <input type="date" name="tanggal_event" id="tanggal_event" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Gambar <span id="gambarRequired">*</span></label>
                    <input type="file" name="gambar" id="gambar" class="form-input" accept="image/*">
                    <img id="gambarPreview" src="" alt="" style="display:none; max-height:100px; border-radius:8px; margin-top:6px; object-fit:cover;">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="is_active" class="form-input">
                        <option value="1">Aktif (Tampil di Landing Page)</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closePortfolioModal()">Batal</button>
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
        document.getElementById('event_id').value = portfolio.event_id ?? '';
        document.getElementById('tanggal_event').value = portfolio.tanggal_event ?? '';
        document.getElementById('deskripsi').value = portfolio.deskripsi ?? '';
        document.getElementById('is_active').value = portfolio.is_active ? '1' : '0';
        document.getElementById('gambarRequired').innerText = '';
        if (portfolio.gambar) {
            preview.src='{{ asset("images/landing/portofolio") }}/'+portfolio.gambar;
            preview.style.display = 'block';
        }
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

function closePortfolioModal() {
    document.getElementById('portfolioModal').classList.remove('show');
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


