<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDailyQuoteController;

Route::get('/', function () {
    return view('welcome');
});

// ========================================
// RUTAS DEL PANEL DE ADMINISTRACIÓN
// ========================================
Route::prefix('admin')->group(function () {
    // Panel de frases diarias
    Route::prefix('daily-quotes')->name('admin.daily-quotes.')->group(function () {
        Route::get('/', [AdminDailyQuoteController::class, 'index'])->name('index');
        Route::get('/create', [AdminDailyQuoteController::class, 'create'])->name('create');
        Route::post('/', [AdminDailyQuoteController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminDailyQuoteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminDailyQuoteController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminDailyQuoteController::class, 'destroy'])->name('destroy');
    });
});
