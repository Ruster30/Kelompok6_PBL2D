<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_vendor',
        'jenis_vendor',
        'alamat',
        'deskripsi',
    ];

    // ─── Relasi ──────────────────────────────────────────────

    /** User (akun login) yang memiliki vendor profile ini */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Events yang menggunakan vendor ini */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vendor')
                    ->withPivot(['jadwal_vendor', 'status_vendor', 'harga_vendor'])
                    ->withTimestamps();
    }

    /** Item RAB yang menggunakan vendor ini */
    public function rabs()
    {
        return $this->hasMany(Rab::class, 'vendor_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────

    /** Total nilai kontrak vendor dari semua RAB */
    public function getTotalNilaiAttribute(): float
    {
        return (float) $this->rabs()->sum('subtotal_biaya');
    }

    /** Jumlah event aktif vendor ini */
    public function getJumlahEventAktifAttribute(): int
    {
        return $this->events()
                    ->whereIn('status_event', ['diproses', 'berjalan'])
                    ->count();
    }
}
