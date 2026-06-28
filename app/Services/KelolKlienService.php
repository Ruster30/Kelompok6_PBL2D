<?php

namespace App\Services;

use App\Models\AdminClientNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * KelolKlienService
 *
 * Memisahkan logika bisnis Kelola Klien dari controller.
 * Controller hanya bertugas menerima request dan mengembalikan response.
 */
class KelolKlienService
{
    /**
     * Mengambil daftar klien dengan filter, sorting, dan pagination.
     */
    public function getKlienList(array $filters): LengthAwarePaginator
    {
        $query = User::where('role', 'client')
            ->withCount('events');

        // Filter pencarian
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter status (aktif/nonaktif berdasarkan last_active_at)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'aktif') {
                $query->where(function ($q) {
                    $q->where('last_active_at', '>=', now()->subDays(30))
                      ->orWhere(function ($qq) {
                          $qq->whereNull('last_active_at')
                             ->where('updated_at', '>=', now()->subDays(30));
                      });
                });
            } elseif ($filters['status'] === 'nonaktif') {
                $query->where(function ($q) {
                    $q->where('last_active_at', '<', now()->subDays(30))
                      ->orWhere(function ($qq) {
                          $qq->whereNull('last_active_at')
                             ->where('updated_at', '<', now()->subDays(30));
                      });
                });
            }
        }

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        match ($sort) {
            'terlama'   => $query->oldest(),
            'nama_az'   => $query->orderBy('name'),
            'nama_za'   => $query->orderByDesc('name'),
            default     => $query->latest(), // terbaru
        };

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Statistik ringkasan untuk card header Kelola Klien.
     */
    public function getStatistik(): array
    {
        $allClients = User::where('role', 'client')->get();

        $aktif = $allClients->filter(function ($u) {
            $tanggal = $u->last_active_at ?? $u->updated_at;
            return $tanggal && $tanggal->diffInDays(now()) <= 30;
        })->count();

        $notifTerkirim = AdminClientNotification::where('sender_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'total'          => $allClients->count(),
            'aktif'          => $aktif,
            'nonaktif'       => $allClients->count() - $aktif,
            'notif_terkirim' => $notifTerkirim,
        ];
    }

    /**
     * Mengirim notifikasi dari admin ke klien.
     * 1. Simpan ke tabel admin_client_notifications (riwayat kirim)
     * 2. Simpan ke tabel notifications (agar muncul di dashboard klien)
     * 3. Kirim email jika mail driver aktif (bukan 'log' atau 'array')
     *
     * @return AdminClientNotification
     */
    public function kirimNotifikasi(array $data): AdminClientNotification
    {
        $recipient = User::findOrFail($data['recipient_id']);

        // 1. Catat riwayat kirim
        $record = AdminClientNotification::create([
            'sender_id'    => auth()->id(),
            'recipient_id' => $recipient->id,
            'judul'        => $data['judul'],
            'pesan'        => $data['pesan'],
            'tipe'         => $data['tipe'],
            'email_sent'   => false,
        ]);

        // 2. Masukkan ke dashboard notifikasi klien
        Notification::create([
            'user_id' => $recipient->id,
            'judul'   => $data['judul'],
            'pesan'   => $data['pesan'],
            'tipe'    => $data['tipe'],
            'dibaca'  => false,
        ]);

        // 3. Kirim email jika driver aktif
        $emailSent = $this->kirimEmail($recipient, $data);
        if ($emailSent) {
            $record->update(['email_sent' => true]);
        }

        return $record;
    }

    /**
     * Mengubah status aktif/nonaktif klien dengan toggle.
     * Implementasi: set atau reset last_active_at.
     */
    public function toggleStatus(User $klien): array
    {
        $tanggal = $klien->last_active_at ?? $klien->updated_at;
        $sedangAktif = $tanggal && $tanggal->diffInDays(now()) <= 30;

        if ($sedangAktif) {
            // Nonaktifkan: set last_active_at ke 31 hari lalu
            $klien->update(['last_active_at' => now()->subDays(31)]);
            return ['status' => 'nonaktif', 'label' => 'Klien berhasil dinonaktifkan.'];
        } else {
            // Aktifkan kembali
            $klien->update(['last_active_at' => now()]);
            return ['status' => 'aktif', 'label' => 'Klien berhasil diaktifkan.'];
        }
    }

    /**
     * Detail lengkap satu klien untuk halaman show.
     */
    public function getDetail(User $klien): array
    {
        $klien->loadCount('events');
        $klien->load([
            'events' => fn ($q) => $q->latest()->take(10),
            'events.invoices',
            'receivedAdminNotifications' => fn ($q) => $q->take(10),
        ]);

        // Hitung total pembayaran via events klien
        $eventIds = $klien->events->pluck('id');
        $totalBayar = \App\Models\Payment::whereHas('invoice', fn ($q) =>
            $q->whereIn('event_id', $eventIds)
        )->where('status_pembayaran', 'diverifikasi')->sum('nominal');

        return [
            'klien'       => $klien,
            'totalBayar'  => $totalBayar,
            'notifikasi'  => $klien->receivedAdminNotifications,
        ];
    }

    // ─── Private helpers ─────────────────────────────────────

    private function kirimEmail(User $recipient, array $data): bool
    {
        $driver = config('mail.default');

        // Jangan kirim email jika driver belum dikonfigurasi production
        if (in_array($driver, ['log', 'array', 'null'])) {
            return false;
        }

        try {
            Mail::send([], [], function ($message) use ($recipient, $data) {
                $message->to($recipient->email, $recipient->name)
                    ->subject('[Alpha Organizer] ' . $data['judul'])
                    ->html(
                        view('emails.notifikasi_klien', [
                            'judul'     => $data['judul'],
                            'pesan'     => $data['pesan'],
                            'recipient' => $recipient,
                        ])->render()
                    );
            });
            return true;
        } catch (\Exception $e) {
            Log::warning('Gagal kirim email notifikasi ke klien: ' . $e->getMessage());
            return false;
        }
    }
}
