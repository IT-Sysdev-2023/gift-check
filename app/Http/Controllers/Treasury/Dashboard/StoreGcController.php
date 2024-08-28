<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Models\ApprovedGcrequest;
use App\Models\Assignatory;
use App\Models\GcRelease;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Models\TempRelease;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\GcType;
use App\Models\Store;
use App\Rules\DenomQty;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreGcController extends Controller
{
    public function __construct(
        public StoreGcRequestService $storeGcRequestService,
    ) {
    }
    public function pendingRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->pendingRequest($request);

        return inertia(
            'Treasury/Dashboard/StoreGc/StoreGcTable',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Pending Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingStoreGcRequest,
            ]

        );
    }
    public function releasedGc(Request $request)
    {
        $record = $this->storeGcRequestService->releasedGc($request);

        return inertia(
            'Treasury/Dashboard/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Released Gc',
                'data' => ApprovedGcRequestResource::collection($record),
                'columns' => ColumnHelper::$releasedStoreGcRequest,
            ]

        );
    }
    public function cancelledRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->cancelledRequest($request);
        return inertia(
            'Treasury/Dashboard/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Cancelled Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$cancelledStoreGcRequest,
            ]

        );
    }
    public function reprint($id)
    {
        $pdfContent = $this->storeGcRequestService->reprint($id);

        return response($pdfContent, 200)->header('Content-Type', 'application/pdf');
    }
    public function viewCancelledGc($id): JsonResponse
    {
        $record = $this->storeGcRequestService->viewCancelledGc($id);
        return response()->json($record);
    }

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
            ->paginate(5)
            ->withQueryString();

        return response()->json($data);
    }

    public function viewReleasingEntry(Request $request, $id)
    {

        $agr = ApprovedGcrequest::max('agcr_request_relnum');

        $relnum = $agr ? $agr + 1 : '1';

        $details = StoreGcrequest::with('store:store_id,store_name', 'user:user_id,firstname,lastname')
            ->select('sgc_id', 'sgc_requested_by', 'sgc_num', 'sgc_date_request', 'sgc_date_needed', 'sgc_file_docno', 'sgc_remarks', 'sgc_store', 'sgc_type')
            ->where('sgc_id', $id)->first();

        $rgc = StoreRequestItem::leftJoin('denomination', 'store_request_items.sri_items_denomination', '=', 'denomination.denom_id')
            ->selectRaw("
            store_request_items.sri_items_remain, 
            store_request_items.sri_items_denomination, 
            denomination.denomination, (denomination.denomination * store_request_items.sri_items_remain) AS subtotal,
            (
                SELECT COUNT(gc_location.loc_barcode_no) 
                FROM gc_location
                INNER JOIN gc ON gc.barcode_no = gc_location.loc_barcode_no
                WHERE gc_location.loc_rel = ''
                AND gc.denom_id = store_request_items.sri_items_denomination
                AND gc_location.loc_store_id = ?
            ) AS count
            ", [$details->sgc_store])->where('sri_items_requestid', $id)
            ->whereNot('store_request_items.sri_items_remain', 0)
            ->paginate()->withQueryString();

        $checkBy = Assignatory::select('assig_position', 'assig_name as label', 'assig_id as value')->where(function ($q) use ($request) {
            $q->where('assig_dept', $request->user()->usertype)
                ->orWhere('assig_dept');
        })->get();
        return response()->json([
            'rel_num' => $relnum,
            'details' => $details,
            'rgc' => $rgc,
            'checkBy' => $checkBy
        ]);

    }

    public function viewAllocatedList(Request $request, $id)
    {
        $data = GcLocation::select('loc_barcode_no', 'loc_gc_type')->with('gc:gc_id,denom_id,barcode_no,pe_entry_gc', 'gc.denomination:denom_id,denomination')
            ->where([['loc_store_id', $id], ['loc_rel', '']])
            ->filter($request)
            ->paginate(10)
            ->withQueryString();

        return response()->json($data);

    }

    public function scanSingleBarcode(Request $request)
    {
        // "relno" => 664
        // "barcode" => 1111212
        // "denid" => 3
        // "store_id" => 1
        // "reqid" => 709


        // $request->validate([
        //     "barcode" => 'required|digits:13'
        // ]);


        $remainGc = StoreRequestItem::where([
            ['sri_items_denomination', $request->denid],
            ['sri_items_requestid', $request->reqid]
        ])
            ->first('sri_items_remain');

        $scannedGc = TempRelease::where([['temp_relno', $request->reqid], ['temp_rdenom', $request->denid]])->count();

        $barcode = $request->barcode;
        $relno = $request->relno;
        $denid = $request->denid;
        $store_id = $request->store_id;
        $reqid = $request->reqid;
        if ($remainGc->sri_items_remain > $scannedGc) {

            //check Barcode existence
            $whereBarcode = Gc::where('barcode_no', $barcode);

            if ($whereBarcode->exists()) {
                if ($whereBarcode->where('denom_id', $denid)->exists()) {

                    $check = GcLocation::whereHas('gc', function ($q) use ($denid) {
                        $q->has('denomination')->where('denom_id', $denid);
                    })->where([['loc_store_id', $store_id], ['loc_barcode_no', $barcode]])->exists();
                    //check if allocated to this store
                    if ($check) {
                        //check if it is already released 
                        if (GcRelease::where('re_barcode_no', $barcode)->doesntExist()) {

                            //check if gc already scanned
                            if(TempRelease::where('temp_rbarcode', $barcode)->doesntExist()){

                                TempRelease::create([
                                    'temp_rbarcode' => $barcode, 
									    'temp_rdenom' => $denid, 
									    'temp_rdate' => now(), 
									    'temp_relno' => $relno,
									    'temp_relby' => $request->user()->user_id
                                ]);
                            }else{
                                return response()->json("Barcode Number '.$barcode.' already scanned for released. ", 400);
                            }
                        } else {
                            return response()->json("Barcode Number '.$barcode.' already released.", 400);
                        }
                    } else {
                        return response()->json("Barcode Number '.$barcode.' not found in this location.", 400);
                    }
                } else {
                    return response()->json("Please scan only .. denomination.", 400);
                }
            } else {
                return response()->json("Barcode Number {$barcode} not found.", 400);
            }

        } else {
            return response()->json('Number of GC Scanned has reached the maximum number to received.', 400);
        }
    }
}

// $query = $link->query(
// 									"INSERT INTO 
// 										`temp_release`
// 									(
// 									    `temp_rbarcode`, 
// 									    `temp_rdenom`, 
// 									    `temp_rdate`, 
// 									    `temp_relno`,
// 									    `temp_relby`
// 									) 
// 									VALUES 
// 									(
// 									    '$barcode',
// 									    '$denid',
// 									    NOW(),
// 									    '$rel_no',
// 									    '".$_SESSION['gc_id']."'
// 									)
// 								");

// 								if($query)
// 								{								
// 									$response['stat'] = 1;
// 									$response['msg'] = $barcode;
// 									// $response['msg'] = 'Barcode Number '.$barcode.' successfully scanned for release.';
// 								}
// 								else
// 								{
// 									$response['stat'] = 0;
// 									$response['msg'] = $link->query;
// 								}