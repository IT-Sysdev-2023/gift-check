<?php

namespace App\Services\Treasury;

use App\Models\Denomination;
use App\Models\GcType;
use App\Models\LedgerBudget;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class AdjustmentService
{
    public static function budgetAdjustment()
    {

        $adjNo = DB::table('budgetadjustment')->select('adj_no')->orderByDesc('adj_no')->get();

        return [
            'adj_no' => $adjNo->isNotEmpty() ? $adjNo : '0001',
            'date_requested' => today()->toDateString(),
            'current_budget' => LedgerBudget::currentBudget(),
            'prepared_by' => request()->user()->firstname
        ];
    }

    public static function allocationAdjustment()
    {
        $query_store = Store::get(['store_id', 'store_name']);
        $query_gc_type = GcType::select('gc_type_id', 'gctype', 'gc_status')
            ->where('gc_status', '1')
            ->get();

        $denom = Denomination::denomation();
    }
}