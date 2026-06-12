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
    $role = request()->user()->role;
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'vendor') {
        return redirect()->route('vendor.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/vendor/dashboard', function () {
        if (request()->user()->role !== 'vendor') {
            abort(403);
        }
        return view('vendor.dashboard');
    })->name('vendor.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
// Admin Routes
// ========================================
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Clients
    Route::get('/clients', [App\Http\Controllers\Admin\ClientController::class, 'index'])->name('admin.clients.index');
    Route::get('/clients/create', [App\Http\Controllers\Admin\ClientController::class, 'create'])->name('admin.clients.create');
    Route::post('/clients', [App\Http\Controllers\Admin\ClientController::class, 'store'])->name('admin.clients.store');
    Route::get('/clients/{client}', [App\Http\Controllers\Admin\ClientController::class, 'show'])->name('admin.clients.show');
    Route::get('/clients/{client}/edit', [App\Http\Controllers\Admin\ClientController::class, 'edit'])->name('admin.clients.edit');
    Route::put('/clients/{client}', [App\Http\Controllers\Admin\ClientController::class, 'update'])->name('admin.clients.update');
    Route::delete('/clients/{client}', [App\Http\Controllers\Admin\ClientController::class, 'destroy'])->name('admin.clients.destroy');

    // Requests
    Route::get('/requests', [App\Http\Controllers\Admin\ClientRequestController::class, 'index'])->name('admin.requests.index');
    Route::get('/requests/{clientRequest}', [App\Http\Controllers\Admin\ClientRequestController::class, 'show'])->name('admin.requests.show');
    Route::post('/requests/{clientRequest}/approve', [App\Http\Controllers\Admin\ClientRequestController::class, 'approve'])->name('admin.requests.approve');
    Route::post('/requests/{clientRequest}/reject', [App\Http\Controllers\Admin\ClientRequestController::class, 'reject'])->name('admin.requests.reject');

    // Events
    Route::get('/events', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('admin.events.index');
    Route::get('/events/create', [App\Http\Controllers\Admin\EventController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [App\Http\Controllers\Admin\EventController::class, 'store'])->name('admin.events.store');
    Route::get('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'show'])->name('admin.events.show');
    Route::get('/events/{event}/edit', [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('admin.events.destroy');

    // RAB
    Route::get('/rab', [App\Http\Controllers\Admin\RabController::class, 'index'])->name('admin.rab.index');
    Route::post('/rab', [App\Http\Controllers\Admin\RabController::class, 'store'])->name('admin.rab.store');
    Route::delete('/rab/{rabItem}', [App\Http\Controllers\Admin\RabController::class, 'destroy'])->name('admin.rab.destroy');

    // Payments
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/payments/{payment}/verify', [App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('admin.payments.verify');
});

require __DIR__.'/auth.php';
