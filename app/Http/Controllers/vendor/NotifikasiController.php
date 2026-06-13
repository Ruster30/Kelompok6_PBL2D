<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Daftar Notifikasi
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        $notifikasi = Notifikasi::where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->get();

        $unreadCount = $notifikasi->where('is_read', false)->count();

        // Tandai semua sebagai dibaca saat dibuka
        Notifikasi::where('vendor_id', $vendor->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('vendor.pages.notifikasi', compact('notifikasi', 'unreadCount'));
    }

    /**
     * Tandai Semua Dibaca
     */
    public function readAll()
    {
        $vendor = Auth::user()->vendor;

        Notifikasi::where('vendor_id', $vendor->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('vendor.notifikasi')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
