<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DocumentNumbering
 *
 * Menyimpan nomor resmi dokumen yang telah disetujui.
 * Satu dokumen hanya memiliki satu nomor (hubungan one-to-one).
 * Nomor bersifat permanen dan tidak dapat diubah.
 *
 * @property int             $id
 * @property int             $document_id
 * @property string          $document_number
 * @property string          $prefix
 * @property int             $year
 * @property int             $sequence_number
 * @property int             $generated_by
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read Document $document
 * @property-read User $generatedBy
 */
class DocumentNumbering extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_number',
        'prefix',
        'year',
        'sequence_number',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'year'            => 'integer',
            'sequence_number' => 'integer',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    /** Dokumen yang memiliki nomor ini */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** User yang me-generate nomor */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
