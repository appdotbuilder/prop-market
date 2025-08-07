<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Home page with property marketplace
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard with role-based content
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Property management routes
    Route::resource('properties', PropertyController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
