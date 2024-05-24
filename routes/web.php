<?php

use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpecialExternalGcrequestController;
use App\Services\GcProductionRequestService;
use App\Services\SpecialExternalGcRequestService;
use App\Services\StoreGcRequestService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/test', [GcProductionRequestService::class, 'cancelledRequest']);
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
