<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\Denomination;
use App\Models\GcLocation;
use App\Models\Store;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Models\StoreVerification;
use App\Models\TempReceivestore;
use App\Services\Admin\AdminServices;
use App\Services\Finance\FinanceService;
use App\Services\RetailStore\RetailServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RetailController extends Controller
{

    public function __construct(
        public FinanceService $financeService,
        public RetailServices $retail,
        public AdminServices $statusScanner,
    ) {
    }
    public function index()
    {

        $gcRequest = [
            'PendingGcRequest' => $this->retail->GcPendingRequest()->count()
        ];
        return inertia('Retail/RetailDashboard', [
            'countGcRequest' => $gcRequest
        ]);
    }

    public function gcRequest(Request $request)
    {
        $storeAssigned = $request->user()->store_assigned;

        $requestNum = StoreGcrequest::select('sgc_num', 'sgc_id')->where('sgc_store', $storeAssigned)->orderByDesc('sgc_num')->first();
        $store = Store::where('store_id', $storeAssigned)->get();

        $requestNumber = intval($requestNum->sgc_num) + 1;

        $denoms = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();

        $allocated = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();

        $denomColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity'],
            ['denomination', 'qty']
        );


        foreach ($allocated as $denom) {

            $allocatedGc = GcLocation::join('gc', 'gc.barcode_no', '=', 'gc_location.loc_barcode_no')
                ->where('loc_rel', '')
                ->where('loc_store_id', $storeAssigned)
                ->where('denom_id', $denom->denom_id)->count();

            $denom->count = $allocatedGc;
        }




        $allocatedGcColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Allocated GC', ''],
            ['denomination', 'qty']
        );

        return Inertia::render('Retail/RequestGc', [
            'denoms' => $denoms,
            'allocated' => $allocated,
            'denomColumns' => ColumnHelper::getColumns($denomColumns),
            'requestno' => $requestNum,
            'requestNumber' => $requestNumber,
            'storeName' => $store,
            'allocatedGcColumns' => $allocatedGcColumns
        ]);
    }

    public function gcRequestsubmit(Request $request)
    {
        $storeAssigned = $request->user()->store_assigned;

        $penum = StoreGcrequest::where('sgc_store', $storeAssigned)
            ->orderByDesc('sgc_id')
            ->first();
        $penumValue = ($penum ? intval($penum->sgc_num) : 0) + 1;

        $denomination = collect($request->data['quantities'])->filter(function ($item) {
            return $item !== null;
        });

        try {
            DB::transaction(function () use ($request, $penumValue, $denomination, $storeAssigned) {
                $storeGcrequest = StoreGcrequest::create([
                    'sgc_num' => $penumValue,
                    'sgc_requested_by' => $request->user()->user_id,
                    'sgc_date_request' => now(),
                    'sgc_date_needed' => Date::parse($request->data['dateNeed'])->format('Y-m-d'),
                    'sgc_file_docno' => !is_null($request->file) ? $this->financeService->uploadFileHandler($request) : '',
                    'sgc_remarks' => $request->data['remarks'],
                    'sgc_status' => '0',
                    'sgc_store' => $storeAssigned,
                    'sgc_type' => 'regular',
                ]);

                foreach ($denomination as $key => $value) {
                    StoreRequestItem::create([
                        'sri_items_denomination' => $key,
                        'sri_items_quantity' => $value,
                        'sri_items_remain' => $value,
                        'sri_items_requestid' => $storeGcrequest->sgc_id,
                    ]);
                }
            });

            return back()->with([
                'type' => 'success',
                'msg' => 'Success!',
                'description' => 'Request Saved!'
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'type' => 'error',
                'msg' => 'An error occurred while saving the request.',
                'description' => $e->getMessage(),
            ]);
        }
    }


    public function approvedGcRequest(Request $request)
    {
        $record = $this->retail->details($request);

        return inertia('Retail/RetailApprovedGcRequest', [
            'columns' => ColumnHelper::$approved_gc_request,
            'record' => $this->retail->getDataApproved(),
            'data' => $record
        ]);
    }

    public function validateBarcode(Request $request)
    {
        return $this->retail->validateBarcode($request);
    }
    public function removeTemporary(Request $request)
    {
        TempReceivestore::where('trec_by', $request->user()->user_id)->delete();
    }
    public function pendingGcRequestList()
    {
        $data = $this->retail->GcPendingRequest();
        $columns = ColumnHelper::pendingGcRequest();
        return Inertia::render('Retail/GcRequest/Pending', [
            'data' => $data,
            'columns' => $columns
        ]);
    }
    public function submitEntry(Request $request)
    {
        return $this->retail->submitEntry($request);
    }
    public function pendingGcRequestdetail(Request $request)
    {
        $denoms = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();

        foreach ($denoms as $key => $value) {
            $denom = StoreRequestItem::where('sri_items_requestid', $request->id)->where('sri_items_denomination', $value->denom_id)->first();
            $value->quantity = $denom->sri_items_quantity ?? 0;
        }

        $denomColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity'],
            ['denomination', 'qty']
        );
        $info = StoreGcrequest::join('stores', 'store_gcrequest.sgc_store', '=', 'stores.store_id')
            ->join('users', 'users.user_id', '=', 'store_gcrequest.sgc_requested_by')
            ->where('store_gcrequest.sgc_id', $request->id)
            ->get();
        $info->transform(function ($item) {
            $item->dateRequest = Date::parse($item->sgc_date_request)->format('F-d-y');
            $item->dateNeed = Date::parse($item->sgc_date_needed)->format('F-d-y');
            $item->requestedBy = ucwords($item->firstname . ' ' . $item->lastname);
            return $item;
        });
        $barcodes = StoreRequestItem::leftJoin('denomination', 'denomination.denom_id', '=', 'store_request_items.sri_items_denomination')
            ->leftJoin('for_denom_set_up', 'for_denom_set_up.fds_denom_reqid', '=', 'store_request_items.sri_id')
            ->where('store_request_items.sri_items_requestid', $request->id)
            ->select('denomination.denom_id', 'store_request_items.sri_items_quantity', 'denomination.denomination', 'for_denom_set_up.fds_denom')
            ->get();
        $barcodeColumns = ColumnHelper::pendingGcRequestBarcode();
        return Inertia::render('Retail/GcRequest/PendingRequestDetail', [
            'details' => $info,
            'barcode' => $barcodes,
            'columns' => $barcodeColumns,
            'denoms' => $denoms,
            'denomColumns' => $denomColumns
        ]);
    }

    public function cancelRequest(Request $request)
    {
        DB::transaction(function () use ($request) {
            StoreRequestItem::where('sri_items_requestid', $request->id)->delete();
            StoreGcrequest::where('sgc_id', $request->id)->delete();
        });
        return back()->with([
            'type' => 'success',
            'msg' => 'Success!',
            'description' => 'Cancelled Successfully!'
        ]);
    }
    public function submitRequest(Request $request)
    {
        $denomination = collect($request->denom)->filter(function ($item) {
            return $item !== null;
        });

        foreach ($denomination as $key => $value) {
            if (is_null($value['quantity'])) {
                continue;
            }
            StoreRequestItem::where('sri_items_denomination', $value['id'])
                ->where('sri_items_requestid', $request->id)
                ->updateOrInsert(
                    ['sri_items_denomination' => $value['id']],
                    [
                        'sri_items_denomination' => $value['id'],
                        'sri_items_requestid' => $request->id,
                        'sri_items_quantity' => $value['quantity'],
                        'sri_items_remain' => $value['quantity']
                    ],
                );
            if ($value['quantity'] == 0) {
                StoreRequestItem::where('sri_items_denomination', $value['id'])
                    ->where('sri_items_requestid', $request->id)
                    ->delete();
            }
        }
    }

    public function verificationIndex(Request $request)
    {
        $data = $this->statusScanner->statusScanned($request);

        return inertia('Retail/Verification', [
            'data' => $data->steps,
            'success' => $data->success,
            'notfound' => $data->barcodeNotFound,
            'empty' => $data->empty,
        ]);
    }

    public function submitVerify(Request $request)
    {

       return  $this->retail->submitVerify($request);

    }

}
