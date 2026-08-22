<?php

use App\Http\Controllers\DDMS\DdmsSettingController;
use App\Http\Controllers\DDMS\DocumentTemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DDMS — Digital Document Management System
|--------------------------------------------------------------------------
|
| Prefix: /api/ddms
| Middleware: auth
| Authorization: Policy (di Controller)
|
| Route Model Binding: implicit binding
|
| Catatan: workflow dokumen (submit/approve/reject/publish/verify) tidak
| lagi disediakan melalui API — alur final sepenuhnya via web (Blade).
| Endpoint yang tersisa hanya manajemen template dan settings.
|
*/

Route::prefix('ddms')->group(function () {

    // ── Templates ───────────────────────────────────────────
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
        Route::get('{template}', [DocumentTemplateController::class, 'show'])->name('show');
        Route::post('/', [DocumentTemplateController::class, 'store'])->name('store');
        Route::put('{template}', [DocumentTemplateController::class, 'update'])->name('update');
        Route::delete('{template}', [DocumentTemplateController::class, 'destroy'])->name('destroy');
    });

    // ── Settings ────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [DdmsSettingController::class, 'index'])->name('index');
        Route::get('{setting}', [DdmsSettingController::class, 'show'])->name('show');
        Route::post('/', [DdmsSettingController::class, 'store'])->name('store');
        Route::put('{setting}', [DdmsSettingController::class, 'update'])->name('update');
        Route::delete('{setting}', [DdmsSettingController::class, 'destroy'])->name('destroy');
    });
});
