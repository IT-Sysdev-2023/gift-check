<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\GcLedgerResource;
use App\Models\LedgerBudget;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(
        public DashboardClass $dashboardClass,
        public LedgerService $ledgerService,
        public GcProductionRequestService $gcProductionRequestService,
    ) {
    }
    public function index(Request $request)
    {
        $record = $this->dashboardClass->treasuryDashboard($request);

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
