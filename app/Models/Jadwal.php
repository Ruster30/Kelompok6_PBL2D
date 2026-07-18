<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'event_id',
        'tanggal',
        'judul',
        'deskripsi',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}