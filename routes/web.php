<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/d', function () {
    return view('welcome');
});

Route::get('/profil', function () {
    echo '<h1>Profil</h1>';
    return '<p>Jurusan Teknologi Informasi - Politeknik Negeri Padang</p>';
});

Route::get('/', function () {
    return view('landing.index');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ═══════════════════════════════════════════════════
//  CLIENT DASHBOARD  (middleware auth)
//  Saat ini: langsung return view (statis, tanpa controller)
//  Nanti:    ganti closure dengan [ClientController::class, 'method']
// ═══════════════════════════════════════════════════
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
 
    // Ringkasan Saya
    Route::get('/', function () {
        return view('client.dashboard');
    })->name('dashboard');
 
    // Event Terdaftar
    Route::get('/events', function () {
        return view('client.events');
    })->name('events');
 
    // Timeline Event
    Route::get('/timeline', function () {
        return view('client.timeline');
    })->name('timeline');
    Route::get('/timeline/{id}', function ($id) {
        return view('client.timeline');
    })->name('timeline.show');
 
    // Anggaran & Faktur
    Route::get('/invoices', function () {
        return view('client.invoices');
    })->name('invoices');
    Route::get('/invoices/{id}', function ($id) {
        return view('client.invoices');
    })->name('invoices.show');
    Route::post('/invoices/{id}/pay', function ($id) {
        return back()->with('success', 'Pembayaran sedang diproses.');
    })->name('invoices.pay');
 
    // Surat Penawaran
    Route::get('/proposals', function () {
        return view('client.proposals');
    })->name('proposals');
    Route::get('/proposals/{id}', function ($id) {
        return view('client.proposals');
    })->name('proposals.show');
 
    // Ajukan Event Baru
    Route::get('/event/create', function () {
        return view('client.event-create');
    })->name('event.create');
    Route::post('/event', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name'       => 'required|string|max:200',
            'event_type' => 'required|string',
            'event_date' => 'required|date',
        ]);
        return redirect()->route('client.dashboard')
                         ->with('success', 'Pengajuan event berhasil dikirim! Tim kami akan menghubungi Anda dalam 24 jam.');
    })->name('event.store');
 
    // Pengaturan Akun
    Route::get('/settings', function () {
        return view('client.settings');
    })->name('settings');
    Route::put('/settings/profile', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email',
        ]);
        return back()->with('success', 'Profil berhasil diperbarui.');
    })->name('settings.update');
    Route::put('/settings/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        return back()->with('success', 'Password berhasil diubah.');
    })->name('settings.password');
    Route::put('/settings/notifications', function () {
        return back()->with('success', 'Preferensi notifikasi disimpan.');
    })->name('settings.notifications');
 
});
 
// ═══════════════════════════════════════════════════
//  CATATAN MIGRASI KE CONTROLLER
//  Ketika siap pakai controller, ganti tiap closure
//  dengan: [ClientController::class, 'namaMethod']
//  Contoh:
//    Route::get('/', [ClientController::class, 'dashboard'])->name('dashboard');
// ═══════════════════════════════════════════════════