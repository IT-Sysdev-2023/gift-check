<?php

use App\Console\Commands\ProcessFiles;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AccountingReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BudgetAdjustmentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustodianController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EodController;
use App\Http\Controllers\FadController;
use App\Http\Controllers\Iad\Dashboard\SpecialExternalGcRequestController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\Treasury\ReportsController;
use \App\Http\Controllers\Treasury\MasterfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IadController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\QueryFilterController;
use App\Http\Controllers\RetailGroupController;
use App\Http\Controllers\StoreAccountingController;
use App\Http\Controllers\Treasury\AdjustmentController;
use App\Http\Controllers\Treasury\Dashboard\BudgetRequestController;
use App\Http\Controllers\Treasury\Dashboard\GcProductionRequestController;
use App\Http\Controllers\Treasury\Dashboard\SpecialGcRequestController;
use App\Http\Controllers\Treasury\Dashboard\StoreGcController;
use App\Http\Controllers\Treasury\Transactions\GcAllocationController;
use App\Http\Controllers\Treasury\Transactions\InstitutionGcRefundController;
use App\Http\Controllers\Treasury\Transactions\InstitutionGcSalesController;
use App\Http\Controllers\Treasury\Transactions\ProductionRequestController;
use App\Http\Controllers\Treasury\Transactions\PromoGcReleasingController;
use App\Http\Controllers\Treasury\Transactions\RetailGcReleasingController;
use App\Http\Controllers\Treasury\TransactionsController;
use App\Http\Controllers\Treasury\TreasuryController;
use App\Http\Controllers\UserDetailsController;
use App\Models\InstitutEod;
use App\Models\InstitutPayment;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render(
        'Login'
    );
})->middleware('guest');


Route::get('/not-found', function () {
    return 'Empty';
})->name('not.found');

Route::fallback(function () {
    return view('notFound');
});
Route::get('kanding', function () {
    return Storage::disk('fad')->files();
});

