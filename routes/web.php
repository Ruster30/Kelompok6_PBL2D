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

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');

    // Proposal & Dokumen
    Route::get('/proposal-dokumen', [App\Http\Controllers\Admin\ProposalController::class, 'index'])->name('admin.proposal_dokumen.index');
    Route::post('/proposal-dokumen/upload', [App\Http\Controllers\Admin\ProposalController::class, 'upload'])->name('admin.proposal_dokumen.upload');
    Route::delete('/proposal-dokumen/{document}', [App\Http\Controllers\Admin\ProposalController::class, 'destroy'])->name('admin.proposal_dokumen.destroy');
    Route::get('/proposal-dokumen/invoices', [App\Http\Controllers\Admin\ProposalController::class, 'invoices'])->name('admin.proposal_dokumen.invoices');
    Route::post('/proposal-dokumen/invoices', [App\Http\Controllers\Admin\ProposalController::class, 'storeInvoice'])->name('admin.proposal_dokumen.storeInvoice');
    Route::put('/proposal-dokumen/invoices/{invoice}', [App\Http\Controllers\Admin\ProposalController::class, 'updateInvoice'])->name('admin.proposal_dokumen.updateInvoice');
    Route::delete('/proposal-dokumen/invoices/{invoice}', [App\Http\Controllers\Admin\ProposalController::class, 'destroyInvoice'])->name('admin.proposal_dokumen.destroyInvoice');
    Route::get('/proposal-dokumen/invoices/{invoice}/print', [App\Http\Controllers\Admin\ProposalController::class, 'printInvoice'])->name('admin.proposal_dokumen.printInvoice');
    Route::get('/proposal-dokumen/builder', [App\Http\Controllers\Admin\ProposalController::class, 'builder'])->name('admin.proposal_dokumen.builder');
    Route::post('/proposal-dokumen/builder/generate', [App\Http\Controllers\Admin\ProposalController::class, 'generate'])->name('admin.proposal_dokumen.generate');

    // Documentation
    Route::get('/documentation', [App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('admin.documentation.index');
    Route::post('/documentation/{documentation}/approve', [App\Http\Controllers\Admin\DocumentationController::class, 'approve'])->name('admin.documentation.approve');
    Route::post('/documentation/{documentation}/reject', [App\Http\Controllers\Admin\DocumentationController::class, 'reject'])->name('admin.documentation.reject');

    // Timeline
    Route::get('/timeline', [App\Http\Controllers\Admin\TimelineController::class, 'index'])->name('admin.timeline.index');
    Route::post('/timeline', [App\Http\Controllers\Admin\TimelineController::class, 'store'])->name('admin.timeline.store');
    Route::put('/timeline/{timeline}', [App\Http\Controllers\Admin\TimelineController::class, 'update'])->name('admin.timeline.update');
    Route::delete('/timeline/{timeline}', [App\Http\Controllers\Admin\TimelineController::class, 'destroy'])->name('admin.timeline.destroy');

    // Tasks
    Route::get('/tasks', [App\Http\Controllers\Admin\TaskController::class, 'index'])->name('admin.tasks.index');
    Route::post('/tasks', [App\Http\Controllers\Admin\TaskController::class, 'store'])->name('admin.tasks.store');
    Route::put('/tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'update'])->name('admin.tasks.update');
    Route::delete('/tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'destroy'])->name('admin.tasks.destroy');

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
