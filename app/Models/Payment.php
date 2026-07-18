<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{   
    use HasFactory;

    protected $fillable = [
        'invoice_id',
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

    // ─── Relasi ──────────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────

    /** CSS class badge sesuai status pembayaran */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_pembayaran) {
            'diverifikasi' => 'badge-aktif',
            'ditolak'      => 'badge-ditolak',
            'menunggu'     => 'badge-pending',
            default        => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_pembayaran) {
            'menunggu'     => 'Menunggu Verifikasi',
            'diverifikasi' => 'Verified',
            'ditolak'      => 'Ditolak',
            default        => ucfirst($this->status_pembayaran ?? ''),
        };
    }

    /** Nominal diformat sebagai currency Rupiah */
    public function getFormattedNominalAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /** URL bukti transfer (null jika belum diupload) */
    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_pembayaran ? asset('storage/' . $this->bukti_pembayaran) : null;
    }
}
