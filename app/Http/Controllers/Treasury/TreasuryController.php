<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\DenominationResource;
use App\Http\Resources\GcLedgerResource;
use App\Http\Resources\ProductionRequestResource;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\Assignatory;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(
        public DashboardClass $dashboardClass,
        public LedgerService $ledgerService,
        public GcProductionRequestService $gcProductionRequestService,
    ) {
    }
    public function index()
    {
        $record = $this->dashboardClass->treasuryDashboard();

        return inertia('Treasury/TreasuryDashboard', ['data' => $record]);
    }
    public function budgetLedger(Request $request)
    {
        $record = $this->ledgerService->budgetLedger($request);

        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => \App\Helpers\ColumnHelper::$budget_ledger_columns,
        ]);
    }
    public function gcLedger(Request $request)
    {
        $record = $this->ledgerService->gcLedger($request);
        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => GcLedgerResource::collection($record),
            'columns' => \App\Helpers\ColumnHelper::$gc_ledger_columns,
        ]);
    }
}
