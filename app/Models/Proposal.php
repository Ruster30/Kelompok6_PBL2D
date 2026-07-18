<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nomor_proposal',
        'file_proposal',
        'versi',
        'status',
        'tanggal_proposal',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_proposal' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────

    /** CSS class badge sesuai status proposal */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu_konfirmasi' => 'badge-info',
            'negosiasi' => 'badge-warning',
            'direvisi' => 'badge-secondary',
            'diterima' => 'badge-success',
            'ditolak' => 'badge-danger',
            default => 'badge-light',

        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',

            'negosiasi' => 'Negosiasi Diajukan',

            'direvisi' => 'Penawaran Direvisi',

            'diterima' => 'Diterima',

            'ditolak' => 'Ditolak',

            default => ucfirst(str_replace('_', ' ', $this->status)),

        };
    }

    public function isWaiting(): bool
    {
        return $this->status === 'menunggu_konfirmasi';
    }

    public function isNegotiation(): bool
    {
        return $this->status === 'negosiasi';
    }

    public function isRevision(): bool
    {
        return $this->status === 'direvisi';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'diterima';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /** URL file proposal (null jika belum ada file) */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_proposal ? asset('storage/' . $this->file_proposal) : null;
    }
}