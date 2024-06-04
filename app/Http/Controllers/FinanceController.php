<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Models\LedgerBudget;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function budgetLedger(Request $request)
    {
        // dd($request->all());
        $data = LedgerService::budgetLedger($request->date);

        $remainingBudget = LedgerBudget::currentBudget();

        return Inertia::render('Finance/BudgetLedger', [
            'data' => $data,
            'columns' => ColumnHelper::$budget_ledger_columns,
            'remainingBudget' => NumberHelper::currency((float) $remainingBudget),
            'date' => $request->date
        ]);
    }

    public function spgcLedger()
    {
        // dd(1);
        return Inertia::render('Finance/SpgcLedger', [
            'columns' => ColumnHelper::$spgc_ledger_columns,
        ]);
    }
}
