<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nomor_invoice',
        'total_invoice',
        'status_invoice',
        'tanggal_invoice',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_invoice' => 'date',
            'total_invoice'   => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'event_id', 'event_id');
    }
}
