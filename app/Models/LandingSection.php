<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'content',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Ambil section berdasarkan key (hero, contact, footer, tentang)
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('section_key', $key)->where('is_active', true)->first();
    }
}
