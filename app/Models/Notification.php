<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['dibaca' => true]);
    }

    public function getIconAttribute(): string
    {
        return match($this->tipe) {
            'sukses'     => 'bi-check-circle-fill',
            'peringatan' => 'bi-exclamation-triangle-fill',
            default      => 'bi-info-circle-fill',
        };
    }

    public function getWarnaAttribute(): string
    {
        return match($this->tipe) {
            'sukses'     => '#10b981',
            'peringatan' => '#f59e0b',
            default      => '#3b82f6',
        };
    }
}
