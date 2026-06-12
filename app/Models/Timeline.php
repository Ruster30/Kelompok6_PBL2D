<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'status_kegiatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isDone(): bool
    {
        return $this->status_kegiatan === 'selesai';
    }

    public function isBerjalan(): bool
    {
        return $this->status_kegiatan === 'berjalan';
    }

    /** CSS class badge sesuai status kegiatan */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_kegiatan) {
            'selesai'     => 'badge-aktif',
            'berjalan'    => 'badge-mendatang',
            'belum_mulai' => 'badge-pending',
            default       => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_kegiatan) {
            'belum_mulai' => 'Belum Mulai',
            'berjalan'    => 'Berjalan',
            'selesai'     => 'Selesai',
            default       => ucfirst($this->status_kegiatan ?? ''),
        };
    }
}
