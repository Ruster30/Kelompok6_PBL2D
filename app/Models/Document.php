<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Document
 *
 * Root entity DDMS. Menyimpan metadata seluruh dokumen
 * (upload manual maupun hasil generate).
 *
 * @property int             $id
 * @property int|null        $event_id
 * @property int|null        $user_id
 * @property string          $nama_file
 * @property string          $file_path
 * @property string          $tipe
 * @property string          $status
 * @property string          $document_category
 * @property int             $current_version
 * @property int|null        $template_id
 * @property int|null        $file_size
 * @property string|null     $mime_type
 * @property int|null        $updated_by
 * @property bool            $is_archived
 * @property \Carbon\Carbon|null $archived_at
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read Event|null $event
 * @property-read User|null $user
 * @property-read DocumentTemplate|null $template
 * @property-read User|null $updatedBy
 * @property-read DocumentNumbering|null $numbering
 * @property-read \Illuminate\Database\Eloquent\Collection|DocumentApproval[] $approvals
 * @property-read DocumentApproval|null $latestApproval
 * @property-read DocumentQrVerification|null $qrVerification
 * @property-read \Illuminate\Database\Eloquent\Collection|DocumentSend[] $sends
 */
class Document extends Model
{
    use HasFactory;

    /*
     * Status dokumen
     */
    public const STATUS_DRAFT           = 'draft';
    public const STATUS_PENDING         = 'pending';
    public const STATUS_APPROVED        = 'approved';
    public const STATUS_REJECTED        = 'rejected';
    public const STATUS_PUBLISHED       = 'published';
    public const STATUS_ARCHIVED        = 'archived';

    /*
     * Kategori dokumen
     */
    public const CATEGORY_OFFICIAL = 'official';
    public const CATEGORY_GENERAL  = 'general';
    public const CATEGORY_INVOICE  = 'invoice';
    public const CATEGORY_RECEIPT  = 'receipt';

    /*
     * Tipe dokumen (untuk kolom tipe)
     */
    public const TIPE_PROPOSAL  = 'proposal';
    public const TIPE_KONTRAK   = 'kontrak';
    public const TIPE_INVOICE   = 'invoice';
    public const TIPE_RAB       = 'rab';
    public const TIPE_LAPORAN   = 'laporan';
    public const TIPE_KWITANSI  = 'kwitansi';
    public const TIPE_LAINNYA   = 'lainnya';

    public const TIPE_OPTIONS = [
        'proposal' => 'Proposal',
        'kontrak'  => 'Kontrak',
        'invoice'  => 'Invoice',
        'rab'      => 'RAB',
        'laporan'  => 'Laporan Akhir',
        'kwitansi' => 'Kwitansi',
        'lainnya'  => 'Lainnya',
    ];

    protected $fillable = [
        'event_id',
        'user_id',
        'nama_file',
        'file_path',
        'tipe',
        // DDMS Phase 1
        'status',
        'document_category',
        'current_version',
        'template_id',
        'file_size',
        'mime_type',
        'updated_by',
        'is_archived',
        'archived_at',
    ];

        protected function casts(): array
    {
        return [
            'status'            => DocumentStatus::class,
            'document_category' => DocumentCategory::class,
            'current_version'   => 'integer',
            'file_size'         => 'integer',
            'is_archived'       => 'boolean',
            'archived_at'       => 'datetime',
        ];
    }

    // ── Relasi ke Event Management ──────────────────────────

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(DocumentSend::class);
    }

    // ── Relasi ke DDMS ──────────────────────────────────────

    /** Template yang digunakan untuk generate dokumen ini */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /** User terakhir yang mengubah dokumen */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Nomor resmi dokumen (satu dokumen = satu nomor) */
    public function numbering(): HasOne
    {
        return $this->hasOne(DocumentNumbering::class, 'document_id');
    }

    /** Riwayat approval dokumen */
    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class, 'document_id');
    }

    /** Approval terakhir (latest) */
    public function latestApproval(): HasOne
    {
        return $this->hasOne(DocumentApproval::class, 'document_id')->latestOfMany();
    }

    /** QR Code verifikasi dokumen */
    public function qrVerification(): HasOne
    {
        return $this->hasOne(DocumentQrVerification::class, 'document_id');
    }

    // ── Accessors ───────────────────────────────────────────

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            self::TIPE_PROPOSAL => 'Proposal',
            self::TIPE_KONTRAK  => 'Kontrak',
            self::TIPE_INVOICE  => 'Invoice',
            self::TIPE_RAB      => 'RAB',
            self::TIPE_LAPORAN  => 'Laporan Akhir',
            self::TIPE_KWITANSI => 'Kwitansi',
            default             => 'Lainnya',
        };
    }

    public function getTipeBadgeClassAttribute(): string
    {
        return match ($this->tipe) {
            self::TIPE_PROPOSAL => 'badge-mendatang',
            self::TIPE_KONTRAK  => 'badge-aktif',
            self::TIPE_INVOICE  => 'badge-selesai',
            self::TIPE_RAB      => 'badge-pending',
            self::TIPE_LAPORAN  => 'badge-purple',
            self::TIPE_KWITANSI => 'badge-selesai',
            default             => 'badge-pending',
        };
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }
}
