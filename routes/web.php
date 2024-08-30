<?php

use App\Console\Commands\ProcessFiles;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\CustodianController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FadController;
use App\Http\Controllers\Iad\Dashboard\SpecialExternalGcRequestController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IadController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\QueryFilterController;
use App\Http\Controllers\RetailGroupController;
use App\Http\Controllers\Treasury\Dashboard\BudgetRequestController;
use App\Http\Controllers\Treasury\Dashboard\GcProductionRequestController;
use App\Http\Controllers\Treasury\Dashboard\SpecialGcRequestController;
use App\Http\Controllers\Treasury\Dashboard\StoreGcController;
use App\Http\Controllers\Treasury\Transactions\SpecialGcPaymentController;
use App\Http\Controllers\Treasury\TransactionsController;
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
    Route::get('retailgroup-dashboard', [RetailGroupController::class, 'index'])->name('retailgroup.dashboard');

    Route::get('accounting-dashboard', [AccountingController::class, 'index'])->name('accounting.dashboard');

    Route::get('finance-dashboard', [FinanceController::class, 'index'])->name('finance.dashboard');

    Route::get('iad-dashboard', [IadController::class, 'index'])->name('iad.dashboard');

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

Route::prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::get('status-scanner', [AdminController::class, 'statusScanner'])->name('status.scanner');
        Route::get('purchase-order', [AdminController::class, 'purchaseOrderDetails'])->name('purchase.order.details');
        Route::post('submit-po', [AdminController::class, 'submitPurchaseOrders'])->name('submit.po');
    });
});




