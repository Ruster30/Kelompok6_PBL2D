<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DocumentQrVerification
 *
 * Menyimpan QR Code untuk verifikasi keaslian dokumen.
 * Satu dokumen hanya memiliki satu QR aktif.
 * QR bersifat permanen dan tidak dapat diubah setelah dibuat.
 *
 * @property int             $id
 * @property int             $document_id
 * @property string          $verification_token
 * @property string          $qr_path
 * @property int             $generated_by
 * @property \Carbon\Carbon  $generated_at
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read Document $document
 * @property-read User $generatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|DocumentVerificationLog[] $verificationLogs
 */
class DocumentQrVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'verification_token',
        'qr_path',
        'generated_by',
        'generated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    /** Dokumen yang memiliki QR ini */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** User yang me-generate QR */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /** Riwayat scan/verifikasi QR */
    public function verificationLogs(): HasMany
    {
        return $this->hasMany(DocumentVerificationLog::class, 'verification_id');
    }
}
