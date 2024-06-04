<?php

use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RetailStore\MainController;
use App\Http\Controllers\Treasury\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Login'
       );
});
Route::get('/test', [DashboardController::class, 'test']);
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('promo-list', [MarketingController::class, 'promoList'])->name('promo.list');
Route::get('addnewpromo', [MarketingController::class, 'addnewpromo'])->name('add.new.promo');
Route::get('promo-gc-request', [MarketingController::class, 'promogcrequest'])->name('promo.gc.request');
Route::get('released-promo-gc', [MarketingController::class, 'releasedpromogc'])->name('released.promo.gc');
Route::get('promo-status', [MarketingController::class, 'promoStatus'])->name('promo.status');
require __DIR__.'/auth.php';
