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
            'password'          => 'hashed',
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

    /** Notifikasi milik user ini */
    public function notifikasi()
    {
        return $this->hasMany(Notification::class, 'user_id');
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

    // ─── Accessors ───────────────────────────────────────────

    /** Inisial nama untuk avatar teks (misal "Ahmad Rizki" → "AR") */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper($words[0][0] . $words[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * URL avatar: pakai file upload jika ada,
     * fallback ke ui-avatars.com dengan warna brand.
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2DD4BF&color=fff&bold=true';
    }

    /** Jumlah notifikasi yang belum dibaca */
    public function getUnreadNotifCountAttribute(): int
    {
        return $this->notifikasi()->where('dibaca', false)->count();
    }
}
