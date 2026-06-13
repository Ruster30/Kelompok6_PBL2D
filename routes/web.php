<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyProfileController;

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
<<<<<<< HEAD

/*
|--------------------------------------------------------------------------
| Company Profile PDF Export
|--------------------------------------------------------------------------
| Route ini men-generate PDF dari data landing page terkini secara
| real-time. Setiap kali admin mengubah konten landing page (di
| CompanyProfileController), PDF yang diunduh akan otomatis diperbarui.
*/
Route::get('/company-profile/pdf', [CompanyProfileController::class, 'downloadPdf'])
    ->name('company-profile.pdf');
=======
Route::get('/dashboard', function () {
    return redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

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
>>>>>>> 76bad9fe144e41c580d9fbf34bad313496db9098
