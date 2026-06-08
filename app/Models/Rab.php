<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    use HasFactory;

    protected $table = 'rabs';

    protected $fillable = [
        'event_id',
        'vendor_id',
        'nama_biaya',
        'kategori_biaya',
        'jumlah_item',
        'harga_satuan',
        'subtotal_biaya',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan'    => 'decimal:2',
            'subtotal_biaya'  => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
