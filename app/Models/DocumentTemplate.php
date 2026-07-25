<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DocumentTemplate
 *
 * Template Blade untuk generate dokumen resmi DDMS.
 * Setiap template memiliki kode unik dan path ke file Blade view.
 *
 * @property int             $id
 * @property string          $name
 * @property string          $code
 * @property string          $blade_view
 * @property string|null     $description
 * @property bool            $is_active
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Document[] $documents
 */
class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'blade_view',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Dokumen yang menggunakan template ini */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'template_id');
    }
}
