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
        'satuan',
        'harga_satuan',
        'subtotal_biaya',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan'   => 'decimal:2',
            'subtotal_biaya' => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /** Vendor terkait item RAB ini (opsional) */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // ─── Accessors ───────────────────────────────────────────

    /** Harga satuan diformat sebagai currency Rupiah */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    /** Subtotal diformat sebagai currency Rupiah */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal_biaya, 0, ',', '.');
    }
}
