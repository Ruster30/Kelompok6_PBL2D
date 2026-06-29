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

    // Nilai tipe yang valid (sudah tidak pakai enum di DB setelah migrasi)
    public const TIPE_PROPOSAL  = 'proposal';
    public const TIPE_KONTRAK   = 'kontrak';
    public const TIPE_INVOICE   = 'invoice';
    public const TIPE_RAB       = 'rab';
    public const TIPE_LAINNYA   = 'lainnya';

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
            'kontrak'  => 'Surat Kontrak',
            'invoice'  => 'Invoice',
            'rab'      => 'RAB',
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