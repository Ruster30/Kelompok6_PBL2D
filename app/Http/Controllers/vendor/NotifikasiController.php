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
        $notifikasi = Notification::where('user_id', auth()->id())
            ->latest()
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('dibaca', 0)
            ->count();

        return view('vendor.notifikasi', compact(
            'notifikasi',
            'unreadCount'
        ));
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
