<?php

namespace App\Http\Controllers\Treasury;

use App\Helpers\NumberHelper;
use App\Http\Resources\GcResource;
use App\Models\AllocationAdjustment;
use App\Models\AllocationAdjustmentItem;
use App\Models\BudgetAdjustment;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\GcType;
use App\Models\LedgerBudget;
use App\Models\Store;
use App\Rules\DenomQty;
use App\Services\Treasury\AdjustmentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    public function __construct(public AdjustmentService $adjustmentService)
    {
    }
    public function budgetAdjustment()
    {
        return AdjustmentService::budgetAdjustment();
    }

    public function allocationAdjustment()
    {
        return AdjustmentService::allocationAdjustment();
    }

    public function viewAllocationAdjustment(Request $request, $id)
    {
        return AdjustmentService::viewAllocationAdjustment($request, $id);
    }
    public function budgetAdjustments(Request $request)
    {
        $adjustmentNo = DB::table('budgetadjustment')->max('adj_no');
        $adj = $adjustmentNo ? $adjustmentNo + 1 : 1;
        return inertia('Treasury/Adjustment/BudgetAdjustment', [
            'title' => 'Budget Adjustment',
            'adjustmentNo' => NumberHelper::leadingZero($adj),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'regularBudget' => LedgerBudget::regularBudget(),
            'specialBudget' => LedgerBudget::specialBudget()
        ]);
    }

    public function storeBudgetAdjustment(Request $request)
    {
        return $this->adjustmentService->storeBudgetAdjustment($request);
    }

    public function allocationSetup()
    {

        $gcTypes = GcType::select('gc_type_id as value', 'gctype as label', 'gc_status')->where('gc_status', '1')->get();
        $store = Store::select('store_id as value', 'store_name as label')->get();

        $denoms = Denomination::selectRaw("
        denomination.*, (SELECT IFNULL(COUNT(barcode_no),0) FROM gc 
                            WHERE gc.denom_id = denomination.denom_id 
                            AND gc.gc_ispromo = '' 
                            AND gc.gc_validated = '*' 
                            AND gc.gc_allocated = '' 
                        ) AS cnt")
            ->where([
                ['denomination.denom_type', '=', 'RSGC'],
                ['denomination.denom_status', '=', 'active']
            ])
            ->get();

        return inertia('Treasury/Adjustment/Allocation', [
            'stores' => $store,
            'gcTypes' => $gcTypes,
            'denoms' => $denoms,
            'title' => 'Allocation Setup'
        ]);
    }



    public function allocationSetupStore(Request $request)
    {
        $request->validate([
            "adjType" => 'required',
            "remarks" => 'required',
            'store' => 'not_in:0',
            'gcType' => 'not_in:0',
            'denomination' => ['required', 'array', new DenomQty()],
        ]);

        try {

            DB::transaction(function () use ($request) {

                $isSuccess = AllocationAdjustment::create([
                    'aadj_type' => $request->adjType,
                    'aadj_by' => $request->user()->user_id,
                    'aadj_datetime' => now(),
                    'aadj_remark' => $request->remarks,
                    'aadj_loc' => $request->store,
                    'aadj_gctype' => $request->gcType
                ]);

                if ($isSuccess->wasRecentlyCreated) {
                    $denom = collect($request->denomination);
                    if ($request->adjType == 'n') {
                        $denom->each(function ($item, $key) use ($request, $isSuccess) {

                            $r = Gc::whereHas('gcLocation', function ($q) use ($request) {
                                $q->where([['loc_store_id', $request->store], ['loc_gc_type', $request->gcType]]);
                            })->where([['gc_allocated', '*'], ['gc_released', ''], ['denom_id', $item['denom_id']]])->orderByDesc('barcode_no')->limit($item['qty'])->get();

                            $r->each(function ($q) use ($isSuccess) {
                                $barcode = $q->barcode_no;

                                AllocationAdjustmentItem::create([
                                    'aadji_aadj_id' => $isSuccess->aadj_id,
                                    'aadji_barcode' => $barcode,
                                ]);

                                GcLocation::where('loc_barcode_no', $barcode)->delete();
                                Gc::where('barcode_no', $barcode)->update(['gc_allocated' => '']);
                            });
                        });

                    } else {
                        $denom->each(function ($item, $key) use ($request, $isSuccess) {
                            $gc = Gc::where([['gc_validated', '*'], ['denom_id', $item['denom_id'], ['gc_allocated', ''], ['gc_ispromo', ''], ['gc_cancelled', '']]])
                                ->orderBy('gc_id')->limit($item['qty'])->get();

                            $gc->each(function ($q) use ($isSuccess, $request) {
                                $barcode = $q->barcode_no;

                                AllocationAdjustmentItem::create([
                                    'aadji_aadj_id' => $isSuccess->aadj_id,
                                    'aadji_barcode' => $barcode,
                                ]);

                                GcLocation::create([
                                    'loc_barcode_no' => $barcode,
                                    'loc_store_id' => $request->store,
                                    'loc_date' => now(),
                                    'loc_time' => now(),
                                    'loc_gc_type' => $request->gcType,
                                    'loc_by' => $request->user()->user_id,
                                ]);

                                Gc::where('barcode_no', $barcode)->update(['gc_allocated' => '*']);
                            });
                        });
                    }
                }

            });

            return redirect()->back()->with('success', 'Successfully Submitted');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
