<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\TugasController;
use App\Http\Controllers\Vendor\DokumentasiController;
use App\Http\Controllers\Vendor\NotifikasiController;

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
    $role = request()->user()->role;
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'vendor') {
        return redirect()->route('vendor.ringkasan');
    }
    return app(ClientController::class)->dashboard();
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])
        ->name('client.dashboard');

    Route::get('/vendor/dashboard', function () {
        if (request()->user()->role !== 'vendor') {
            abort(403);
        }
        return view('vendor.ringkasan');
    })->name('vendor.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});






/*
|─────────────────────────────────────────────────────────
|  CLIENT DASHBOARD ROUTES
|  Semua dilindungi middleware 'auth'
|  Prefix URL  : /client/...
|  Prefix name : client....
|─────────────────────────────────────────────────────────
*/
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
 
    // ── Ringkasan / Dashboard ────────────────────────
    Route::get('/',                         [ClientController::class, 'dashboard'])
         ->name('dashboard');
 
    // ── Event Terdaftar ──────────────────────────────
    Route::get('/events',                   [ClientController::class, 'events'])
         ->name('events');
 
    // ── Ajukan Event Baru ────────────────────────────
    Route::get('/event/create',             [ClientController::class, 'eventCreate'])
         ->name('event.create');
    Route::post('/event',                   [ClientController::class, 'eventStore'])
         ->name('event.store');
 
    // ── Timeline ─────────────────────────────────────
    Route::get('/timeline',                 [ClientController::class, 'timeline'])
         ->name('timeline');
    Route::get('/timeline/{id}',            [ClientController::class, 'timeline'])
         ->name('timeline.show');
 
    // ── Anggaran & Faktur ────────────────────────────
    Route::get('/invoices',                 [ClientController::class, 'invoices'])
         ->name('invoices');
    Route::post('/invoices/{id}/bayar',     [ClientController::class, 'bayar'])
         ->name('invoices.bayar');
 
    // ── Surat Penawaran ──────────────────────────────
    Route::get('/proposals',                [ClientController::class, 'proposals'])
         ->name('proposals');
    Route::get('/proposals/{id}',           [ClientController::class, 'proposalShow'])
         ->name('proposals.show');
 
    // ── Pengaturan Akun ──────────────────────────────
    Route::get('/settings',                 [ClientController::class, 'settings'])
         ->name('settings');
    Route::put('/settings/profile',         [ClientController::class, 'settingsProfile'])
         ->name('settings.profile');
    Route::put('/settings/password',        [ClientController::class, 'settingsPassword'])
         ->name('settings.password');
 
    // ── Notifikasi ───────────────────────────────────
    Route::post('/notifications/read',      [ClientController::class, 'notifRead'])
         ->name('notif.read');
});

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
| Semua route ini dilindungi oleh middleware 'auth' dan 'role:vendor'
| Sesuaikan middleware dengan sistem autentikasi yang Anda gunakan.
*/

Route::prefix('vendor')->name('vendor.')->middleware(['auth'])->group(function () {

    // Ringkasan (Dashboard)
    Route::get('/ringkasan', [VendorController::class, 'ringkasan'])->name('ringkasan');

    // Event Saya
    Route::get('/event-saya', [VendorController::class, 'eventSaya'])->name('event-saya');

    // Jadwal
    Route::get('/jadwal', [VendorController::class, 'jadwal'])->name('jadwal');

    // Daftar Tugas
    Route::get('/daftar-tugas', [TugasController::class, 'index'])->name('daftar-tugas');
    Route::put('/tugas/update', [TugasController::class, 'update'])->name('tugas.update');

    // Dokumentasi
    Route::post('/dokumentasi/store', [DokumentasiController::class, 'store'])->name('dokumentasi.store');

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    // Pengaturan
    Route::get('/pengaturan', [VendorController::class, 'pengaturan'])->name('pengaturan');

    // Logout
    Route::post('/logout', [VendorController::class, 'logout'])->name('logout');

    // Redirect root /vendor ke ringkasan
    Route::redirect('/', '/vendor/ringkasan');

});

require __DIR__.'/auth.php';

