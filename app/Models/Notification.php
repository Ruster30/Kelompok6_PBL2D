<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    // ─── Relasi ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    /** Tandai notifikasi ini sebagai sudah dibaca */
    public function markAsRead(): void
    {
        $this->update(['dibaca' => true]);
    }

    /** Icon sesuai tipe notifikasi (untuk tampilan di view) */
    public function getIconAttribute(): string
    {
        return match($this->tipe) {
            'proposal'   => 'fa-file-alt',
            'pembayaran' => 'fa-credit-card',
            'invoice'    => 'fa-file-invoice',
            'kontrak'    => 'fa-file-contract',
            'event'      => 'fa-calendar',
            'sukses'     => 'fa-check-circle',
            'peringatan' => 'fa-exclamation-triangle',
            default      => 'fa-bell',
        };
    }

    /** CSS class warna icon sesuai tipe */
    public function getIconColorAttribute(): string
    {
        return match($this->tipe) {
            'proposal'   => 'text-blue-500',
            'pembayaran' => 'text-green-500',
            'invoice'    => 'text-yellow-500',
            'kontrak'    => 'text-purple-500',
            'event'      => 'text-teal-500',
            default      => 'text-gray-500',
        };
    }
}
