<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\ApprovedGcrequest;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function budgetLedger()
    {
        $data = LedgerService::budgetLedger();

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
            }elseif ($item->bledger_type == 'RFGCSEGCREL'){
                $item->transactionType = 'Special External GC Releasing';
            }elseif ($item->bledger_type == 'RC'){
                $item->transactionType = 'Requisition Cancelled';
            }elseif ($item->bledger_type == 'GCRELINS'){
                $item->transactionType = 'Institution GC Releasing';
            }
            return $item;
        });

        $remainingBudget = LedgerBudget::currentBudget();

        return Inertia::render('Finance/BudgetLedger', [
            'data' => $data,
            'columns' => ColumnHelper::$budget_ledger_columns,
            'remainingBudget' => intval($remainingBudget),
        ]);
    }

    public function spgcLedger()
    {
        $data = LedgerService::spgcLedger();
        // $data = LedgerSpgc::get();

        // dd($data->toArray());

        return Inertia::render('Finance/SpgcLedger', [
            'data' => $data,
            'columns' => ColumnHelper::$spgc_ledger_columns,
        ]);
    }
}
