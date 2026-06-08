<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nominal',
        'tanggal_pembayaran',
        'status_pembayaran',
        'bukti_pembayaran',
        'jenis_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pembayaran' => 'date',
            'nominal'            => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
