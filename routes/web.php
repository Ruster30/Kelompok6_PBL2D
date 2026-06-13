<?php

use App\Http\Controllers\ProfileController;
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
    return view('dashboard.vendor');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
| Semua route ini dilindungi oleh middleware 'auth' dan 'role:vendor'
| Sesuaikan middleware dengan sistem autentikasi yang Anda gunakan.
*/

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor'])->group(function () {

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