Route::get('admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('employee', [UserDetailsController::class, 'index']);
Route::get('get-employee', [UserDetailsController::class, 'getEmp'])->name('get.employee');
Route::post('add-employee-{id}', [UserDetailsController::class, 'addEmp'])->name('add.employee');

//Dashboards
Route::middleware(['auth'])->group(function () {

    Route::get('treasury-dashboard', [TreasuryController::class, 'index'])->name('treasury.dashboard')->middleware('userType:treasury,admin');

    Route::get('retail-dashboard', [RetailController::class, 'index'])->name('retail.dashboard')->middleware('userType:retail,admin');

    Route::get('retailgroup-dashboard', [RetailGroupController::class, 'index'])->name('retailgroup.dashboard')->middleware('userType:retailgroup,admin');

    Route::get('accounting-dashboard', [AccountingController::class, 'index'])->name('accounting.dashboard')->middleware('userType:accounting,admin');

    Route::get('finance-dashboard', [FinanceController::class, 'index'])->name('finance.dashboard')->middleware('userType:finance,admin');

    Route::get('iad-dashboard', [IadController::class, 'index'])->name('iad.dashboard')->middleware('userType:iad,admin');

    Route::get('custodian-dashboard', [CustodianController::class, 'index'])->name('custodian.dashboard')->middleware('userType:custodian,admin');

    Route::get('eod-dashboard', [EodController::class, 'index'])->name('eod.dashboard')->middleware('userType:eod,admin');

    Route::get('marketing-dashboard', [MarketingController::class, 'index'])->name('marketing.dashboard')->middleware('userType:marketing,admin');

    Route::get('storeaccounting-dashboard', [StoreAccountingController::class, 'storeAccountingDashboard'])->name('storeaccounting.dashboard');
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
        Route::get('add-new-fund', [AdminController::class, 'addNewFund'])->name('revolvingFund.saveNewFund');
        Route::get('users_add_user', [AdminController::class, 'users_save_user'])->name('masterfile.user.saveUser');
        Route::post('update-password', [AdminController::class, 'updateStoreStaffPassword'])->name('masterfile.updateStoreStaffPassword');
        Route::post('update-store-setup', [AdminController::class, 'updateStoreStaffSetup'])->name('masterfile.updateStoreStaffSetup');
        Route::get('denomination-setup', [AdminController::class, 'denominationSetup'])->name('masterfile.denominationSetup');
        Route::get('credit-card-setup', [AdminController::class, 'creditCardSetup'])->name('masterfile.creditCardSetup');
        Route::get('store-setup', [AdminController::class, 'setupStore'])->name('masterfile.setupStore');
        Route::get('customer-setup', [AdminController::class, 'customerSetup'])->name('masterfile.customer.setup');
        Route::post('update-customer-store-register', [AdminController::class, 'updateCustomerStoreRegister'])->name('masterfile.updateCustomerStoreRegister');
        Route::post('update-institute-customer', [AdminController::class, 'updateInstituteCustomer'])->name('masterfile.UpdateInstituteCustomer');
        Route::get('store-staff-setup', [AdminController::class, 'storeSetup'])->name('masterfile.store.staff');
        Route::get('save-user', [AdminController::class, 'saveUser'])->name('masterfile.store.saveUser');

        Route::get('status-scanner', [AdminController::class, 'statusScanner'])->name('status.scanner');

        Route::get('purchase-order', [AdminController::class, 'purchaseOrderDetails'])->name('purchase.order.details');
        Route::post('submit-po', [AdminController::class, 'submitPurchaseOrders'])->name('submit.po');
        Route::name('masterfile.')->group(function () {
            Route::get('user-list', [AdminController::class, 'userlist'])->name('users');
            Route::get('update-status', [AdminController::class, 'updatestatus'])->name('updatestatus');
            Route::get('user-reset-password', [AdminController::class, 'usersResetPassword'])->name('usersResetPassword');
            Route::get('save-store', [AdminController::class, 'saveStore'])->name('saveStore');
            Route::get('issue-receipt', [AdminController::class, 'issueReceipt'])->name('issueReceipt');
            Route::get('save-credit-card', [AdminController::class, 'saveCreditCard'])->name('saveCreditCard');
            Route::get('revolving-fund', [AdminController::class, 'revolving_fund'])->name('revolvingFund');
            route::get('save-denomination', [AdminController::class, 'saveDenomination'])->name('saveDenomination');
            route::post('update-denomination', [AdminController::class, 'UpdateDenomination'])->name('saveUpdateDenomination');
            route::post('update-user', [AdminController::class, 'updateUser'])->name('updateUser');
            route::post('update-revolvingfund', [AdminController::class, 'updateRevolvingFund'])->name('updateRevolvingFund');
            route::post('update-special-customer', [AdminController::class, 'updateSpecialCustomer'])->name('updateSpecialCustomer');
            Route::post('submit-po', [AdminController::class, 'submitPurchaseOrders'])->name('submit.po')->middleware([HandlePrecognitiveRequests::class]);
            Route::get('edit-po-{id}', [AdminController::class, 'editPoDetails'])->name('edit.po');
        });

        Route::get('eod-reports', [AdminController::class, 'eodReports'])->name('eod.reports');
        Route::get('eod-reports', [AdminController::class, 'eodReports'])->name('eod.reports');
        Route::get('eod-reports-generate', [AdminController::class, 'generateReports'])->name('generate');


        Route::get('setup-po-{any}', [AdminController::class, 'setupPurchaseOrders'])->name('setup');

        Route::post('submit-po-to-iad', [AdminController::class, 'submitPurchaseOrdersToIad'])->name('submit.po.to.iad')->middleware([HandlePrecognitiveRequests::class]);
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
            Route::get('reprint', [MarketingController::class, 'reprint'])->name('reprint');
        });
        Route::name('pendingRequest.')->group(function () {
            Route::get('pending-request', [MarketingController::class, 'pendingRequest'])->name('pending.request');
            Route::get('checker-pending-request', [MarketingController::class, 'checkerpendingRequest'])->name('checker.pending.request');
            Route::get('approve-pending-request', [MarketingController::class, 'approvependingRequest'])->name('approve.pending.request');
            Route::get('getSigners', [MarketingController::class, 'getSigners'])->name('getSigners');
            Route::get('getChecker', [MarketingController::class, 'getChecker'])->name('getChecker');
            Route::post('submit-request', [MarketingController::class, 'submitPendingRequest'])->name('submit.request');
        });
        Route::name('approvedRequest.')->group(function () {
            Route::get('approved-request', [MarketingController::class, 'approvedRequest'])->name('approved.request');
            Route::get('get-requisition', [MarketingController::class, 'getrequisition'])->name('getrequisition');
        });
        Route::name('promoGcRequest.')->group(function () {
            Route::get('promo-pending-list', [MarketingController::class, 'promoPendinglist'])->name('pending.list');
            Route::get('promo-cancelled-list', [MarketingController::class, 'promocancelledlist'])->name('cancelled.list');
            Route::get('promo-approved-list', [MarketingController::class, 'promoApprovedlist'])->name('approved.list');
            Route::get('selected-approved', [MarketingController::class, 'selectedApproved'])->name('selected.approved');
            Route::get('selected-promo-pending-request', [MarketingController::class, 'selectedPromoPendingRequest'])->name('pending.selected');
            Route::post('submit', [MarketingController::class, 'submitUpdate'])->name('submit');
        });
        Route::name('manage-supplier.')->group(function () {
            Route::get('manage-supplier', [MarketingController::class, 'manageSupplier'])->name('manage.supplier');
            Route::post('add-supplier', [MarketingController::class, 'addSupplier'])->name('add.supplier');
            Route::get('status-supplier', [MarketingController::class, 'statusSupplier'])->name('status.supplier');
        });
        Route::name('verifiedgc.')->group(function () {
            Route::get('verified-gc-alturas-mall', [MarketingController::class, 'verifiedGc_Amall'])->name('alturas.mall');
            Route::get('verified-gc-alturas-talibon', [MarketingController::class, 'verifiedGc_A_talibon'])->name('alturas.talibon');
            Route::get('verified-gc-icm', [MarketingController::class, 'verifiedGc_icm'])->name('island.city.mall');
            Route::get('verified-gc-plaza-marcela', [MarketingController::class, 'verifiedGc_plazaMarcela'])->name('plaza.marcela');
            Route::get('verified-gc-alturas-tubigon', [MarketingController::class, 'verifiedGc_A_tubigon'])->name('alturas.tubigon');
            Route::get('verified-gc-colonade-colon', [MarketingController::class, 'verifiedGc_colonadeColon'])->name('colonade.colon');
            Route::get('verified-gc-colonade-mandaue', [MarketingController::class, 'verifiedGc_colonadeMandaue'])->name('colonade.mandaue');
            Route::get('verified-gc-alta-cita', [MarketingController::class, 'verifiedGc_AltaCita'])->name('alta.cita');
            Route::get('verified-gc-farmers-market', [MarketingController::class, 'verifiedGc_farmersMarket'])->name('farmers.market');
            Route::get('verified-gc-udc', [MarketingController::class, 'verifiedGc_udc'])->name('udc');
            Route::get('verified-gc-screenville', [MarketingController::class, 'verifiedGc_screenville'])->name('screenville');
            Route::get('verified-gc-asctech', [MarketingController::class, 'verifiedGc_AscTech'])->name('asctech');
        });
        Route::name('promostatus.')->group(function () {
            Route::get('promo-status', [MarketingController::class, 'promoStatus'])->name('promo.status');
        });
        Route::name('treasurysales.')->group(function () {
            Route::get('sales', [MarketingController::class, 'treasurySales'])->name('sales.treasury.sales');
            Route::get('view-treasury-sales', [MarketingController::class, 'viewTreasurySales'])->name('view.treasury.sales');
        });
        Route::name('cancelled.')->group(function () {
            Route::get('cancelled-production-request', [MarketingController::class, 'cancelledProductionRequest'])->name('production.request');
            Route::get('view-cancelled-production-request', [MarketingController::class, 'ViewcancelledProductionRequest'])->name('view.cancelled.request');
        });
        Route::name('special-gc.')->group(function () {
            Route::get('pending', [MarketingController::class, 'pendingspgclist'])->name('pending');
            Route::get('pending-view-details', [MarketingController::class, 'pendingspgclistview'])->name('pending.view');
            Route::get('approved-external-gc-request', [MarketingController::class, 'ApprovedExternalGcRequest'])->name('aexgcreq');
            Route::get('selected-approved-external-gc-request', [MarketingController::class, 'selectedApprovedExternalGcRequest'])->name('selectedaexgcreq');
            Route::get('cancelled-especial-external-list', [MarketingController::class, 'cancelledspexgclist'])->name('cancelledspexgclsit');
        });
        Route::name('releasedspexgc.')->group(function () {
            Route::get('count-released-spex-gc', [MarketingController::class, 'countreleasedspexgc'])->name('count');
            Route::get('released-spex-gc', [MarketingController::class, 'releasedspexgc'])->name('releasedspexgc');
            Route::get('view-released-spex-gc', [MarketingController::class, 'viewReleasedSpexGc'])->name('viewReleasedSpexGcdetails');
        });
    });
});


