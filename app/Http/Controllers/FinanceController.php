<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Http\Resources\BudgetLedgerCollection;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\SpgcLedgerResource;
use App\Models\ApprovedGcrequest;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Services\Finance\SpgcService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class FinanceController extends Controller
{

    public function __construct(public LedgerService $ledgerService)
    {
    }

    public function index()
    {
        return Inertia::render('Finance/FinanceDashboard');
    }
    public function budgetLedger(Request $request)
    {
        return $this->ledgerService->budgetLedger($request);
    }

    public function spgcLedger(Request $request)
    {
        $data = LedgerService::spgcLedger($request->all());

        $operators = SpgcService::operatorsFn();

        return Inertia::render('Finance/SpgcLedger', [
            'data' => SpgcLedgerResource::collection($data),
            'columns' => ColumnHelper::$budget_ledger_columns,
            'operators' => $operators,
            'filters' => $request->only([
                'search',
                'date'
            ])
        ]);
    }
}
