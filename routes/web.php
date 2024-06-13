<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\CustodianController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\Treasury\MainController;
use App\Http\Controllers\Treasury\DashboardController;
use App\Http\Controllers\Treasury\TreasuryController;
use App\Http\Middleware\UserTypeRoute;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render(
        'Login'
    );
})->middleware('guest');

Route::get('/not-found', function () {
    return 'Empty';
})->name('not.found');

//Dashboards
Route::middleware(['auth'])->group(function () {

    Route::get('treasury-dashboard', [TreasuryController::class, 'index'])->name('treasury.dashboard');

    Route::get('retail-dashboard', [RetailController::class, 'index'])->name('retail.dashboard');

    Route::get('accounting-dashboard', [AccountingController::class, 'index'])->name('accounting.dashboard');

    Route::get('finance-dashboard', [FinanceController::class, 'index'])->name('finance.dashboard');

    Route::get('custodian-dashboard', [CustodianController::class, 'index'])->name('custodian.dashboard');

    Route::get('marketing-dashboard', [MarketingController::class, 'index'])->name('marketing.dashboard');
});

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Marketing
Route::prefix('marketing')->group(function () {
    Route::name('treasury.')->group(function () {
    });
});
Route::get('promo-list', [MarketingController::class, 'promoList'])->name('promo.list');
Route::get('addnewpromo', [MarketingController::class, 'addnewpromo'])->name('add.new.promo');
Route::get('promo-gc-request', [MarketingController::class, 'promogcrequest'])->name('promo.gc.request');
Route::get('released-promo-gc', [MarketingController::class, 'releasedpromogc'])->name('released.promo.gc');
Route::get('promo-status', [MarketingController::class, 'promoStatus'])->name('promo.status');
Route::get('manage-supplier', [MarketingController::class, 'manageSupplier'])->name('manage.supplier');
Route::get('sales/treasury-sales', [MarketingController::class, 'treasurySales'])->name('sales.treasury.sales');
Route::get('sales/store-sales', [MarketingController::class, 'storeSales'])->name('sales.store.sales');
Route::get('verified-gc-alturas-mall', [MarketingController::class, 'verifiedGc_Amall'])->name('verified.gc.alturas.mall');
Route::get('verified-gc-alturas-talibon', [MarketingController::class, 'verifiedGc_A_talibon'])->name('verified.gc.alturas.talibon');
Route::get('verified-gc-alturas-tubigon', [MarketingController::class, 'verifiedGc_A_tubigon'])->name('verified.gc.alturas.tubigon');
Route::get('verified-gc-colonade-colon', [MarketingController::class, 'verifiedGc_colonadeColon'])->name('verified.gc.colonade.colon');
Route::get('verified-gc-colonade-mandaue', [MarketingController::class, 'verifiedGc_colonadeMandaue'])->name('verified.gc.colonade.mandaue');
Route::get('verified-gc-alta-cita', [MarketingController::class, 'verifiedGc_AltaCita'])->name('verified.gc.alta.cita');
Route::get('verified-gc-plaza-marcela', [MarketingController::class, 'verifiedGc_plazaMarcela'])->name('verified.gc.plaza.marcela');
Route::get('verified-gc-farmers-market', [MarketingController::class, 'verifiedGc_farmersMarket'])->name('verified.gc.farmers.market');
Route::get('verified-gc-udc', [MarketingController::class, 'verifiedGc_udc'])->name('verified.gc.udc');
Route::get('verified-gc-screenville', [MarketingController::class, 'verifiedGc_screenville'])->name('verified.gc.screenville');
Route::get('verified-gc-asctech', [MarketingController::class, 'verifiedGc_AscTech'])->name('verified.gc.asctech');
Route::get('verified-gc-icm', [MarketingController::class, 'verifiedGc_icm'])->name('verified.gc.island.city.mall');


//Treasury
Route::prefix('treasury')->group(function () {
    Route::name('treasury.')->group(function () {
        Route::prefix('budget-ledger')->group(function () {

            Route::get('approved', [TreasuryController::class, 'budgetLedgerApproved'])->name('approved.budget.ledger');
        });

        Route::get('budget-ledger', [TreasuryController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('budget-ledger', [TreasuryController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('gc-ledger', [TreasuryController::class, 'gcLedger'])->name('gc.ledger');


    });

});

//Finance
Route::prefix('finance')->group(function () {
    Route::name('finance.')->group(function () {
        Route::get('budget-ledger', [FinanceController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('spgc-ledger', [FinanceController::class, 'spgcLedger'])->name('spgc.ledger');
    });
});


require __DIR__ . '/auth.php';
