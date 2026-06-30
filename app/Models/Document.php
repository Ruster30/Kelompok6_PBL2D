<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'nama_file',
        'file_path',
        'tipe',
    ];

    // Nilai tipe yang valid
    public const TIPE_PROPOSAL  = 'proposal';
    public const TIPE_KONTRAK   = 'kontrak';
    public const TIPE_INVOICE   = 'invoice';
    public const TIPE_RAB       = 'rab';
    public const TIPE_LAPORAN   = 'laporan'; 
    public const TIPE_LAINNYA   = 'lainnya';

    // Daftar tipe valid untuk validasi form
    public const TIPE_OPTIONS = [
        'proposal' => 'Proposal',
        'kontrak'  => 'Kontrak',
        'invoice'  => 'Invoice',
        'rab'      => 'RAB',
        'laporan'  => 'Laporan Akhir', 
        'lainnya'  => 'Lainnya',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'proposal' => 'Proposal',
            'kontrak'  => 'Kontrak',
            'invoice'  => 'Invoice',
            'rab'      => 'RAB',
            'laporan'  => 'Laporan Akhir',   // [TAMBAH]
            default    => 'Lainnya',
        };
    }

    public function getTipeBadgeClassAttribute(): string
    {
        return match ($this->tipe) {
            'proposal' => 'badge-mendatang',
            'kontrak'  => 'badge-aktif',
            'invoice'  => 'badge-selesai',
            'rab'      => 'badge-pending',
            'laporan'  => 'badge-purple',   // [TAMBAH]
            default    => 'badge-pending',
        };
    }

    public function sends()
    {
        return $this->hasMany(DocumentSend::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }
}