<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSend extends Model
{
    protected $fillable = [
        'document_id',
        'sender_id',
        'recipient_id',
        'pesan',
        'email_sent',
        'sent_at',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'sent_at'    => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}