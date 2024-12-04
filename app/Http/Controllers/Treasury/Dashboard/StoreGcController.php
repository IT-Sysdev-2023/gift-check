<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Helpers\ArrayHelper;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\GcResource;
use App\Models\ApprovedGcrequest;
use App\Models\Assignatory;
use App\Models\GcRelease;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use Illuminate\Support\Number;
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
            ->paginate(3)->withQueryString();

        // $checkBy = Assignatory::select('assig_position', 'assig_name as label', 'assig_id as value')->where(function ($q) use ($request) {
        //     $q->where('assig_dept', $request->user()->usertype)
        //         ->orWhere('assig_dept','1');
        // })->get();
        $checkBy = Assignatory::select('assig_position', 'assig_name as label', 'assig_id as value')->where('assig_id', 10)->get(); //Melisa Miculob only

        $rgc->transform(function ($item) {
            $item->denomination = NumberHelper::currency($item->denomination);
            $item->subtotal = NumberHelper::currency($item->subtotal);
            return $item;
        });

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
            ->paginate(8)
            ->withQueryString();
        return response()->json($data);

    }

    public function scanBarcode(Request $request)
    {
        return $this->storeGcRequestService->scanBarcode($request);
    }

    public function viewScannedBarcode(Request $request)
    {
        $scannedBc = collect($request->session()->get('scanReviewGC', []))->where('reqid', $request->id);

        $newArr = collect();
        $scannedBc->each(function ($item) use (&$newArr) {

            $gc = Gc::where('barcode_no', $item['barcode'])->value('pe_entry_gc');
            $gcLocation = GcLocation::where('loc_barcode_no', $item['barcode'])->value('loc_gc_type');
            $denomination = Denomination::where('denom_id', $item['denid'])->value('denomination');

            $type = $gcLocation == 1 ? 'Regular' : 'Special';

            $newArr[] = [
                'barcode' => $item['barcode'],
                'pro' => $gc,
                'denomination' => NumberHelper::currency($denomination),
                'type' => $type
            ];
        });

        return response()->json(ArrayHelper::paginate($newArr, 5));
    }

    public function releasingEntrySubmission(Request $request)
    {
        // dd($request->all());
        return $this->storeGcRequestService->releasingEntrySubmit($request);
    }



}

