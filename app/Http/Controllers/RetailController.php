<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\ApprovedGcrequest;
use App\Models\Denomination;
use App\Models\GcLocation;
use App\Models\Store;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Models\TempReceivestore;
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
        public RetailServices $retail
    ) {}
    public function index()
    {

        $gcRequest = [
            'PendingGcRequest' => $this->retail->countGcPendingRequest()->count()
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

        DB::transaction(function () use ($request, $penumValue, $denomination, $storeAssigned) {
            StoreGcrequest::create([
                'sgc_num' => $penumValue,
                'sgc_requested_by' => $request->user()->user_id,
                'sgc_date_request' => now(),
                'sgc_date_needed' => Date::parse($request->data['dateNeed'])->format('Y-m-d'),
                'sgc_file_docno' => !is_null($request->file) ? $this->financeService->uploadFileHandler($request) : '',
                'sgc_remarks' => $request->data['remarks'],
                'sgc_status' => '0',
                'sgc_store' => $storeAssigned,
                'sgc_type' => 'regular'
            ]);

            foreach ($denomination as $key => $value) {
                StoreRequestItem::create([
                    'sri_items_denomination' => $key,
                    'sri_items_quantity' => $value,
                    'sri_items_remain' => $value,
                    'sri_items_requestid' => $request->data['sgc_id'] + 1,
                ]);
            }
        });

        return back()->with([
            'type' => 'success',
            'msg' => 'Success!',
            'description' => 'Request Saved!'
        ]);
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
        $data = $this->retail->countGcPendingRequest();
        $columns= ColumnHelper::pendingGcRequest();


        return Inertia::render('Retail/GcRequest/Pending', [
            'data' => $data,
            'columns' =>$columns
        ]);
    }
}
