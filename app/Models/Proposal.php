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
        return match($this->status) {
            'disetujui' => 'badge-diterima',
            'ditolak'   => 'badge-ditolak',
            'diajukan'  => 'badge-mendatang',
            default     => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'diajukan'  => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    /** URL file proposal (null jika belum ada file) */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_proposal ? asset('storage/' . $this->file_proposal) : null;
    }
}
