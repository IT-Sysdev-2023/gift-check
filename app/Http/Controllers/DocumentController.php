<?php

namespace App\Http\Controllers;

use App\Models\LedgerBudget;
use App\Services\Documents\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{

    public function startGeneratingBudgetLedger(Request $request)
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
            ->orderByDesc('bledger_no')->get();

        return (new DocumentService)
            ->record($record)
            ->writeResult();
    }
}
