<?php

namespace App\Services\Treasury;

use App\Http\Resources\AllocationAdjustmentResource;
use App\Models\AllocationAdjustment;
use App\Models\AllocationAdjustmentItem;
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

        $record = AllocationAdjustment::with('store:store_id,store_name', 'gcType:gc_type_id,gctype', 'user:user_id,firstname,lastname')
        ->select('aadj_id', 'aadj_datetime', 'aadj_type', 'aadj_remark', 'aadj_loc', 'aadj_gctype', 'aadj_by')
        ->paginate()->withQueryString();
        return inertia('Treasury/Dashboard/Adjustment/AllocationAdjustment', [
            'title' => 'Allocation Adjustment',
            'records' => AllocationAdjustmentResource::collection($record),
            'columns'=> ColumnHelper::$allocationAdjustment,
        ]);
    }

    public static function viewAllocationAdjustment(string $id)
    {
      
    $record = AllocationAdjustmentItem::with(['gc' => function ($q) {
        $q->with('denomination:denom_id,denomination')->select('barcode_no', 'denom_id');
    }])->where('aadji_aadj_id', $id)
        ->orderBy('aadji_barcode')
    ->paginate(10)->withQueryString();

    return response()->json($record);
    }
}