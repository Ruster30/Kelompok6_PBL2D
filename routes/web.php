<?php

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