Route::get('sales-treasury-sales', [MarketingController::class, 'treasurySales'])->name('marketing.sales.treasury.sales');
Route::get('sales-store-sales', [MarketingController::class, 'storeSales'])->name('sales.store.sales');
Route::get('get-view-promo-details', [MarketingController::class, 'getPromoDetails'])->name('get.view.details');
Route::get('get-store-sales-details', [MarketingController::class, 'getStoreSaleDetails'])->name('get.store.sale.details');
Route::get('get-transaction-pos-detail', [MarketingController::class, 'getTransactionPOSdetail'])->name('get.transaction.pos.detail');
Route::get('get-view-barcode-details', [MarketingController::class, 'getBarcodeDetails'])->name('get.sub.barcode.details');
Route::get('sales/store-sales', [MarketingController::class, 'storeSales'])->name('sales.store.sales');


//Treasury
Route::middleware(['auth'])->group(function () {
    Route::prefix('treasury')->name('treasury.')->group(function () {
        Route::prefix('budget-request')->name('budget.request.')->group(function () { //can be accessed using route treasury.budget.request
            Route::get('approved', [BudgetRequestController::class, 'approvedRequest'])->name('approved');
            Route::get('view-approved-record/${id}', [BudgetRequestController::class, 'viewApprovedRequest'])->name('view.approved');

            Route::get('pending-request', [BudgetRequestController::class, 'pendingRequest'])->name('pending');
            Route::put('submit-budget-entry/{id}', [BudgetRequestController::class, 'submitBudgetEntry'])->name('budget.entry');
            Route::get('download-document/{file}', [BudgetRequestController::class, 'downloadDocument'])->name('download.document');

            Route::get('cancelled-request', [BudgetRequestController::class, 'cancelledRequest'])->name('cancelled');
            // Route::get('view-cancelled-request/{$id}', [BudgetRequestController::class, 'viewCancelledRequest'])->name('view.cancelled');
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
            Route::get('cancelled-request', [GcProductionRequestController::class, 'cancelledProductionRequest'])->name('cancelled');
            Route::get('view-cancelled-request-{id}', [GcProductionRequestController::class, 'viewCancelledProduction'])->name('viewCancelled');
            Route::get('view-approved-request/{id}', [GcProductionRequestController::class, 'viewApprovedProduction'])->name('view.approved');
            Route::get('view-barcode-generated/{id}', [GcProductionRequestController::class, 'viewBarcodeGenerate'])->name('view.barcode');
            Route::get('view-requisition/{id}', [GcProductionRequestController::class, 'viewRequisition'])->name('requisition');
            Route::get('download-{file}', [GcProductionRequestController::class, 'download'])->name('download.document');
            Route::get('reprint-{id}', [GcProductionRequestController::class, 'reprintRequest'])->name('reprint');

            Route::get('pending', [GcProductionRequestController::class, 'pending'])->name('pending');
            Route::post('submit-pending-request', [GcProductionRequestController::class, 'pendingSubmission'])->name('pendingSubmission');
        });
        Route::prefix('special-gc-request')->name('special.gc.')->group(function () {
            Route::get('pending-special-gc', [SpecialGcRequestController::class, 'pendingSpecialGc'])->name('pending');
            Route::get('update-pending-special-gc/{id}', [SpecialGcRequestController::class, 'updatePendingSpecialGc'])->name('update.pending');

            // Route::post('add-assign-employee', [SpecialGcRequestController::class, 'addAssignEmployee'])->name('add.assign.employee');
            Route::post('update-special-gc', [SpecialGcRequestController::class, 'updateSpecialGc'])->name('update.special');

            Route::get('reviewing-gc', [SpecialGcRequestController::class, 'releasingGc'])->name('gcReleasing');
            Route::get('reviewing-gc-{id}', [SpecialGcRequestController::class, 'viewReleasing'])->name('viewReleasing');
            Route::get('view-denominations-{id}', [SpecialGcRequestController::class, 'viewDenomination'])->name('viewDenomination');
            Route::post('submit-gc-internal-{id}', [SpecialGcRequestController::class, 'relasingGcSubmission'])->name('releasingSubmission');

            Route::get('released-gc', [SpecialGcRequestController::class, 'releasedGc'])->name('specialReleasedGc');
            // Route::get('reviewed-gc-for-releasing', [SpecialGcRequestController::class,'reviewedGcReleasing'])->name('reviewedGcReleasing');
            Route::get('view-released-gc-{id}', [SpecialGcRequestController::class, 'viewReleasedGc'])->name('viewReleasedGc');

            Route::get('approved-request', [SpecialGcRequestController::class, 'approvedRequest'])->name('approvedRequest');
            Route::get('cancelled-request', [SpecialGcRequestController::class, 'cancelledRequest'])->name('cancelledRequest');
            Route::get('view-cancelled-request-{id}', [SpecialGcRequestController::class, 'viewCancelledRequest'])->name('viewCancelledRequest');
            Route::get('view-approved-request-{id}', [SpecialGcRequestController::class, 'viewApprovedRequest'])->name('viewApprovedRequest');
        });
        Route::prefix('transactions')->name('transactions.')->group(function () {

            //Budget Request
            Route::get('budget-request', [TransactionsController::class, 'budgetRequest'])->name('budgetRequest');
            Route::post('budget-request-submission', [TransactionsController::class, 'budgetRequestSubmission'])->name('budgetRequestSubmission');

            //Production Request
            Route::prefix('production-request')->name('production.')->group(function () {
                Route::get('gift-check', [ProductionRequestController::class, 'giftCheck'])->name('gc');
                Route::post('store-gift-check', [ProductionRequestController::class, 'giftCheckStore'])->name('gcSubmit');
                Route::get('envelope', [ProductionRequestController::class, 'envelope'])->name('envelope');
                Route::post('envelope-store', [ProductionRequestController::class, 'envelopeStore'])->name('envelopSubmission');
                Route::get('accept-production-request-{id}', [ProductionRequestController::class, 'acceptProductionRequest'])->name('acceptProdRequest');
            });

            //Gc allocation
            Route::prefix('gc-allocation')->name('gcallocation.')->group(function () {
                Route::get('/', [GcAllocationController::class, 'gcAllocation'])->name('index');
                Route::post('gc-location-submission', [GcAllocationController::class, 'store'])->name('store');

                Route::get('store-allocation', [GcAllocationController::class, 'storeAllocation'])->name('storeAllocation');
                Route::get('view-allocated-gc', [GcAllocationController::class, 'viewAllocatedGc'])->name('viewAllocatedGc');
                Route::get('view-gc-for-allocation', [GcAllocationController::class, 'viewForAllocationGc'])->name('forallocation');
            });

            //Gc Releasiing Retail Store
            Route::prefix('retail-gc-releasing')->name('retail.releasing.')->group(function () {
                Route::get('/', [RetailGcReleasingController::class, 'index'])->name('index');
            });

            //Promo Gc Releasing
            Route::prefix('promo-gc-releasing')->name('promo.gc.releasing.')->group(function () {
                Route::get('/', [PromoGcReleasingController::class, 'index'])->name('index');

                Route::get('promo-gc-request-{id}', [PromoGcReleasingController::class, 'denominationList'])->name('denominationList');
                Route::post('scan-barcode', [PromoGcReleasingController::class, 'scanBarcode'])->name('scanBarcode');

                Route::post('form-submission', [PromoGcReleasingController::class, 'formSubmission'])->name('submission');
            });

            //Institution GC Sales
            Route::prefix('institution-gc-sales')->name('institution.gc.sales.')->group(function () {
                Route::get('/', [InstitutionGcSalesController::class, 'index'])->name('index');
                Route::get('view-trasactions', [InstitutionGcSalesController::class, 'viewTransaction'])->name('transaction');

                Route::get('scan-barcode', [InstitutionGcSalesController::class, 'scanBarcode'])->name('scan');
                Route::put('remove-barcode-{barcode}', [InstitutionGcSalesController::class, 'removeBarcode'])->name('removeBarcode');

                Route::post('form-submission', [InstitutionGcSalesController::class, 'formSubmission'])->name('submission');
                Route::get('view-transaction-details-{id}', [InstitutionGcSalesController::class, 'transactionDetails'])->name('transactionDetails');
                Route::get('print-ar-{id}', [InstitutionGcSalesController::class, 'printAr'])->name('printAr');
                Route::get('reprint-{id}', [InstitutionGcSalesController::class, 'reprint'])->name('reprint');
                Route::get('generate-excel-{id}', [InstitutionGcSalesController::class, 'generateExcel'])->name('excel');
            });

            //Institution Gc Refund
            Route::prefix('institution-gc-refund')->name('intitution.refund.')->group(function () {
                Route::get('/', [InstitutionGcRefundController::class, 'index'])->name('index');

                Route::post('refund-submission', [InstitutionGcRefundController::class, 'store'])->name('refund');
            });

            //special gc payment
            Route::prefix('special-gc-payment')->name('special.')->group(function () {
                Route::get('/', [SpecialGcRequestController::class, 'specialGcPayment'])->name('index');
                Route::post('submission-request', [SpecialGcRequestController::class, 'gcPaymentSubmission'])->name('paymentSubmission');
            });

            //EOD
            Route::prefix('treasury-eod')->name('eod.')->group(function () {
                Route::get('/', [EodController::class, 'eodList'])->name('eodList');
                Route::get('generate-pdf-{id}', [EodController::class, 'generatePdf'])->name('pdf');

                Route::get('gc-sales-report', [EodController::class, 'gcSalesReport'])->name('gcSales');
                Route::post('gc-sales-report-eod', [EodController::class, 'toEndOfDay'])->name('setToEod');
            });
        });
        Route::prefix('masterfile')->name('masterfile.')->group(function () {
            Route::get('customer-setup', [MasterfileController::class, 'customerSetup'])->name('customersetup');
            Route::post('add-customer', [MasterfileController::class, 'storeCustomer'])->name('addCustomer')->middleware([HandlePrecognitiveRequests::class]);

            Route::get('special-external-setup', [MasterfileController::class, 'specialExternalSetup'])->name('externalSetup');
            Route::post('add-special-external-customer', [MasterfileController::class, 'storeSpecialExternalCustomer'])->name('addSpecialExternalCustomer')->middleware([HandlePrecognitiveRequests::class]);

            Route::get('payment-fund-setup', [MasterfileController::class, 'paymentFundSetup'])->name('paymentFundSetup');
            Route::post('add-payment-fund', [MasterfileController::class, 'storePaymentFund'])->name('addPaymentFund')->middleware([HandlePrecognitiveRequests::class]);
        });
        Route::prefix('adjustment')->name('adjustment.')->group(function () {

            Route::get('allocation', [AdjustmentController::class, 'allocationAdjustment'])->name('allocation');

            Route::prefix('allocation')->name('allocation.')->group(function () {
                Route::get('allocation-setup', [AdjustmentController::class, 'allocationSetup'])->name('allocationSetup');
                Route::post('allocation-setup-submission', [AdjustmentController::class, 'allocationSetupStore'])->name('setupSubmission');
            });


            Route::get('allocation-details-{id}', [AdjustmentController::class, 'viewAllocationAdjustment'])->name('viewAllocation');

            Route::get('budget-adjustment', [AdjustmentController::class, 'budgetAdjustments'])->name('budgetAdjustments');
            Route::get('budget-adjustment-update', [AdjustmentController::class, 'budgetAdjustmentsUpdate'])->name('budgetAdjustmentsUpdate');
            Route::post('budget-adjustment-update-submission', [AdjustmentController::class, 'budgetAdjustmentsUpdateSubmission'])->name('budgetAdjustmentUpdateSubmission');
            Route::post('budget-adjustment-submission', [AdjustmentController::class, 'storeBudgetAdjustment'])->name('budgetAdjustmentSubmission');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('gc-report', [ReportsController::class,'gcReport'])->name('index');
            Route::get('eod-report', [ReportsController::class,'eodReport'])->name('eod');
            Route::get('generate-gc-report', [ReportsController::class,'generateGcReports'])->name('generate.gc');
            Route::get('generate-eod-report', [ReportsController::class,'generateEodReports'])->name('generate.eod');

            Route::get('list-of-generated-reports', [ReportsController::class,'listOfGeneratedReports'])->name('generatedReports');
            Route::get('download-generated-report', [ReportsController::class,'downloadGeneratedReport'])->name('download.gc');
        });

        Route::get('accept-production-request-{id}', [TreasuryController::class, 'acceptProductionRequest'])->name('acceptProdRequest');

        Route::get('budget-ledger', [TreasuryController::class, 'budgetLedger'])->name('budget.ledger');
        Route::get('gc-ledger', [TreasuryController::class, 'gcLedger'])->name('gc.ledger');
    });
});
Route::prefix('documents')->group(function () {
    Route::name('start.')->group(function () {
        Route::get('budget-ledger', [DocumentController::class, 'startGeneratingBudgetLedger'])->name('budget.ledger');
    });
});

