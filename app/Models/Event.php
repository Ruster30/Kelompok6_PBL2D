<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'pic_admin_id',
        'nama_event',
        'jenis_event',
        'tanggal_event',
        'lokasi_event',
        'jumlah_tamu',
        'detail_kebutuhan',
        'status_event',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_event' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function picAdmin()
    {
        return $this->belongsTo(User::class, 'pic_admin_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'event_vendor')
                    ->withPivot(['jadwal_vendor', 'status_vendor', 'harga_vendor'])
                    ->withTimestamps();
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'event_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'event_id');
    }

    public function rabs()
    {
        return $this->hasMany(Rab::class, 'event_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'event_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'event_id');
    }

    public function timelines()
    {
        return $this->hasMany(Timeline::class, 'event_id');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'event_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'event_id');
    }
}
