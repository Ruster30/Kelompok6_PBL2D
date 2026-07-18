<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabAdditionalDetail extends Model
{
    use HasFactory;

    protected $table = 'rab_additional_details';

    protected $fillable = [
        'event_id',
        'fee_enabled',
        'fee_percent',
        'ppn_enabled',
        'ppn_percent',
        'pph_enabled',
        'pph_percent',
    ];

    protected function casts(): array
    {
        return [
            'fee_enabled'  => 'boolean',
            'fee_percent'  => 'decimal:2',
            'ppn_enabled'  => 'boolean',
            'ppn_percent'  => 'decimal:2',
            'pph_enabled'  => 'boolean',
            'pph_percent'  => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}