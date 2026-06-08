<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    /** Events milik client ini */
    public function events()
    {
        return $this->hasMany(Event::class, 'client_id');
    }

    /** Events yang di-handle admin ini sebagai PIC */
    public function handledEvents()
    {
        return $this->hasMany(Event::class, 'pic_admin_id');
    }

    /** Vendor profile milik user ini (jika role = vendor) */
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    // ─── Helper Role ─────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }
}
