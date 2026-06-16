<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVendor extends Model
{
    protected $table = 'event_vendor';

    protected $fillable = [
        'event_id',
        'vendor_id',
        'jadwal_vendor',
        'status_vendor',
        'harga_vendor',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}