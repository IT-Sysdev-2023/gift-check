<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\CustodianController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\Treasury\MainController;
use App\Http\Controllers\Treasury\TreasuryController;
use App\Http\Middleware\UserTypeRoute;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
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

Route::get('admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

//Dashboards
Route::middleware(['auth'])->group(function () {

    Route::get('treasury-dashboard', [TreasuryController::class, 'index'])->name('treasury.dashboard');

    Route::get('retail-dashboard', [RetailController::class, 'index'])->name('retail.dashboard');

    Route::get('accounting-dashboard', [AccountingController::class, 'index'])->name('accounting.dashboard');

    Route::get('finance-dashboard', [FinanceController::class, 'index'])->name('finance.dashboard');

    Route::get('custodian-dashboard', [CustodianController::class, 'index'])->name('custodian.dashboard');

    Route::get('marketing-dashboard', [MarketingController::class, 'index'])->name('marketing.dashboard');

    Route::get('admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('view-barcode-status', [AdminController::class, 'index'])->name('view.barcode.status');

    });
});

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Marketing
Route::prefix('marketing')->group(function () {
    Route::name('marketing.')->group(function (){
        Route::name('promo.gc.')->group(function () {
            Route::get('promo-gc-request', [MarketingController::class, 'promogcrequest'])->name('request');
            Route::post('', [MarketingController::class, 'submitPromoGcRequest'])->name('submit');
        });
        Route::name('addPromo.')->group(function () {
            Route::get('add-new-promo', [MarketingController::class, 'addnewpromo'])->name('add');
            Route::post('validate-gc', [MarketingController::class, 'validateGc'])->name('validate');
            Route::get('promo-list', [MarketingController::class, 'promoList'])->name('list');
            Route::post('gc-promo-validation', [MarketingController::class, 'gcpromovalidation'])->name('gcpromovalidation');
            Route::post('truncategcpromovalidation', [MarketingController::class, 'truncate'])->name('truncategcpromovalidation');
        });
    });


});


Route::get('released-promo-gc', [MarketingController::class, 'releasedpromogc'])->name('released.promo.gc');
Route::get('promo-status', [MarketingController::class, 'promoStatus'])->name('promo.status');
Route::get('manage-supplier', [MarketingController::class, 'manageSupplier'])->name('manage.supplier');
Route::get('sales-treasury-sales', [MarketingController::class, 'treasurySales'])->name('marketing.sales.treasury.sales');
Route::get('sales-store-sales', [MarketingController::class, 'storeSales'])->name('sales.store.sales');
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
Route::get('get-view-promo-details', [MarketingController::class, 'getPromoDetails'])->name('get.view.details');
Route::get('get-store-sales-details', [MarketingController::class, 'getStoreSaleDetails'])->name('get.store.sale.details');
Route::get('get-transaction-pos-detail', [MarketingController::class, 'getTransactionPOSdetail'])->name('get.transaction.pos.detail');
Route::get('get-view-barcode-details', [MarketingController::class, 'getBarcodeDetails'])->name('get.sub.barcode.details');
Route::get('view-treasury-sales', [MarketingController::class, 'viewTreasurySales'])->name('view.treasury.sales');




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
Route::get('verified-gc-icm', [MarketingController::class, 'verifiedGc_icm'])->name('verified.gc.island.city.mall');



//Treasury
Route::prefix('treasury')->group(function () {
    Route::name('treasury.')->group(function () {
        Route::prefix('budget-request')->name('budget.request.')->group(function () { //can be accessed using route treasury.budget.request
            Route::get('approved', [TreasuryController::class, 'approvedRequest'])->name('approved');
            Route::get('view-approved-record/${id}', [TreasuryController::class, 'viewApprovedRequest'])->name('view.approved');

            Route::get('pending-request', [TreasuryController::class, 'pendingRequest'])->name('pending');
            Route::post('submit-budget-entry/{id}', [TreasuryController::class, 'submitBudgetEntry'])->name('budget.entry');
            Route::get('download-document/{file}', [TreasuryController::class, 'downloadDocument'])->name('download.document');

            Route::get('cancelled-request', [TreasuryController::class, 'cancelledRequest'])->name('cancelled');
            Route::get('view-cancelled-request/{$id}', [TreasuryController::class, 'viewCancelledRequest'])->name('view.cancelled');

        });
        Route::prefix('store-gc')->name('store.gc.')->group(function () {
            Route::get('pending-request', [TreasuryController::class, 'pendingRequestStoreGc'])->name('pending');
            Route::get('released-gc', [TreasuryController::class, 'releasedGc'])->name('released');
            Route::get('cancelled-request', [TreasuryController::class, 'cancelledRequestStoreGc'])->name('cancelled');

            Route::get('reprint/{id}', [TreasuryController::class, 'reprint'])->name('reprint');
            Route::get('view-cancelled-gc/{id}', [TreasuryController::class, 'viewCancelledGc'])->name('cancelled.gc');

        });

        Route::prefix('gc-production-request')->name('production.request.')->group(function () {
            Route::get('approved-request', [TreasuryController::class, 'approvedProductionRequest'])->name('approved');
            Route::get('view-approved-request/{id}', [TreasuryController::class, 'viewApprovedProduction'])->name('view.approved');
            Route::get('view-barcode-generated/{id}', [TreasuryController::class, 'viewBarcodeGenerate'])->name('view.barcode');
            Route::get('view-requisition/{id}', [TreasuryController::class, 'viewRequisition'])->name('requisition');
        });


        Route::get('budget-ledger', [TreasuryController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('gc-ledger', [TreasuryController::class, 'gcLedger'])->name('gc.ledger');


    });

});
Route::prefix('documents')->group(function () {
    Route::name('start.')->group(function () {
        Route::get('budget-ledger', [DocumentController::class, 'startGeneratingBudgetLedger'])->name('budget.ledger');
    });
});


//Finance
Route::prefix('finance')->group(function () {
    Route::name('finance.')->group(function () {
        Route::get('budget-ledger', [FinanceController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('spgc-ledger', [FinanceController::class, 'spgcLedger'])->name('spgc.ledger');
        Route::get('app-promo-request', [FinanceController::class, 'approvedPromoRequest'])->name('app.promo.request');
        Route::get('pen-promo-request', [FinanceController::class, 'pendingPromoRequest'])->name('pen.promo.request');
        Route::get('approved-released-reports', [FinanceController::class, 'approvedAndReleasedSpgc'])->name('approved.released.reports');
        Route::get('generate-approved-spgc-reports', [FinanceController::class, 'approvedSpgdcPdfExcelFunction'])->name('approved.spgc.pdf.excel');
        Route::get('generate-released-spgc-reports', [FinanceController::class, 'releasedSpgcPdfExcelFunction'])->name('released.spgc.pdf.excel');
        Route::get('generate-spgc-ledger', [FinanceController::class, 'generateSpgcPromotionalExcel'])->name('spgc.ledger.start');
    });

    Route::get('/download/{filename}', function ($filename) {
        $filePath = storage_path('app/' . $filename);
        return response()->download($filePath);
    })->name('download');
});


require __DIR__ . '/auth.php';
