<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class FinanceController extends Controller
{
    public function budgetLedger(Request $request)
    {
        $data = LedgerService::budgetLedger($request->all());

        $data->transform(function ($item) {
            $item->bledger_datetime = Date::parse($item->bledger_datetime)->toFormattedDateString();
            if ($item->bledger_type == 'RFBR') {
                $item->transactionType = 'Budget Entry';
            } elseif ($item->bledger_type == 'RFGCP') {
                $item->transactionType = 'GC';
            } elseif ($item->bledger_type == 'RFGCSEGC') {
                $item->transactionType = 'Special External GC Request';
            } elseif ($item->bledger_type == 'RFGCPROM') {
                $item->transactionType = 'Promo GC Request';
            } elseif ($item->bledger_type == 'RFGCPROM') {
                $item->transactionType = 'Promo GC Releasing';
            } elseif ($item->bledger_type == 'GCSR') {

                $data = ApprovedGcrequest::join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                    ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                    ->where('approved_gcrequest.agcr_id', $item->bledger_trid)
                    ->select('stores.store_name')
                    ->first();

                $item->transactionType = 'GC Releasing - ' . $data->store_name;
            } elseif ($item->bledger_type == 'RFGCSEGCREL') {
                $item->transactionType = 'Special External GC Releasing';
            } elseif ($item->bledger_type == 'RC') {
                $item->transactionType = 'Requisition Cancelled';
            } elseif ($item->bledger_type == 'GCRELINS') {
                $item->transactionType = 'Institution GC Releasing';
            }
            return $item;
        });

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
        $data = LedgerService::spgcLedger();

        $data->transform(function ($item) {
            if ($item->spgcledger_type == 'RFGCSEGC') {
                $item->transactionType = 'Special External GC Request(PROMOTIONAL)';
            } elseif ($item->spgcledger_type == 'RFGCSEGCREL') {
                $item->transactionType = 'Special External GC Releasing(PROMOTIONAL)';
            }
            return $item;
        });

        return Inertia::render('Finance/SpgcLedger', [
            'data' => $data,
            'columns' => ColumnHelper::$spgc_ledger_columns,
        ]);
    }

    public function toHash()
    {
        dd(Hash::make('123456'));
    }
}
