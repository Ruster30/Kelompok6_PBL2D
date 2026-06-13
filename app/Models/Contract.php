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

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
