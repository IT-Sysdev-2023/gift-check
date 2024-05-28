<?php

namespace App\Services;

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

        // getRequestNo($link,'budgetadjustment','adj_no')

        // function getRequestNo($link,$table,$field){
        //     $query = $link->query(
        //         "SELECT 
        //             $field 
        //         FROM 
        //             $table 
        //         ORDER by 
        //             $field 
        //         DESC
        //     ");

        //     $n = $query->num_rows;
        //     if($n>0){
        //         $row = $query->fetch_assoc();
        //         $row = $row[$field];
        //         $row++;
        //         $row = sprintf("%04d", $row);
        //         return $row;

        //     } else {
        //         return '0001';
        //     }
        // }
    }

    public static function allocationAdjustment()
    {
        $query_store = Store::get(['store_id', 'store_name']);
        $query_gc_type = GcType::select('gc_type_id', 'gctype', 'gc_status')
            ->where('gc_status', '1')
            ->get();

        $denom = Denomination::where([['denom_type', 'RSGC'], ['denom_status', 'active']])
            ->orderBy('denomination')->get();

        //         $query_store = $link->query("SELECT `store_id`,`store_name` FROM `stores`");
//          $query_gc_type = $link->query("SELECT `gc_type_id`,`gctype`,`gc_status` FROM `gc_type` WHERE `gc_status`='1'"); 
    }
}