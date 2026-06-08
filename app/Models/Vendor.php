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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vendor')
                    ->withPivot(['jadwal_vendor', 'status_vendor', 'harga_vendor'])
                    ->withTimestamps();
    }

    public function rabs()
    {
        return $this->hasMany(Rab::class, 'vendor_id');
    }
}
