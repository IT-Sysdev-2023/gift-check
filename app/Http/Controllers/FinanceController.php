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
        $remainingBudget = LedgerBudget::currentBudget();

        return Inertia::render('Finance/BudgetLedger', [
            'filters' => $request->all('search', 'date'),
            'columns' => ColumnHelper::$budget_ledger_columns,
            'remainingBudget' => $remainingBudget,
            'dateRange' => $request->only('date'),
            'data' => LedgerBudget::filter($request->only('search', 'date'))
                ->select(
                    [
                        'bledger_id',
                        'bledger_no',
                        'bledger_trid',
                        'bledger_datetime',
                        'bledger_type',
                        'bdebit_amt',
                        'bcredit_amt'
                    ]
                )
                ->paginate(10)
                ->withQueryString()
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
