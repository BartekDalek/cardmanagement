<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'auth/Login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('cards', CardController::class)
        ->except(['index'])
        ->middlewareFor(['show', 'store', 'update', 'destroy'], 'throttle:cards');
});

require __DIR__.'/settings.php';
