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


    // function currentSPGCBudget($link)
    // {
    // 	$query = "SELECT SUM(spgcledger_debit),SUM(spgcledger_credit) FROM ledger_spgc";

    // 	$query = $link->query($query) or die('unable to query');
    // 	$budget_row		= $query->fetch_array();
    // 	$debit 	= $budget_row['SUM(spgcledger_debit)'];
    // 	$credit = $budget_row['SUM(spgcledger_credit)'];

    // 	$budget = $debit - $credit;

    // 	return $budget;
    // }

    public static function currentBudget()
    {
        $query =  LedgerSpgc::select(DB::raw('SUM(spgcledger_debit) as debit'), DB::raw('SUM(spgcledger_credit) as credit'))->get();
        dd($query);

        $budget = bcsub($query->debit, $query->credit, 2);

        return NumberHelper::currency((float) $budget);
    }
}
