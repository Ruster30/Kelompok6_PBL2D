<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'file_laporan',
        'deskripsi',
        'tanggal_laporan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
