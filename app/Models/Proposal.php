<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nomor_proposal',
        'file_proposal',
        'versi',
        'status',
        'tanggal_proposal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_proposal' => 'date',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