Route::prefix('eod')->group(function () {
    Route::name('eod.')->group(function () {
        Route::get('eod-verified-gc', [EodController::class, 'eodVerifiedGc'])->name('verified.gc');
        Route::get('eod-process', [EodController::class, 'processEod'])->name('process');
        Route::get('list', [EodController::class, 'list'])->name('list');
    });
});

//Accounting =====

Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::name('pending.')->group(function () {
        Route::get('pending-special-gc', [SpecialGcRequestController::class, 'pendingSpecialGc'])->name('index');
    });
    Route::name('approved.')->group(function () {
        Route::get('approved-gc-request', [CustodianController::class, 'approvedGcRequest'])->name('request');
    });
    Route::name('payment.')->group(function () {
        Route::get('payment-gc', [AccountingController::class, 'paymantGc'])->name('payment.gc');
        Route::get('setup-payment-{id}', [AccountingController::class, 'setupPayment'])->name('setup');
        Route::get('setup-table-{id}', [AccountingController::class, 'tableFetch'])->name('fetch');
        Route::post('submit-form', [AccountingController::class, 'submitPayment'])->name('submit');
        Route::get('payment-viewing', [AccountingController::class, 'paymentViewing'])->name('viewing');
        Route::get('payment-details-{id}', [AccountingController::class, 'paymentDetails'])->name('details');
    });

    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('spgc-report-approved', [AccountingReportController::class, 'spgcApprovedReport'])->name('special.gc.approved');
        Route::get('generate-spgc-report-approved', [AccountingReportController::class, 'generateApprovedReport'])->name('generate.special.gc.approved');

        Route::get('spgc-report-released', [AccountingReportController::class, 'spgcReleasedReport'])->name('special.gc.released');
        Route::get('generated-reports', [AccountingReportController::class, 'generatedReports'])->name('generatedReports');
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
            Route::get('selected-special-gc', [FinanceController::class, 'selectedapprovedGc'])->name('selected.approved');
        });

        Route::name('budget.')->group(function () {
            Route::get('budget-pending', [FinanceController::class, 'budgetPendingIndex'])->name('pending');
            Route::get('budget-setup', [FinanceController::class, 'setupBudget'])->name('setup');
            Route::post('budget-submit', [FinanceController::class, 'submitBudget'])->name('submit');
            Route::get('approved-budget', [FinanceController::class, 'approvedBudget'])->name('approved');
            Route::get('approved-budget-details-{id}', [FinanceController::class, 'approvedBudgetDetails'])->name('approved.details');
            Route::get('reprint-{id}', [FinanceController::class, 'reprint'])->name('reprint');
        });

        Route::name('cancelledSpecialExternalGC.')->group(function(){
            Route::get('cancelled-especial-external-gc',[FinanceController::class, 'list'])->name('list');
            Route::get('view-cancelled-especial-external-gc',[FinanceController::class, 'view'])->name('view');
        });
    });

    Route::get('/download/{filename}', function ($filename) {
        $filePath = storage_path('app/' . $filename);
        return response()->download($filePath);
    })->name('download');
})->middleware('userType:finance');

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
            Route::get('pendingList', [RetailController::class, 'pendingGcRequestList'])->name('pending.list');
            Route::get('pendingdetail', [RetailController::class, 'pendingGcRequestdetail'])->name('pending.detail');
            Route::get('cancel-request', [RetailController::class, 'cancelRequest'])->name('cancel');
            Route::put('submit-request', [RetailController::class, 'submitRequest'])->name('update');
        });
        Route::name('manage.')->group(function () {
            Route::post('remove-temporary', [RetailController::class, 'removeTemporary'])->name('remove');
            Route::post('submit-entry', [RetailController::class, 'submitEntry'])->name('submit');
        });

        Route::name('verification.')->group(function () {
            Route::get('verification-index', [RetailController::class, 'verificationIndex'])->name('index');
            Route::post('submit-verification', [RetailController::class, 'submitVerify'])->name('submit');
        });
        Route::get('AvailableGc', [RetailController::class, 'availableGcList'])->name('availableGcList');
        Route::get('soldGc', [RetailController::class, 'soldGc'])->name('soldGc');
    

        Route::get('lostGc', [RetailController::class, 'lostGC'])->name('lostGc');
        Route::post('submit-lost-gc',[RetailController::class, 'submitLostGc'])->name('submit-lost-gc')->middleware([HandlePrecognitiveRequests::class]);
   
        Route::get('store-eod', [RetailController::class, 'storeEOD'])->name('storeEod');
        
        Route::name('verified-gc.')->group(function (){
            Route::get('verified-gc-list', [RetailController::class, 'verifiedGc'])->name('list');
            Route::get('gc-details',[RetailController::class, 'gcdetails'])->name('gcdetails');
        });
        
    });
});
Route::prefix('retailgroup')->name('retailgroup.')->group(function () {
    Route::get('pending-gc-request', [RetailGroupController::class, 'pendingGcRequest'])->name('pending');

    Route::name('recommendation.')->group(function () {
        Route::get('setup', [RetailGroupController::class, 'setup'])->name('setup');
        Route::post('submit', [RetailGroupController::class, 'submitPendingRequest'])->name('submit');
    });

    Route::get('approved-promo-request', [RetailGroupController::class, 'approvedPromoRequest'])->name('approved');
    Route::get('approved-details-{id}', [RetailGroupController::class, 'approvedDetails'])->name('details');

    Route::get('legder', [RetailGroupController::class, 'ledger'])->name('ledger');
});

