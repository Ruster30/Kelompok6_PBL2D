<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DdmsSetting
 *
 * Menyimpan konfigurasi global Digital Document Management System.
 * Tidak memiliki relationship — tabel mandiri.
 *
 * @property int             $id
 * @property string          $setting_key
 * @property string|null     $setting_value
 * @property string|null     $description
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class DdmsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];
}
