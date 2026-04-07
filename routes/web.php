<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\auth\DosenController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profil', function () {
    echo '<h1>Profil</h1>';
    return '<p>Jurusan Teknologi Informasi - Politeknik Negeri Padang</p>';
});

Route::get('/mahasiswa/ti/rafi', function () {
    echo "<p style='font-size:40;color:orange'>Jurusan Teknologi Informasi";
    echo '<h1>Selamat datang Rafi..</h1>';
    echo '<hr>';
    echo '<p>Lorem ipsum dolor sit amet consectetur adipsicing elit. culpa, delectus.</p>';
});

Route::get('/mahasiswa/{nama}', function ($nama) {
    return '<p>Ketua HIMA Jurusan TI adalah <b>'. $nama . '</b></p>';
});
Route::get('/mahasiswa/{nama}/{nim}', function ($nama,$nim) {
    return '<p>Ketua HIMA Jurusan TI adalah <b>' . $nama .'</b> dengan NIM <b>' . $nim . '</b></p>';
});



Route::get('/user/{id}', function ($id) {
    return '<p>User Admin memiliki id <b>' .$id .'</b></p>';
})->where('id', '[0-9]+');

Route::get('/buku-tamu', function () {
    return '<h2>Buku Tamu</h2>';
});
Route::redirect('/guest-book','/buku-tamu');

Route::prefix('/login')->group(function() {
    Route::get('/mahasiswa', function () {
        return '<h2>Login Mahasiswa</h2>';
    });
    Route::get('/dosen', function () {
        return '<h2>Login Dosen</h2>';
    });
    Route::get('/admin', function () {
        return '<h2>Login Admin</h2>';
    });
});

Route::fallback(function () {
    return '<h2>Halaman tidak ditemukan</h2>';
});


Route::get('/nilai_mahasiswa', function () {
    $nama='Taylor Otwell';
    $nim='2022180001';
    $total_nilai=100;
    return view('akademik.nilai_mahasiswa', compact('nama','nim','total_nilai'));
});


Route::get('/perulangan',function() {
    $nama='Bill gates';
    $nim='2022180001';
    $total_nilai=[80,70,20,60,45];
    return view('akademik.perulangan', compact('nama','nim','total_nilai'));
});


Route::get('/latihan_nilai', function () {
    $nama='Max';
    $nim='3030303030';
    $uts='70';
    $uas='90';
    $total_nilai=(0.4*$uts)+(0.6*$uas);
    $keterangan=($total_nilai >= 60) ? 'Lulus' : 'Tidak Lulus';
    return view('akademik.latihan_nilai', compact('nama', 'nim', 'uts', 'uas',  'total_nilai', 'keterangan'));
});

Route::get('/mahasiswa', [App\Http\Controllers\MahasiswaController::class, 'index']);
Route::get('/mahasiswa-show', [App\Http\Controllers\MahasiswaController::class,'show']);

Route::get('/dosen', [App\Http\Controllers\auth\DosenController::class, 'index']);