Route::prefix('custodian')->group(function () {

    Route::name('custodian.')->group(function () {

        Route::get('barcode-checker', [CustodianController::class, 'barcodeCheckerIndex'])->name('barcode.checker');
        Route::post('scan-barcode', [CustodianController::class, 'scanBarcode'])->name('scan.barcode');
        Route::get('received-gc-barcode', [CustodianController::class, 'receivedGcIndex'])->name('received.gc');
        Route::get('available-gc-allocation', [CustodianController::class, 'getAvailableGc'])->name('available.gc');

        Route::name('pendings.')->group(function () {
            Route::get('pending-holder-entry', [CustodianController::class, 'pendingHolderEntry'])->name('holder.entry');
            Route::get('pending-holder-setup', [CustodianController::class, 'pendingHolderSetup'])->name('external.holder.setup');
            Route::post('submit-special-external', [CustodianController::class, 'submitSpecialExternalGc'])->name('external.submit');
        });

        Route::name('approved.')->group(function () {
            Route::get('approved-gc-request', [CustodianController::class, 'approvedGcRequest'])->name('request');
            Route::get('approve-request-special', [CustodianController::class, 'setupApproval'])->name('setup');
            Route::get('reprint-request-{id}', [CustodianController::class, 'reprintRequest'])->name('reprint.request');
        });

        Route::name('check.')->group(function () {
            Route::get('by-barcode-range', [CustodianController::class, 'barcodeOrRange'])->name('print.barcode');
        });
        Route::name('production.')->group(function () {
            Route::get('production', [CustodianController::class, 'productionIndex'])->name('index');
            Route::get('production-cancelled', [CustodianController::class, 'productionCancelled'])->name('pro.cancelled');
            Route::get('production-cancelled-details-{id}', [CustodianController::class, 'productionCancelledDetails'])->name('cancelled.details');
            Route::get('production-details-{id}', [CustodianController::class, 'productionApprovedDetails'])->name('details');
            Route::get('barcode-details-{id}', [CustodianController::class, 'barcodeApprovedDetails'])->name('barcode.details');
            Route::get('barcode-every-{id}', [CustodianController::class, 'getEveryBarcode'])->name('barcode.every');
            Route::get('requisition-details-{id}', [CustodianController::class, 'getRequisitionDetails'])->name('requisition');
        });
        Route::get('text-fileuploader', [CustodianController::class, 'textFileUploader'])->name('textfile.uploader');
        Route::post('upload', [CustodianController::class, 'upload'])->name('upload');
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
        Route::get('received-gc-view', [IadController::class, 'receivedGc'])->name('view.received');
        Route::get('received-gc-view-details-{id}', [IadController::class, 'receivedGcDetails'])->name('details.view');

        Route::prefix('special-external-gc-request')->name('special.external.')->group(function () {
            Route::get('view-approved-gc', [SpecialExternalGcRequestController::class, 'approvedGc'])->name('approvedGc');
            Route::get('view-approved-gc-{id}', [SpecialExternalGcRequestController::class, 'viewApprovedGcRecord'])->name('viewApprovedGc');

            Route::post('barcode-submission-{id}', [SpecialExternalGcRequestController::class, 'barcodeSubmission'])->name('barcode');
            Route::post('gc-review-{id}', [SpecialExternalGcRequestController::class, 'gcReview'])->name('gcreview');

            Route::get('gc-reprint-{id}', [SpecialExternalGcRequestController::class, 'reprint'])->name('reprint');
        });

        Route::prefix('reviewed-gc')->name('reviewed.gc.')->group(function () {
            Route::get('review-index', [IadController::class, 'reviewedGcIndex'])->name('special.review');
            Route::get('review-datails-{id}', [IadController::class, 'reviewDetails'])->name('details');
        });
        Route::get('details-{id}', [IadController::class, 'details'])->name('details');
        Route::put('approve-budget-{id}', [IadController::class, 'approveBudget'])->name('approve');

        Route::name('audit.')->group(function () {
            Route::get('audit-store', [IadController::class, 'auditStore'])->name('store');
            Route::get('audit-store-generate', [IadController::class, 'auditStoreGenerate'])->name('generate');
        });
        Route::name('versoldused.')->group(function () {
            Route::get('verified-sold-used', [IadController::class, 'verifiedSoldUsed'])->name('index');
            Route::get('get-verified-{barcode}', [IadController::class, 'verifiedDetails'])->name('verified');
            Route::get('get-verifieds-{barcode}', [IadController::class, 'verifiedsDetails'])->name('verifieds');
            Route::get('get-transaction-txt-{barcode}', [IadController::class, 'transactionTxtDetails'])->name('transaction');
        });
        Route::name('excel.')->group(function () {
            Route::get('verified', [IadController::class, 'verifiedReports'])->name('verified');
            Route::get('purchased', [IadController::class, 'purchasedReports'])->name('purchased');

            Route::name('generate.')->group(function () {
                Route::get('generate-verified', [IadController::class, 'generateVerifiedReports'])->name('verified');
                Route::get('generate-purchased', [IadController::class, 'generatePurchasedReports'])->name('purchased');
            });
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
    });
});

Route::prefix('coupon')->group(function () {
    Route::name('treasury.')->group(function () {
        Route::name('coupon.')->group(function () {
            Route::get('coupon-transaction', [CouponController::class, 'couponIndex'])->name('transactions.special.index');
            Route::post('coupon-submit', [CouponController::class, 'submit'])->name('submit');
        });
    });
});

Route::get('file-search', [ProcessFiles::class, 'barcodeSearch']);

Route::get('generate-barcode', [CustodianController::class, 'generateBarcode'])->name('generaate');

//Store Accounting
Route::prefix('store-accounting')
    ->group(
        function () {
            Route::name('storeaccounting.')
                ->group(
                    function () {

                        Route::get('storeaccouting-storeeod-{id}', [StoreAccountingController::class, 'storeeod'])->name('storeeod');
                        Route::get('store-accounting.GCNavisionPOSTransactions-{barcode}', [StoreAccountingController::class, 'GCNavisionPOSTtransactions'])->name('GCNavisionPOSTransactions');

                        Route::get('storeaccouting-sales', [StoreAccountingController::class, 'storeAccoutingSales'])->name('sales');
                        Route::get('store-accounting-view-sales-{id}', [StoreAccountingController::class, 'storeaccountingViewSales'])->name('storeAccountingViewSales');
                        Route::get('view-sales-{barcode}', [StoreAccountingController::class, 'viewSalesPostTransaction'])->name('storeAccountingPOStransaction');

                        Route::get('storeaccounting-store', [StoreAccountingController::class, 'storeAccountingStore'])->name('store');
                        Route::get('store-accounting-view-store-{id}', [StoreAccountingController::class, 'storeAccountingViewStore'])->name('storeAccountingViewStore');
                        Route::get('view-modal-{barcode}', [StoreAccountingController::class, 'storeAccountingViewModalStore'])->name('storeAccountingViewModal');

                        Route::get('store-verified-alturasMall-{id}', [StoreAccountingController::class, 'storeVerifiedAlturasMall'])->name('alturasMall');
                        Route::get('alturas-pos-transaction-{barcode}', [StoreAccountingController::class, 'alturasMallPosTransaction'])->name('alturasMallPosTransaction');

                        Route::get('store-verified-alturasTalibon-{id}', [StoreAccountingController::class, 'storeVerifiedAlturasTalibon'])->name('alturasTalibon');
                        Route::get('talibon-pos-transaction-{barcode}', [StoreAccountingController::class, 'talibonPosTransaction'])->name('talibonPosTransaction');

                        Route::get('store-verified-islandCityMall-{id}', [StoreAccountingController::class, 'storeVerifiedIslandCityMall'])->name('islandCityMall');
                        Route::get('island-pos-transaction-{barcode}', [StoreAccountingController::class, 'islandCityMallPosTransaction'])->name('islandCityMallPosTransaction');

                        Route::get('store-verified-plazaMarcela-{id}', [StoreAccountingController::class, 'storeVerifiedPlazaMarcela'])->name('plazaMarcela');
                        Route::get('plaza-transaction-plazaMarcela-{barcode}', [StoreAccountingController::class, 'plazaPostTransaction'])->name('plazaMarcelaPosTransaction');

                        Route::get('store-verified-alturasTubigon-{id}', [StoreAccountingController::class, 'storeVerifiedAlturasTubigon'])->name('alturasTubigon');
                        Route::get('tubigon-transaction-alturasTubigon-{barcode}', [StoreAccountingController::class, 'tubigonTransanction'])->name('TubigonPosTransaction');

                        Route::get('store-verified-colonadeColon-{id}', [StoreAccountingController::class, 'storeVerifiedColonadeColon'])->name('colonadeColon');
                        Route::get('pos-transaction-colonadeColon-{barcode}', [StoreAccountingController::class, 'transactionColonadeColon'])->name('colonadeColonPosTransaction');

                        Route::get('store-verified-colonadeMandaue-{id}', [StoreAccountingController::class, 'storeVerifiedColonadeMandaue'])->name('colonadeMandaue');
                        Route::get('pos-transaction-colonadeMandaue-{barcode}', [StoreAccountingController::class, 'transactionColonadeMandaue'])->name('colonadeMandauePosTransaction');

                        Route::get('store-verified-altaCitta-{id}', [StoreAccountingController::class, 'storeVerifiedAltaCitta'])->name('altaCitta');
                        Route::get('pos-transaction-altaCitta-{barcode}', [StoreAccountingController::class, 'transactionAltaCitta'])->name('altaCittaPosTransaction');

                        Route::get('store-verified-farmersMarket-{id}', [StoreAccountingController::class, 'storeVerifiedFarmersMarket'])->name('farmersMarket');
                        Route::get('pos-transaction-farmersMarket-{barcode}', [StoreAccountingController::class, 'transactionFarmersMarket'])->name('farmersMarketPosTransaction');

                        Route::get('store-verified-ubayDistribution-{id}', [StoreAccountingController::class, 'storeVerifiedUbayDistribution'])->name('ubayDistribution');
                        Route::get('pos-transaction-ubayDistribution-{barcode}', [StoreAccountingController::class, 'transactionUbayDistribution'])->name('ubayDistributionPosTransaction');

                        Route::get('store-verified-screenville-{id}', [StoreAccountingController::class, 'storeVerifiedScreenville'])->name('screenville');
                        Route::get('pos-transaction-screenville-{barcode}', [StoreAccountingController::class, 'transactionScreenville'])->name('screenvillePosTransaction');

                        Route::get('store-verified-ascTech-{id}', [StoreAccountingController::class, 'storeVerifiedAscTech'])->name('ascTech');
                        Route::get('pos-transaction-ascTech-{barcode}', [StoreAccountingController::class, 'transactionAscTech'])->name('ascTechPosTransaction');

                        Route::get('verified-gc-report', [StoreAccountingController::class, 'verifiedGCReport'])->name('verifiedGCReport');
                        Route::get('verified-gc-submit', [StoreAccountingController::class, 'verifiedGcSubmit'])->name('verifiedGcSubmit');
                        Route::get('verified-yearly-submit', [StoreAccountingController::class, 'verifiedGcYearlySubmit'])->name('verifiedGcYearlySubmit');

                        Route::get('store-gc-purchased', [StoreAccountingController::class, 'storeGCPurchasedReport'])->name('storeGCPurchasedReport');
                        Route::get('store-monthly-submit', [StoreAccountingController::class, 'billingMonthlySubmit'])->name('billingMonthlySubmit');
                        Route::get('store-yearly-submit', [StoreAccountingController::class, 'billingYearlySubmit'])->name('billingYearlySubmit');



                        Route::get('redeem-report-purchased', [StoreAccountingController::class, 'redeemReport'])->name('redeemReport');
                        Route::get('redeem-monthly-submit', [StoreAccountingController::class, 'monthlyRedeemSubmit'])->name('monthlyRedeemSubmit');
                        Route::get('redeem-yearly-submit', [StoreAccountingController::class, 'yearlyRedeemSubmit'])->name('yearlyRedeemSubmit');




                        Route::get('verified-store-purchased', [StoreAccountingController::class, 'verifiedStore'])->name('verifiedStore');
                        Route::get('monthly-submit', [StoreAccountingController::class, 'puchasedMonthlySubmit'])->name('puchasedMonthlySubmit');
                        Route::get('yearly-submit', [StoreAccountingController::class, 'purchasedYearlySubmit'])->name('purchasedYearlySubmit');



                        Route::get('spgc-approved', [StoreAccountingController::class, 'SPGCApproved'])->name('SPGCApproved');
                        Route::get('generate-excel-perCustomer', [StoreAccountingController::class, 'SPGCExcel'])->name('SPGCApprovedExcel');
                        Route::get('spgc-approved-submit', [StoreAccountingController::class, 'SPGCApprovedSubmit'])->name('SPGCApprovedSubmit');
                        Route::get('spgc-approved-pdf', [StoreAccountingController::class, 'pdfApproved'])->name('pdfApprovedSubmit');
                        Route::get('spgc-approved-excel-barcode', [StoreAccountingController::class, 'excelPerBarcode'])->name('excelPerBarcode');




                        Route::get('spgc-release', [StoreAccountingController::class, 'SPGCRelease'])->name('SPGCRelease');
                        Route::get('spgc-release-submit', [StoreAccountingController::class, 'SPGCReleasedSubmit'])->name('SPGCReleasedSubmit');
                        Route::get('spgc-release-excel', [StoreAccountingController::class, 'releaseExcel'])->name('releaseExcel');
                        Route::get('spgc-release-pdf', [StoreAccountingController::class, 'releasePdf'])->name('releasePdf');



                        Route::get('duplicated-barcode', [StoreAccountingController::class, 'DuplicatedBarcodes'])->name('DuplicatedBarcodes');
                        Route::get('duplicate-barcode-excel', [StoreAccountingController::class, 'duplicateExcel'])->name('duplicateExcel');

                        Route::get('check-variance', [StoreAccountingController::class, 'CheckVariance'])->name('CheckVariance');
                        Route::get('check-variance-select', [StoreAccountingController::class, 'CheckVarianceSubmit'])->name('CheckVarianceSubmit');
                        Route::get('variance-excel', [StoreAccountingController::class, 'varianceExcelExport'])->name('varianceExcelExport');









                        Route::get('store-about-us', [StoreAccountingController::class, 'aboutUs'])->name('storeAccountingAboutUs');
                    }
                );
        }
    );

require __DIR__ . '/auth.php';
