<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Feedback;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'pic_admin_id',
        'nama_event',
        'jenis_event',
        'tanggal_event',
        'lokasi_event',
        'jumlah_tamu',
        'detail_kebutuhan',
        'status_event',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_event' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function picAdmin()
    {
        return $this->belongsTo(User::class, 'pic_admin_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'event_vendor')
                    ->withPivot(['jadwal_vendor', 'status_vendor', 'harga_vendor'])
                    ->withTimestamps();
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'event_id');
    }

    /** Proposal terakhir (versi terbaru) */
    public function latestProposal()
    {
        return $this->hasOne(Proposal::class, 'event_id')->latestOfMany();
    }

    /** Satu kontrak aktif per event */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'event_id');
    }

    /** Semua kontrak (jika ada revisi) */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'event_id');
    }

    public function rabs()
    {
        return $this->hasMany(Rab::class, 'event_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'event_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class, 'event_id', 'invoice_id', 'id', 'id');
    }

    public function timelines()
    {
        return $this->hasMany(Timeline::class, 'event_id')->orderBy('tanggal_kegiatan');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'event_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'event_id');
    }

    // ─── Computed Attributes ─────────────────────────────────

    /** Progress event berdasarkan % kegiatan timeline yang selesai */
    public function getProgressAttribute(): int
    {
        $total = $this->timelines()->count();
        if ($total === 0) return 0;
        $done = $this->timelines()->where('status_kegiatan', 'selesai')->count();
        return (int) round($done / $total * 100);
    }

    /** Total nominal pembayaran yang sudah diverifikasi */
    public function getTotalDibayarAttribute(): float
    {
        return (float) $this->payments()->where('status_pembayaran', 'diverifikasi')->sum('nominal');
    }

    /** Total nilai semua invoice */
    public function getTotalInvoiceAttribute(): float
    {
        return (float) $this->invoices()->sum('total_invoice');
    }

    /** Sisa tagihan yang belum dibayar */
    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->total_invoice - $this->total_dibayar);
    }

    /** Total anggaran dari RAB */
    public function getTotalRabAttribute(): float
    {
        return (float) $this->rabs()->sum('subtotal_biaya');
    }

    // ─── Helper Badge & Label ────────────────────────────────

    /** CSS class badge sesuai status event */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_event) {
            'berjalan'   => 'badge-aktif',
            'diproses'   => 'badge-mendatang',
            'menunggu'   => 'badge-pending',
            'selesai'    => 'badge-selesai',
            'dibatalkan' => 'badge-ditolak',
            default      => 'badge-pending',
        };
    }

    /** Label status dalam Bahasa Indonesia */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_event) {
            'menunggu'   => 'Menunggu',
            'diproses'   => 'Diproses',
            'berjalan'   => 'Berjalan',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default      => ucfirst($this->status_event),
        };
    }

    public function feedbacks()
    {

        return $this->hasMany(Feedback::class);

    }
}
