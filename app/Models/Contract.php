<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nomor_kontrak',
        'file_kontrak',
        'tanggal_kontrak',
        'status_kontrak',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kontrak' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────

    /** URL file kontrak (null jika belum ada file) */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_kontrak ? asset('storage/' . $this->file_kontrak) : null;
    }

    /** CSS class badge sesuai status kontrak */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_kontrak) {
            'aktif'      => 'badge-aktif',
            'selesai'    => 'badge-selesai',
            'dibatalkan' => 'badge-ditolak',
            default      => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_kontrak) {
            'draft'      => 'Draft',
            'aktif'      => 'Aktif',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default      => ucfirst($this->status_kontrak ?? ''),
        };
    }
}
