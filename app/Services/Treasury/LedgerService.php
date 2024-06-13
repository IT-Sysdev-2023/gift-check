<?php

namespace App\Services\Treasury;

use App\Helpers\ColumnHelper;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\GcLedgerResource;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\LedgerSpgc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LedgerService
{

    public function __construct() {
    }
    public function budgetLedger(Request $request) //ledger_budget.php
    {
        $record =  LedgerBudget::with('approvedGcRequest.storeGcRequest.store:store_id,store_name')
        ->filter($request->only('search', 'date'))
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
        ->withQueryString();

        return Inertia::render('Ledger', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => ColumnHelper::$budget_ledger_columns,
        ]);
    }
    public function gcLedger(Request $request) // gccheckledger.php
    {

        $record = LedgerCheck::with('user:user_id,firstname,lastname')
            ->select(
                'cledger_id',
                'c_posted_by',
                'cledger_no',
                'cledger_datetime',
                'cledger_type',
                'cledger_desc',
                'cdebit_amt',
                'ccredit_amt',
                'c_posted_by'
            )
            ->orderBy('cledger_id')
            ->paginate(10)
            ->withQueryString();

            return Inertia::render('Ledger', [
                'filters' => $request->all('search', 'date'),
                'remainingBudget' => LedgerBudget::currentBudget(),
                'data' => GcLedgerResource::collection($record),
                'columns' => ColumnHelper::$gc_ledger_columns,
            ]);

    }
    public static function spgcLedger($filters)
    {
        return LedgerSpgc::select(
            'spgcledger_id',
            'spgcledger_no',
            'spgcledger_trid',
            'spgcledger_datetime',
            'spgcledger_type',
            'spgcledger_debit',
            'spgcledger_credit'
        )->filter($filters)
        ->paginate(10)->withQueryString();
    }
}
