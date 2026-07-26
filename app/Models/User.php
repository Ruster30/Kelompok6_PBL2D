<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /*
     * Role constants
     */
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_DIRECTOR = 'director';
    public const ROLE_VENDOR   = 'vendor';
    public const ROLE_CLIENT   = 'client';

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
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'approval_pin',
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
            'last_active_at'    => 'datetime',
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

    /** Notifikasi yang dikirim admin ke klien ini */
    public function receivedAdminNotifications()
    {
        return $this->hasMany(AdminClientNotification::class, 'recipient_id')->latest();
    }

    /** Notifikasi yang pernah dikirim admin ini ke klien */
    public function sentAdminNotifications()
    {
        return $this->hasMany(AdminClientNotification::class, 'sender_id')->latest();
    }

    /** Pembayaran yang dilakukan oleh klien ini (via event_id) */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Event::class, 'client_id', 'invoice_id')
                    ->join('invoices', 'invoices.id', '=', 'payments.invoice_id');
    }

        // ─── Helper Role ─────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDirector(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }

    public function isVendor(): bool
    {
        return $this->role === self::ROLE_VENDOR;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    /** Apakah user termasuk manajemen (admin atau director)? */
    public function isManagement(): bool
    {
        return $this->isAdmin() || $this->isDirector();
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

    /** Apakah klien aktif (login dalam 30 hari terakhir) */
    public function getIsActiveClientAttribute(): bool
    {
        if (!$this->last_active_at) {
            // Fallback: cek tanggal update akun
            return $this->updated_at->diffInDays(now()) <= 30;
        }
        return $this->last_active_at->diffInDays(now()) <= 30;
    }

    /** Total event yang dimiliki klien ini */
    public function getTotalEventAttribute(): int
    {
        return $this->events()->count();
    }

    /**
     * Cek apakah user sudah memiliki PIN approval.
     */
    public function hasApprovalPin(): bool
    {
        return $this->approval_pin !== null;
    }

}