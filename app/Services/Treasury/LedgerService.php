<?php

namespace App\Services\Treasury;

use App\Helpers\ColumnHelper;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use Inertia\Inertia;

class LedgerService
{
    public static function budgetLedger($dateRange) //ledger_budget.php
    {
        return LedgerBudget::filter($dateRange)->select('bledger_id', 'bledger_no', 'bledger_trid', 'bledger_datetime', 'bledger_type', 'bdebit_amt', 'bcredit_amt')->paginate(10)->withQueryString();
    }
    public static function gcLedger() // gccheckledger.php
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
            ->cursorPaginate()
            ->withQueryString();

        return $record;

    }
}
