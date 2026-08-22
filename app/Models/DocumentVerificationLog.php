<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DocumentVerificationLog
 *
 * Mencatat setiap aktivitas scan/verifikasi QR Code.
 * Satu QR dapat diverifikasi berkali-kali (one-to-many).
 *
 * @property int             $id
 * @property int             $verification_id
 * @property \Carbon\Carbon  $verified_at
 * @property string          $status
 * @property string|null     $ip_address
 * @property string|null     $user_agent
 * @property int|null        $verified_by
 * @property string          $verification_source
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read DocumentQrVerification $documentQrVerification
 * @property-read User|null $verifiedBy
 */
class DocumentVerificationLog extends Model
{
    use HasFactory;

    /*
     * Status verifikasi
     */
    public const STATUS_VALID    = 'valid';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_INVALID  = 'invalid';
    public const STATUS_TAMPERED = 'tampered';

    /*
     * Sumber verifikasi
     */
    public const SOURCE_PUBLIC  = 'public';
    public const SOURCE_ADMIN   = 'admin';
    public const SOURCE_API     = 'api';
    public const SOURCE_MOBILE  = 'mobile';
    public const SOURCE_SYSTEM  = 'system';

    protected $fillable = [
        'verification_id',
        'verified_at',
        'status',
        'ip_address',
        'user_agent',
        'verified_by',
        'verification_source',
    ];

    protected function casts(): array
    {
        return [
            'verified_at'          => 'datetime',
        ];
    }

    // ── Helper Methods ──────────────────────────────────────

    /** Apakah hasil verifikasi valid? */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_VALID;
    }

    // ── Relationships ───────────────────────────────────────

    /** QR yang diverifikasi */
    public function documentQrVerification(): BelongsTo
    {
        return $this->belongsTo(DocumentQrVerification::class, 'verification_id');
    }

    /** User yang melakukan verifikasi (nullable untuk scan publik) */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
