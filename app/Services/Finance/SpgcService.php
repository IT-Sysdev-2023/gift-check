<?php

namespace App\Services\Finance;

use App\Helpers\NumberHelper;
use App\Models\LedgerSpgc;
use Illuminate\Support\Facades\DB;

class SpgcService
{

    public static function operatorsFn()
    {
        $query =  LedgerSpgc::select(DB::raw('SUM(spgcledger_debit) as debit'), DB::raw('SUM(spgcledger_credit) as credit'))->get();
        $budget = 0;

        $query->each(function ($item) use (&$budget) {
            $budget = bcsub($item->debit, $item->credit, 2);
        });
        return NumberHelper::currency((float) $budget);
    }

    public static function currentBudget()
    {
        $query =  LedgerSpgc::select(DB::raw('SUM(spgcledger_debit) as debit'), DB::raw('SUM(spgcledger_credit) as credit'))->get();
        dd($query);

        $budget = bcsub($query->debit, $query->credit, 2);

        return NumberHelper::currency((float) $budget);
    }
}