//Marketing
Route::prefix('marketing')->group(function () {
    Route::name('marketing.')->group(function () {
        Route::name('promo.gc.')->group(function () {
            Route::get('promo-gc-request', [MarketingController::class, 'promogcrequest'])->name('request');
            Route::post('submit-promo-gc-request', [MarketingController::class, 'submitPromoGcRequest'])->name('submit');
        });
        Route::name('addPromo.')->group(function () {
            Route::get('add-new-promo', [MarketingController::class, 'addnewpromo'])->name('add');
            Route::get('get-denom', [MarketingController::class, 'getdenom'])->name('get.denom');
            Route::post('validate-gc', [MarketingController::class, 'validateGc'])->name('validate');
            Route::get('promo-list', [MarketingController::class, 'promoList'])->name('list');
            Route::post('gc-promo-validation', [MarketingController::class, 'gcpromovalidation'])->name('gcpromovalidation');
            Route::post('truncategcpromovalidation', [MarketingController::class, 'truncate'])->name('truncategcpromovalidation');
            Route::get('scannedGc', [MarketingController::class, 'scannedGc'])->name('scannedGc');
            Route::post('removeGc', [MarketingController::class, 'removeGc'])->name('removeGc');
            Route::post('newpromo', [MarketingController::class, 'newpromo'])->name('newpromo');
        });
        Route::name('releaseGc.')->group(function () {
            Route::get('released-promo-gc', [MarketingController::class, 'releasedpromogc'])->name('releasegc');
            Route::post('gcpromoreleased', [MarketingController::class, 'gcpromoreleased'])->name('gcpromoreleased');
        });
        Route::name('requisition.')->group(function () {
            Route::post('submit-requisition-form', [MarketingController::class, 'submitReqForm'])->name('submit.form');
            Route::get('requis-pdf', [MarketingController::class, 'requisitionPdf'])->name('requistion.pdf');
        });
        Route::name('pendingRequest.')->group(function () {
            Route::get('pending-request', [MarketingController::class, 'pendingRequest'])->name('pending.request');
            Route::post('submit-request', [MarketingController::class, 'submitPendingRequest'])->name('submit.request');
            Route::get('pending-request', [MarketingController::class, 'pendingRequest'])->name('pending.request');
            Route::post('submit-request', [MarketingController::class, 'submitPendingRequest'])->name('submit.request');
        });
        Route::name('approvedRequest.')->group(function () {
            Route::get('approved-request', [MarketingController::class, 'approvedRequest'])->name('approved.request');
            Route::get('approved-request', [MarketingController::class, 'approvedRequest'])->name('approved.request');
        });
    });
});

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
Route::middleware('auth')->group(function () {
    Route::prefix('treasury')->group(function () {
        Route::name('treasury.')->group(function () {
            Route::prefix('budget-request')->name('budget.request.')->group(function () { //can be accessed using route treasury.budget.request
                Route::get('approved', [BudgetRequestController::class, 'approvedRequest'])->name('approved');
                Route::get('view-approved-record/${id}', [BudgetRequestController::class, 'viewApprovedRequest'])->name('view.approved');

                Route::get('pending-request', [BudgetRequestController::class, 'pendingRequest'])->name('pending');
                Route::post('submit-budget-entry/{id}', [BudgetRequestController::class, 'submitBudgetEntry'])->name('budget.entry');
                Route::get('download-document/{file}', [BudgetRequestController::class, 'downloadDocument'])->name('download.document');

                Route::get('cancelled-request', [BudgetRequestController::class, 'cancelledRequest'])->name('cancelled');
                Route::get('view-cancelled-request/{$id}', [BudgetRequestController::class, 'viewCancelledRequest'])->name('view.cancelled');
            });
            Route::prefix('store-gc')->name('store.gc.')->group(function () {
                Route::get('pending-request', [StoreGcController::class, 'pendingRequestStoreGc'])->name('pending');
                Route::get('released-gc', [StoreGcController::class, 'releasedGc'])->name('released');
                Route::get('cancelled-request', [StoreGcController::class, 'cancelledRequestStoreGc'])->name('cancelled');

                Route::get('reprint/{id}', [StoreGcController::class, 'reprint'])->name('reprint');
                Route::get('view-cancelled-gc/{id}', [StoreGcController::class, 'viewCancelledGc'])->name('cancelled.gc');

                Route::get('releasing-entry-{id}', [StoreGcController::class, 'viewReleasingEntry'])->name('releasingEntry');
                Route::get('view-allocated-gc-{id}', [StoreGcController::class, 'viewAllocatedList'])->name('viewAllocatedList');
                Route::post('scan-barcode', [StoreGcController::class, 'scanBarcode'])->name('scanBarcode');
                Route::get('view-scanned-barcode', [StoreGcController::class, 'viewScannedBarcode'])->name('viewScannedBarcode');
                Route::post('submit-releasing-entry', [StoreGcController::class, 'releasingEntrySubmission'])->name('releasingEntrySubmission');
            });
            Route::prefix('gc-production-request')->name('production.request.')->group(function () {
                Route::get('approved-request', [GcProductionRequestController::class, 'approvedProductionRequest'])->name('approved');
                Route::get('view-approved-request/{id}', [GcProductionRequestController::class, 'viewApprovedProduction'])->name('view.approved');
                Route::get('view-barcode-generated/{id}', [GcProductionRequestController::class, 'viewBarcodeGenerate'])->name('view.barcode');
                Route::get('view-requisition/{id}', [GcProductionRequestController::class, 'viewRequisition'])->name('requisition');
            });
            Route::prefix('special-gc-request')->name('special.gc.')->group(function () {
                Route::get('pending-special-gc', [SpecialGcRequestController::class, 'pendingSpecialGc'])->name('pending');
                Route::get('update-pending-special-gc/{id}', [SpecialGcRequestController::class, 'updatePendingSpecialGc'])->name('update.pending');

                Route::get('get-assign-employee', [SpecialGcRequestController::class, 'getAssignEmployee'])->name('get.assign.employee');
                Route::post('get-assign-employee', [SpecialGcRequestController::class, 'addAssignEmployee'])->name('add.assign.employee');
            });


            Route::prefix('transactions')->name('transactions.')->group(function () {
                Route::prefix('production-request')->name('production.')->group(function () {
                    Route::get('gift-check', [TransactionsController::class, 'giftCheck'])->name('gc');
                    Route::post('store-gift-check', [TransactionsController::class, 'giftCheckStore'])->name('gcSubmit');
                    Route::get('envelope', [TransactionsController::class, 'envelope'])->name('envelope');
                    Route::get('accept-production-request-{id}', [TransactionsController::class, 'acceptProductionRequest'])->name('acceptProdRequest');
                });

                //Budget Request
                Route::get('budget-request', [TransactionsController::class, 'budgetRequest'])->name('budgetRequest');
                Route::post('budget-request-submission', [TransactionsController::class, 'budgetRequestSubmission'])->name('budgetRequestSubmission');
                Route::get('budget-request', [TransactionsController::class, 'budgetRequest'])->name('budgetRequest');
                Route::post('budget-request-submission', [TransactionsController::class, 'budgetRequestSubmission'])->name('budgetRequestSubmission');

                //Gc allocation
                Route::prefix('gc-allocation')->name('gcallocation.')->group(function () {
                    Route::get('/', [StoreGcController::class, 'gcAllocation'])->name('index');
                    Route::post('gc-location-submission', [StoreGcController::class, 'store'])->name('store');

                    Route::get('store-allocation', [StoreGcController::class, 'storeAllocation'])->name('storeAllocation');
                    Route::get('view-allocated-gc', [StoreGcController::class, 'viewAllocatedGc'])->name('viewAllocatedGc');
                });

                //special gc payment
                Route::prefix('special-gc-payment')->name('special.')->group(function () {
                    Route::get('external', [SpecialGcRequestController::class, 'specialExternalPayment'])->name('index');
                    Route::post('external-request', [SpecialGcRequestController::class, 'gcPaymentSubmission'])->name('paymentSubmission');
                });

            });

            Route::get('accept-production-request-{id}', [TreasuryController::class, 'acceptProductionRequest'])->name('acceptProdRequest');

            Route::get('budget-ledger', [TreasuryController::class, 'budgetLedger'])->name('budget.ledger');
            Route::get('gc-ledger', [TreasuryController::class, 'gcLedger'])->name('gc.ledger');
        });
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
        Route::post('approve-request', [FinanceController::class, 'approveRequest'])->name('approve.request');
        Route::name('pendingGc.')->group(function () {
            Route::get('pending-list', [FinanceController::class, 'specialGcPending'])->name('pending');
            Route::get('pending-approval-form', [FinanceController::class, 'SpecialGcApprovalForm'])->name('approval.form');
            Route::post('pending-approval-form-submit', [FinanceController::class, 'SpecialGcApprovalSubmit'])->name('approval.submit');
        });
        Route::name('approvedGc.')->group(function () {
            Route::get('approved-special-gc', [FinanceController::class, 'approvedGc'])->name('approved');
        });

        Route::name('budget.')->group(function () {
            Route::get('budget-pending', [FinanceController::class, 'budgetPendingIndex'])->name('pending');
        });

    });

    Route::get('/download/{filename}', function ($filename) {
        $filePath = storage_path('app/' . $filename);
        return response()->download($filePath);
    })->name('download');
});

