<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk riwayat notifikasi yang dikirim admin ke klien.
 *
 * Berbeda dengan App\Models\Notification yang dipakai oleh sistem
 * untuk semua role, model ini mencatat secara eksplisit notifikasi
 * manual yang dikirim admin dari halaman Kelola Klien.
 */
class AdminClientNotification extends Model
{
    protected $table = 'admin_client_notifications';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'judul',
        'pesan',
        'tipe',
        'email_sent',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
    ];

    // ─── Relasi ──────────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
