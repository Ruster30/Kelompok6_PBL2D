<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Daftar Notifikasi
     */
    public function index()
    {
        $user = Auth::user();

        $notifikasi = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $unreadCount = $notifikasi->where('dibaca', false)->count();

        Notification::where('user_id', $user->id)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true
            ]);

        return view('vendor.notifikasi', compact('notifikasi', 'unreadCount'));
    }
    /**
     * Tandai Semua Dibaca
     */
    public function readAll()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true
            ]);

        return redirect()->route('vendor.notifikasi')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
