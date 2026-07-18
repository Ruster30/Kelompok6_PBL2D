<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendClientNotificationRequest;
use App\Http\Requests\Admin\UpdateKelolKlienRequest;
use App\Models\User;
use App\Services\KelolKlienService;
use Illuminate\Http\Request;

/**
 * KelolKlienController
 *
 * Mengelola halaman "Kelola Klien" di dashboard admin.
 * Semua logika bisnis didelegasikan ke KelolKlienService.
 */
class KelolKlienController extends Controller
{
    public function __construct(private KelolKlienService $service) {}

    /**
     * Daftar klien dengan pencarian, filter, dan pagination.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort']);
        $kliens  = $this->service->getKlienList($filters);
        $stats   = $this->service->getStatistik();

        return view('admin.kelola_klien.index', compact('kliens', 'stats', 'filters'));
    }

    /**
     * Detail satu klien: profil, riwayat event, pembayaran, notifikasi.
     */
    public function show(User $user)
    {
        abort_unless($user->role === 'client', 404);

        $detail = $this->service->getDetail($user);

        return view('admin.kelola_klien.show', $detail);
    }

    /**
     * Form edit klien (nama, email, phone).
     */
    public function edit(User $user)
    {
        abort_unless($user->role === 'client', 404);
        return view('admin.kelola_klien.edit', compact('user'));
    }

    /**
     * Simpan perubahan data klien.
     */
    public function update(UpdateKelolKlienRequest $request, User $user)
    {
        abort_unless($user->role === 'client', 404);

        $user->update($request->validated());

        return redirect()
            ->route('admin.kelola-klien.show', $user)
            ->with('success', 'Data klien berhasil diperbarui.');
    }

    /**
     * Kirim notifikasi ke satu klien.
     * Dipanggil lewat modal di halaman index atau show.
     */
    public function kirimNotifikasi(SendClientNotificationRequest $request)
    {
        $this->service->kirimNotifikasi($request->validated());

        return back()->with('success', 'Notifikasi berhasil dikirim ke klien.');
    }

    /**
     * Toggle aktif / nonaktif klien.
     */
    public function toggleStatus(User $user)
    {
        abort_unless($user->role === 'client', 404);

        $result = $this->service->toggleStatus($user);

        return back()->with('success', $result['label']);
    }

    /**
     * Hapus klien beserta datanya.
     * Hanya menghapus akun user; data event tetap ada (foreign key nullable).
     */
    public function destroy(User $user)
    {
        abort_unless($user->role === 'client', 404);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.kelola-klien.index')
            ->with('success', "Klien \"{$name}\" berhasil dihapus.");
    }
}
