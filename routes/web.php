<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\vendor\DashboardController;
use App\Http\Controllers\vendor\EventController;
use App\Http\Controllers\vendor\ScheduleController;
use App\Http\Controllers\vendor\TaskController;
use App\Http\Controllers\vendor\NotificationController;
use App\Http\Controllers\vendor\SettingController;

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

Route::get('/vendor', [DashboardController::class, 'index']);
Route::get('/vendor/event', [EventController::class, 'index']);
Route::get('/vendor/schedule', [ScheduleController::class, 'index']);
Route::get('/vendor/task', [TaskController::class, 'index']);
Route::get('/vendor/notification', [NotificationController::class, 'index']);
Route::get('/vendor/setting', [SettingController::class, 'index']);

require __DIR__.'/auth.php';
