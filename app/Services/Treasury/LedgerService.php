<?php

namespace App\Services\Treasury;


use App\Helpers\Excel\ExcelWriter;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\LedgerSpgc;
use Illuminate\Http\Request;


class LedgerService extends ExcelWriter
{
    protected $record;
    protected $border;
    public function __construct()
    {
        parent::__construct();
    }
    public function budgetLedger(Request $request) //ledger_budget.php
    {
        return LedgerBudget::with('approvedGcRequest.storeGcRequest.store:store_id,store_name')
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
            ->orderByDesc('bledger_no')
            ->paginate(10)
            ->withQueryString();
    }



    public function gcLedger(Request $request) // gccheckledger.php
    {

        return LedgerCheck::with('user:user_id,firstname,lastname')
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

    public static function spgcLedgerToExcel($filters)
    {
        return LedgerSpgc::select(
            'spgcledger_id',
            'spgcledger_no',
            'spgcledger_trid',
            'spgcledger_datetime',
            'spgcledger_type',
            'spgcledger_debit',
            'spgcledger_credit'
        )->filter($filters)->get();
    }
}