// retailstore
Route::prefix('retail')->group(function () {
    Route::name('retail.')->group(function () {
        Route::get('retailstore-gc-request', [RetailController::class, 'gcRequest'])->name('gc.request');
        Route::post('retailstore-gc-request-submit', [RetailController::class, 'gcRequestsubmit'])->name('gc.request.submit');

        Route::name('approved.')->group(function () {
            Route::get('approved-gc-request', [RetailController::class, 'approvedGcRequest'])->name('request');
        });
        Route::name('validate.')->group(function () {
            Route::get('validate-barcode', [RetailController::class, 'validateBarcode'])->name('barcode');
        });
        Route::name('gcrequest.')->group(function () {
            Route::get('pendingList',[RetailController::class, 'pendingGcRequestList'])->name('pending.list');
            Route::get('pendingdetail',[RetailController::class, 'pendingGcRequestdetail'])->name('pending.detail');
            Route::get('cancel-request',[RetailController::class, 'cancelRequest'])->name('cancel');
            Route::put('submit-request',[RetailController::class, 'submitRequest'])->name('update');
        });
        Route::name('manage.')->group(function () {
            Route::post('remove-temporary', [RetailController::class, 'removeTemporary'])->name('remove');
            Route::post('submit-entry', [RetailController::class, 'submitEntry'])->name('submit');
        });

        Route::name('verification.')->group(function () {
            Route::get('verification-index', [RetailController::class, 'verificationIndex'])->name('index');
            Route::post('submit-verification', [RetailController::class, 'submitVerify'])->name('submit');
        });

    });
});
Route::prefix('retailgroup')->group(function () {
    Route::name('retailgroup.')->group(function () {
        Route::get('pending-gc-request', [RetailGroupController::class, 'pendingGcRequest'])->name('pending');

        Route::name('recommendation.')->group(function () {
            Route::get('setup', [RetailGroupController::class, 'setup'])->name('setup');


        });
    });
});

