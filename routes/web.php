<?php

use App\Http\Controllers\SerpController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/', [SerpController::class, 'index'])->name('serp.index');
    Route::post('/search', [SerpController::class, 'search'])->name('serp.search');
    Route::get('/locations', [SerpController::class, 'locations'])->name('serp.locations');
});
