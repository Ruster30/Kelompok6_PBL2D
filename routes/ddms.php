<?php

use App\Http\Controllers\DDMS\DdmsSettingController;
use App\Http\Controllers\DDMS\DocumentController;
use App\Http\Controllers\DDMS\DocumentTemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DDMS — Digital Document Management System
|--------------------------------------------------------------------------
|
| Prefix: /api/ddms
| Middleware: auth (kecuali verify — publik)
| Authorization: Policy (di Controller)
|
| Route Model Binding: implicit binding
|
*/

Route::prefix('api/ddms')->group(function () {

    // ── Documents ───────────────────────────────────────────
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('{document}', [DocumentController::class, 'show'])->name('show');
        Route::delete('{document}', [DocumentController::class, 'destroy'])->name('destroy');

        // Workflow
        Route::post('{document}/submit', [DocumentController::class, 'submit'])->name('submit');
        Route::post('{document}/approve', [DocumentController::class, 'approve'])->name('approve');
        Route::post('{approval}/reject', [DocumentController::class, 'reject'])->name('reject');
        Route::patch('{document}/archive', [DocumentController::class, 'archive'])->name('archive');
    });

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

    // ── Public Verification ─────────────────────────────────
    Route::post('verify', [DocumentController::class, 'verify'])
        ->name('verify')
        ->withoutMiddleware('auth');
});
