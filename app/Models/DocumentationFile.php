<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'file_path',
        'status',
        'tipe_file',
    ];

    public function documentation()
    {
        return $this->belongsTo(Documentation::class, 'documentation_id');
    }
}
