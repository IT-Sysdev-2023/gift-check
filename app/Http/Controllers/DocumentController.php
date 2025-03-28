<?php

namespace App\Http\Controllers;

use App\Models\LedgerBudget;
use App\Services\Documents\DocumentBudgetLedgerService;
use App\Services\Documents\DocumentService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            ->orderBy('bledger_no', 'ASC')->get();


        $save = (new DocumentBudgetLedgerService)
            ->record($record)
            ->date($request->date)
            ->writeResult()
            ->save();

        return Inertia::render('Documents/BudgetLedgerResult', [
            'filePath' => $save,
        ]);
    }
}
