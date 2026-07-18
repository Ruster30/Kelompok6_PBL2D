<div {{ \->merge(['class' => 'empty-state']) }}>
    @if(isset(\))
    <div class="empty-state-icon">
        <i class="{{ \ }}"></i>
    </div>
    @else
    <div class="empty-state-icon">
        <i class="bi bi-inbox"></i>
    </div>
    @endif
    <h3 class="empty-state-title">{{ \ ?? 'Belum Ada Data' }}</h3>
    <p class="empty-state-text">{{ \ ?? 'Belum ada data yang tersedia pada halaman ini.' }}</p>
    @if(isset(\) && isset(\))
    <a href="{{ \ }}" class="btn btn-primary" style="margin-top:16px; padding:10px 24px; border-radius:10px;">
        <i class="bi bi-plus-lg"></i> {{ \ }}
    </a>
    @endif
    {{ \ }}
</div>
