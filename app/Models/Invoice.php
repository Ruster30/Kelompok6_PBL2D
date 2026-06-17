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

    // ─── Relasi ──────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Payment yang terkait dengan invoice ini (melalui event_id).
     * Gunakan relasi ini untuk menampilkan riwayat pembayaran per invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────

    /** CSS class badge sesuai status invoice */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_invoice) {
            'lunas'    => 'badge-aktif',
            'terkirim' => 'badge-mendatang',
            'draft'    => 'badge-pending',
            default    => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_invoice) {
            'draft'    => 'Draft',
            'terkirim' => 'Terkirim',
            'lunas'    => 'Lunas',
            default    => ucfirst($this->status_invoice ?? ''),
        };
    }

    /** Total invoice diformat sebagai currency Rupiah */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_invoice, 0, ',', '.');
    }
}
