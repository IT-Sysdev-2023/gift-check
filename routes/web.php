<?php

use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RetailStore\MainController;
use App\Http\Controllers\Treasury\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Login'
       );
});
Route::get('/hash', [FinanceController::class, 'toHash']);
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('budget-ledger', [FinanceController::class, 'budgetLedger'])->name('budget.ledger');
Route::get('spgc-ledger', [FinanceController::class, 'spgcLedger'])->name('spgc.ledger');
Route::get('spgc-approved-reports', [FinanceController::class, 'reportSpgcApproved'])->name('spgc.approved.reports');

require __DIR__.'/auth.php';
