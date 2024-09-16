<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\GcResource;
use Illuminate\Support\Facades\DB;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\GcType;
use App\Models\Store;
use App\Rules\DenomQty;

class GcAllocationController extends Controller
{
    public function gcAllocation()
    {
        $gcTypes = GcType::select('gc_type_id as value', 'gctype as label', 'gc_status')->where([['gc_status', '1'], ['gc_forallocation', '1']])->get();
        $store = Store::select('store_id as value', 'store_name as label')->where('store_status', 'active')->get();

        $denoms = Denomination::selectRaw("
        denomination.*, (SELECT IFNULL(COUNT(barcode_no),0) FROM gc 
                            WHERE gc.denom_id = denomination.denom_id 
                            AND gc.gc_ispromo = '' 
                            AND gc.gc_validated = '*' 
                            AND gc.gc_allocated = '' 
                            AND gc.gc_treasury_release = ''
                        ) AS cnt")
            ->where([
                ['denomination.denom_type', '=', 'RSGC'],
                ['denomination.denom_status', '=', 'active']
            ])
            ->get();

        return inertia('Treasury/Transactions/GcAllocation', [
            'title' => 'Gc Allocation',
            'stores' => $store,
            'gcTypes' => $gcTypes,
            'denoms' => $denoms
        ]);
    }
    public function store(Request $request)
    {

        $request->validate([
            'store' => 'not_in:0',
            'gcType' => 'not_in:0',
            'denomination' => ['required', 'array', new DenomQty()],
        ]);

        $denom = collect($request->denomination);

        $denom->each(function ($item) use ($request) {

            DB::transaction(function () use ($item, $request) {
                $gc = Gc::where([
                    ['gc_validated', '*'],
                    ['denom_id', $item['denom_id']],
                    ['gc_allocated', ''],
                    ['gc_ispromo', ''],
                    ['gc_treasury_release', '']
                ])->orderBy('gc_id')->limit($item['qty'])->get();

                $gc->each(function ($gc) use ($request) {
                    $barcode = $gc->barcode_no;

                    GcLocation::create([
                        'loc_barcode_no' => $barcode,
                        'loc_store_id' => $request->store,
                        'loc_date' => today(),
                        'loc_time' => now()->format('H:i:s'),
                        'loc_gc_type' => $request->gcType,
                        'loc_rel' => '',
                        'loc_by' => $request->user()->user_id,
                    ]);

                    Gc::where('barcode_no', $barcode)->update([
                        'gc_allocated' => '*'
                    ]);
                });
            });

        });
        return redirect()->back()->with('success', 'Success mate!');


    }

    public function storeAllocation(Request $request)
    {
        $data = Denomination::selectRaw("
        denomination.denomination, denomination.denom_id, 
            (
                SELECT COUNT(gc_location.loc_barcode_no) 
                FROM gc_location
                INNER JOIN gc ON gc.barcode_no = gc_location.loc_barcode_no
                WHERE gc.denom_id = denomination.denom_id 
                AND gc_location.loc_store_id = ?
                AND gc_location.loc_gc_type = ?
                AND gc_location.loc_rel = ''
            ) AS count
        ")
            ->where([
                ['denomination.denom_type', '=', 'RSGC'],
                ['denomination.denom_status', '=', 'active']
            ])
            ->addBinding([$request->store, $request->type], 'select')
            ->get();

        return response()->json($data);
    }

    public function viewAllocatedGc(Request $request)
    {
        $data = GcLocation::with([
            'user:user_id,firstname,lastname',
            'gcType:gc_type_id,gctype',
            'gc:gc_id,denom_id,pe_entry_gc,barcode_no' => [
                'denomination:denom_id,denomination',
                'productionRequest:pe_id,pe_num'
            ]
        ])->select('loc_by', 'loc_barcode_no', 'loc_date', 'loc_gc_type')
            ->where([['loc_store_id', $request->store], ['loc_rel', ''], ['loc_gc_type', $request->type]])
            ->filterDenomination($request)
            ->paginate(5)
            ->withQueryString();

        return response()->json($data);
    }

    public function viewForAllocationGc(Request $request)
    {
        $record = Gc::with([
            'denomination:denom_id,denomination',
            'custodianSrrItems' => fn($q) =>
                $q->select('cssitem_barcode', 'cssitem_recnum')
                    ->with([
                        'custodiaSsr' => fn($query) =>
                            $query->select('csrr_id', 'csrr_prepared_by', 'csrr_datetime')
                                ->with('user:user_id,firstname,lastname')
                    ])
        ])
            ->select('denom_id', 'barcode_no')
            ->where([['gc_validated', '*'], ['gc_allocated', ''], ['gc_ispromo', ''], ['gc_treasury_release', '']])
            ->filterDenomination($request)
            ->paginate()
            ->withQueryString();


        return response()->json([
            'data' => GcResource::collection($record->items()),
            'from' => $record->firstItem(),
            'to' => $record->lastItem(),
            'total' => $record->total(),
            'links' => $record->linkCollection(),
        ]);
    }
}
