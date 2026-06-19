<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\FeedbackController;
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

Route::get('/', [App\Http\Controllers\LandingPageController::class, 'index']);

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

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');

    // Proposal & Dokumen
    Route::get('/proposals', [App\Http\Controllers\Admin\ProposalController::class, 'index'])->name('admin.proposals.index');
    Route::post('/proposals/upload', [App\Http\Controllers\Admin\ProposalController::class, 'upload'])->name('admin.proposals.upload');
    Route::delete('/proposals/{document}', [App\Http\Controllers\Admin\ProposalController::class, 'destroy'])->name('admin.proposals.destroy');
    Route::get('/proposals/invoices', [App\Http\Controllers\Admin\ProposalController::class, 'invoices'])->name('admin.proposals.invoices');
    Route::post('/proposals/invoices', [App\Http\Controllers\Admin\ProposalController::class, 'storeInvoice'])->name('admin.proposals.storeInvoice');
    Route::put('/proposals/invoices/{invoice}', [App\Http\Controllers\Admin\ProposalController::class, 'updateInvoice'])->name('admin.proposals.updateInvoice');
    Route::delete('/proposals/invoices/{invoice}', [App\Http\Controllers\Admin\ProposalController::class, 'destroyInvoice'])->name('admin.proposals.destroyInvoice');
    Route::get('/proposals/invoices/{invoice}/print', [App\Http\Controllers\Admin\ProposalController::class, 'printInvoice'])->name('admin.proposals.printInvoice');
    Route::get('/proposals/builder', [App\Http\Controllers\Admin\ProposalController::class, 'builder'])->name('admin.proposals.builder');
    Route::post('/proposals/builder/generate', [App\Http\Controllers\Admin\ProposalController::class, 'generate'])->name('admin.proposals.generate');

    // Documentation
    Route::get('/documentation', [App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('admin.documentation.index');
    Route::post('/documentation/{documentation}/approve', [App\Http\Controllers\Admin\DocumentationController::class, 'approve'])->name('admin.documentation.approve');
    Route::post('/documentation/{documentation}/reject', [App\Http\Controllers\Admin\DocumentationController::class, 'reject'])->name('admin.documentation.reject');

    // Timeline
    Route::get('/timeline', [App\Http\Controllers\Admin\TimelineController::class, 'index'])->name('admin.timeline.index');
    Route::post('/timeline', [App\Http\Controllers\Admin\TimelineController::class, 'store'])->name('admin.timeline.store');
    Route::put('/timeline/{timeline}', [App\Http\Controllers\Admin\TimelineController::class, 'update'])->name('admin.timeline.update');
    Route::delete('/timeline/{timeline}', [App\Http\Controllers\Admin\TimelineController::class, 'destroy'])->name('admin.timeline.destroy');

    // EventVendor
    Route::get('/event-vendors', [App\Http\Controllers\Admin\EventVendorController::class, 'index'])->name('admin.event-vendors.index');
    Route::post('/event-vendors', [App\Http\Controllers\Admin\EventVendorController::class, 'store'])->name('admin.event-vendors.store');
    Route::put('/event-vendors/{task}', [App\Http\Controllers\Admin\EventVendorController::class, 'update'])->name('admin.event-vendors.update');
    Route::delete('/event-vendors/{task}', [App\Http\Controllers\Admin\EventVendorController::class, 'destroy'])->name('admin.event-vendors.destroy');

    // Vendors
    Route::get('/vendors', [App\Http\Controllers\Admin\VendorController::class, 'index'])->name('admin.vendors.index');
    Route::post('/vendors', [App\Http\Controllers\Admin\VendorController::class, 'store'])->name('admin.vendors.store');
    Route::put('/vendors/{vendor}', [App\Http\Controllers\Admin\VendorController::class, 'update'])->name('admin.vendors.update');
    Route::delete('/vendors/{vendor}', [App\Http\Controllers\Admin\VendorController::class, 'destroy'])->name('admin.vendors.destroy');

    // CMS (Landing Page)
    Route::get('/cms/services', [App\Http\Controllers\Admin\CmsController::class, 'services'])->name('admin.cms.index'); // as index default
    Route::post('/cms/services', [App\Http\Controllers\Admin\CmsController::class, 'storeService'])->name('admin.cms.storeService');
    Route::put('/cms/services/{service}', [App\Http\Controllers\Admin\CmsController::class, 'updateService'])->name('admin.cms.updateService');
    Route::delete('/cms/services/{service}', [App\Http\Controllers\Admin\CmsController::class, 'destroyService'])->name('admin.cms.destroyService');

    Route::get('/cms/portfolio', [App\Http\Controllers\Admin\CmsController::class, 'portfolio'])->name('admin.cms.portfolio');
    Route::post('/cms/portfolio', [App\Http\Controllers\Admin\CmsController::class, 'storePortfolio'])->name('admin.cms.storePortfolio');
    Route::put('/cms/portfolio/{portfolio}', [App\Http\Controllers\Admin\CmsController::class, 'updatePortfolio'])->name('admin.cms.updatePortfolio');
    Route::delete('/cms/portfolio/{portfolio}', [App\Http\Controllers\Admin\CmsController::class, 'destroyPortfolio'])->name('admin.cms.destroyPortfolio');

    Route::get('/cms/team', [App\Http\Controllers\Admin\CmsController::class, 'team'])->name('admin.cms.team');
    Route::post('/cms/team', [App\Http\Controllers\Admin\CmsController::class, 'storeTeam'])->name('admin.cms.storeTeam');
    Route::put('/cms/team/{team}', [App\Http\Controllers\Admin\CmsController::class, 'updateTeam'])->name('admin.cms.updateTeam');
    Route::delete('/cms/team/{team}', [App\Http\Controllers\Admin\CmsController::class, 'destroyTeam'])->name('admin.cms.destroyTeam');

    Route::get('/cms/clients', [App\Http\Controllers\Admin\CmsController::class, 'clients'])->name('admin.cms.clients');
    Route::post('/cms/clients', [App\Http\Controllers\Admin\CmsController::class, 'storeClient'])->name('admin.cms.storeClient');
    Route::put('/cms/clients/{client}', [App\Http\Controllers\Admin\CmsController::class, 'updateClient'])->name('admin.cms.updateClient');
    Route::delete('/cms/clients/{client}', [App\Http\Controllers\Admin\CmsController::class, 'destroyClient'])->name('admin.cms.destroyClient');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings/update', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
    Route::put('/settings/update-password', [App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('admin.settings.updatePassword');

    // Alias singkat untuk kemudahan sidebar (opsional tapi dibutuhkan jika blade pakai nama ini)
    // admin.proposals alias (dari ProposalController - redirect dari upload/destroy)
    Route::get('/proposals', [App\Http\Controllers\Admin\ProposalController::class, 'index'])->name('admin.proposals.index');
    Route::get('/proposals/invoices', [App\Http\Controllers\Admin\ProposalController::class, 'invoices'])->name('admin.proposals.invoices');
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


Route::get('/feedback/{event}', [FeedbackController::class, 'create'])
    ->name('feedback.create');

Route::post('/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.store');

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
     Route::get('/notifications', [ClientController::class, 'notifications'])
    ->name('notifications');

    Route::post('/notifications/read', [ClientController::class, 'notifRead'])
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

 