Route::prefix('custodian')->group(function () {
    Route::name('custodian.')->group(function () {

        Route::get('barcode-checker', [CustodianController::class, 'barcodeCheckerIndex'])->name('barcode.checker');
        Route::post('scan-barcode', [CustodianController::class, 'scanBarcode'])->name('scan.barcode');
        Route::get('received-gc', [CustodianController::class, 'receivedGcIndex'])->name('received.gc');

        Route::name('pendings.')->group(function () {
            Route::get('pending-holder-entry', [CustodianController::class, 'pendingHolderEntry'])->name('holder.entry');
            Route::get('pending-holder-setup', [CustodianController::class, 'pendingHolderSetup'])->name('external.holder.setup');
            Route::post('submit-special-external', [CustodianController::class, 'submitSpecialExternalGc'])->name('external.submit');
        });

        Route::name('approved.')->group(function () {
            Route::get('approved-gc-request', [CustodianController::class, 'approvedGcRequest'])->name('request');
            Route::get('approve-request', [CustodianController::class, 'setupApproval'])->name('setup');
        });

        Route::name('check.')->group(function () {
            Route::get('by-barcode-range', [CustodianController::class, 'barcodeOrRange'])->name('print.barcode');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('iad')->name('iad.')->group(function () {
        Route::get('receiving-index', [IadController::class, 'receivingIndex'])->name('receiving');
        Route::get('receiving-setup', [IadController::class, 'setupReceiving'])->name('setup.receiving');
        Route::post('validate-with-range', [IadController::class, 'validateByRange'])->name('validate.range');
        Route::post('delete-scanned-barcode', [IadController::class, 'removeScannedGc'])->name('remove.scanned.gc');
        Route::post('validate-barcode', [IadController::class, 'validateBarcode'])->name('validate.barcode');
        Route::post('submit-setup', [IadController::class, 'submitSetup'])->name('submit.setup');

        Route::prefix('special-external-gc-request')->name('special.external.')->group(function () {
            Route::get('view-approved-gc', [SpecialExternalGcRequestController::class, 'approvedGc'])->name('approvedGc');
            Route::get('view-approved-gc-{id}', [SpecialExternalGcRequestController::class, 'viewApprovedGcRecord'])->name('viewApprovedGc');

            Route::post('barcode-submission-{id}', [SpecialExternalGcRequestController::class, 'barcodeSubmission'])->name('barcode');
            Route::post('gc-review-{id}', [SpecialExternalGcRequestController::class, 'gcReview'])->name('gcreview');

            Route::get('gc-reprint-{id}', [SpecialExternalGcRequestController::class, 'reprint'])->name('reprint');
        });
    });
});

Route::prefix('search')->group(function () {
    Route::name('search.')->group(function () {
        Route::get('check-by', [QueryFilterController::class, 'getCheckBy'])->name('checkBy');
        Route::get('search-customer', [QueryFilterController::class, 'customer'])->name('customer');
    });
});
Route::prefix('management')->group(function () {
    Route::name('manager.')->group(function () {
        Route::post('managers-key', [ManagerController::class, 'managersKey'])->name('managers.key');
        Route::post('managers-key', [ManagerController::class, 'managersKey'])->name('managers.key');
    });
});

Route::get('file-search',[ProcessFiles::class ,'barcodeSearch']);

Route::get('generate-barcode', [CustodianController::class, 'generateBarcode'])->name('generaate');

require __DIR__ . '/auth.php';
