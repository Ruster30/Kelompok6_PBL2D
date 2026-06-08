<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'judul',
        'deskripsi',
        'tanggal_upload',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'date',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /** File foto/video yang terkait dengan dokumentasi ini */
    public function files()
    {
        return $this->hasMany(DocumentationFile::class, 'documentation_id');
    }
}
