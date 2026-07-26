<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DocumentApproval
 *
 * Menyimpan proses approval dokumen resmi.
 * Satu dokumen dapat memiliki banyak riwayat approval
 * (misalnya: reject -> revisi -> submit ulang -> approve).
 *
 * @property int             $id
 * @property int             $document_id
 * @property int             $submitted_by
 * @property int|null        $approver_id
 * @property string          $status
 * @property string|null     $approval_note
 * @property \Carbon\Carbon|null $submitted_at
 * @property \Carbon\Carbon|null $reviewed_at
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read Document $document
 * @property-read User $submittedBy
 * @property-read User|null $approvedBy
 */
class DocumentApproval extends Model
{
    use HasFactory;

    /*
     * Status approval
     */
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'document_id',
        'submitted_by',
        'approver_id',
        'status',
        'approval_note',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    // ── Helper Methods ──────────────────────────────────────

    /** Apakah masih menunggu review? */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** Apakah sudah disetujui? */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /** Apakah ditolak? */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // ── Relationships ───────────────────────────────────────

    /** Dokumen yang diajukan approval */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** Admin yang mengajukan dokumen untuk approval */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /** Direktur yang melakukan review (approve/reject) */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
