<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Models\Assignatory;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\LostGcBarcode;
use App\Models\LostGcDetail;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\Store;
use App\Models\StoreEodItem;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreGcrequest;
use App\Models\StoreReceivedGc;
use App\Models\StoreRequestItem;
use App\Models\StoreVerification;
use App\Models\TempReceivestore;
use App\Services\Admin\AdminServices;
use App\Services\Finance\FinanceService;
use App\Services\RetailStore\RetailServices;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RetailController extends Controller
{

    public function __construct(
        public FinanceService $financeService,
        public RetailServices $retail,
        public AdminServices $statusScanner,
        public DashboardClass $dashboardClass
    ) {
    }
    public function index(Request $request)
    {

        $counts = $this->dashboardClass->retailDashboard();

        $rfund = Store::where('store_id', $request->user()->store_assigned)->first();
        $gcRequest = [
            'PendingGcRequest' => $this->retail->GcPendingRequest()->count(),
            'approved' => $counts['approved'],
        ];

        $getAvailableGc = $this->retail->getAvailableGC();

        $soldGc = StoreReceivedGc::where('strec_storeid', $request->user()->store_assigned)
            ->whereNotNull('strec_sold')
            ->join('denomination', 'denomination.denom_id', '=', 'store_received_gc.strec_denom')
            ->select('denomination.denomination', DB::raw('count(*) as total'))
            ->groupBy('denomination.denom_id', 'denomination.denomination') // Group by both fields
            ->get();
        $currentStorebudget = number_format($rfund['r_fund'] - $getAvailableGc['total'], 2);


        return inertia('Retail/RetailDashboard', [
            'countGcRequest' => $gcRequest,
            'availableGc' => $getAvailableGc['denoms'],
            'soldGc' => $soldGc,
            'total' => number_format($getAvailableGc['total'], 2),
            'r_fund' => number_format($rfund['r_fund'], 2),
            'storeBudget' => $currentStorebudget
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
        $request->validate([
            'remarks' => 'required',
            'quantities' => 'required'
        ]);

        $storeBudget = $this->retail->getRevolvingFund($request);

        $hasPending = $this->retail->GcPendingRequest();

        if (!empty($hasPending->toArray())) {
            return back()->with([
                'type' => 'warning',
                'msg' => 'Warning!',
                'description' => 'You still have pending GC Request!'
            ]);
        }

        $storeAssigned = $request->user()->store_assigned;

        $penum = StoreGcrequest::where('sgc_store', $storeAssigned)
            ->orderByDesc('sgc_id')
            ->first();
        $penumValue = ($penum ? intval($penum->sgc_num) : 0) + 1;

        $denomination = collect($request['quantities'])->filter(function ($item) {
            return $item !== null;
        });

        $total = 0;

        foreach ($denomination as $key => $item) {
            $denom = Denomination::where('denom_id', $key)->max('denomination');
            $subt = $denom * $item;

            $total += $subt;
        }

        if ($total > $storeBudget) {
            return back()->with([
                'type' => 'warning',
                'msg' => 'Warning!',
                'description' => 'Total GC Request is greater than the current store budget!'
            ]);
        }

        try {
            DB::transaction(function () use ($request, $penumValue, $denomination, $storeAssigned) {
                $storeGcrequest = StoreGcrequest::create([
                    'sgc_num' => $penumValue,
                    'sgc_requested_by' => $request->user()->user_id,
                    'sgc_date_request' => now(),
                    'sgc_date_needed' => null,
                    'sgc_file_docno' => !is_null($request->file) ? $this->financeService->uploadFileHandler($request) : '',
                    'sgc_remarks' => $request['remarks'],
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
        return inertia('Retail/RetailApprovedGcRequest', [
            'columns' => ColumnHelper::$approved_gc_request,
            'record' => $this->retail->getDataApproved(),
            'data' => $this->retail->details($request),
            'assign' => Assignatory::get()
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
        return Inertia::render('Retail/GcRequest/Pending', [
            'data' => $this->retail->GcPendingRequest(),
            'columns' => ColumnHelper::pendingGcRequest()
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

        return $this->retail->submitVerify($request);

    }
    public function availableGcList(Request $request)
    {
        $denom = Denomination::where('denom_status', 'active')->get();

        $gc = StoreReceivedGc::where('strec_storeid', $request->user()->store_assigned)
            ->when($request->id !== null, function ($query) use ($request) {
                return $query->where('strec_denom', $request->id);
            })
            ->when($request->barcode !== null, function ($query) use ($request) {
                return $query->where('strec_barcode', $request->barcode);
            })
            ->join('gc_release', 'gc_release.re_barcode_no', '=', 'store_received_gc.strec_barcode')
            ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'gc_release.rel_storegcreq_id')
            ->join('denomination', 'store_received_gc.strec_denom', '=', 'denomination.denom_id')
            ->leftJoin('transaction_refund', 'transaction_refund.refund_barcode', '=', 'store_received_gc.strec_barcode')
            ->leftJoin('transaction_stores', 'transaction_stores.trans_sid', '=', 'transaction_refund.refund_trans_id')
            ->where('store_received_gc.strec_sold', '')
            ->where('store_received_gc.strec_transfer_out', '')
            ->where('store_received_gc.strec_bng_tag', '')
            ->orderByDesc('transaction_refund.refund_id')
            ->paginate(10)
            ->withQueryString();
        return Inertia::render('Retail/AvailableGcTable', [
            'denom' => $denom,
            'gc' => $gc
        ]);
    }

    public function soldGc(Request $request)
    {
        $query = StoreReceivedGc::distinct()
            ->select([
                'store_verification.vs_barcode',
                'store_received_gc.strec_barcode',
                'denomination.denomination',
                'store_verification.vs_date',
                'store_received_gc.strec_recnum',
                'transaction_stores.trans_number',
                'transaction_stores.trans_type',
                'transaction_stores.trans_datetime',
                'stores.store_name',
            ])
            ->whereAny([
                'store_received_gc.strec_barcode'
            ], 'like', '%' . $request['barcode'] . '%')
            ->join('denomination', 'store_received_gc.strec_denom', '=', 'denomination.denom_id')
            ->join('transaction_sales', 'transaction_sales.sales_barcode', '=', 'store_received_gc.strec_barcode')
            ->join('transaction_stores', 'transaction_stores.trans_sid', '=', 'transaction_sales.sales_transaction_id')
            ->leftJoin('store_verification', 'store_received_gc.strec_barcode', '=', 'store_verification.vs_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->where('store_received_gc.strec_sold', '*')
            ->where('store_received_gc.strec_return', '')
            ->where('store_received_gc.strec_storeid', $request->user()->store_assigned)
            ->where('transaction_sales.sales_item_status', '0')
            ->orderBy('transaction_stores.trans_datetime', 'DESC')
            ->paginate(10)
            ->withQueryString();

        $query->transform(function ($item) {
            $paymentTypes = [
                1 => 'Cash',
                2 => 'Credit Card',
                3 => 'AR Payment',
                5 => 'Refund',
                6 => 'Revalidation',
            ];

            $item->paymentType = $paymentTypes[$item->trans_type] ?? null;

            $item->dateSold = Date::parse($item->trans_datetime)->format('F d Y');
            return $item;
        });

        return inertia('Retail/SoldGcList', [
            'data' => $query
        ]);
    }

    public function lostGC(Request $request)
    {
        $lostGCnumber = LostGcDetail::where('lostgcd_storeid', $request->user()->store_assigned)
            ->count() + 1 ?? 1;
        $lostgc = LostGcBarcode::select('lostgcb_barcode', 'lostgcb_denom')
            ->whereAny([
            ], 'like', $request->q . '%')
            ->get();

        return inertia('Retail/LostGc', [
            'lostGCnumber' => $lostGCnumber,
            'barcodes' => $lostgc
        ]);
    }

    public function submitLostGc(Request $request)
    {
        $lostBarcode = [];

        $request->validate([
            'dateLost' => 'required',
            'owner' => 'required',
            'address' => 'required',
            'contact' => 'required|numeric',
            'remarks' => 'required',
            'lostbarcode' => 'required|numeric',
        ]);

        $checkifexist = LostGcBarcode::where('lostgcb_barcode', $request['lostbarcode'])->exists();
        $reggc = Gc::where('barcode_no', $request['lostbarcode'])->exists();
        $spgc = SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request['lostbarcode'])->exists();

        if ($checkifexist) {
            return back()->with([
                'msg' => "Warning",
                'description' => "Barcode Already Added to the Lost Barcode Table",
                'type' => "warning",
            ]);
        } elseif ($reggc) {
            $reggc = Gc::where('barcode_no', $request['lostbarcode'])
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->first();
            $lostBarcode = [
                'id' => $reggc['gc_id'],
                'barcode' => $reggc['barcode_no'],
                'denom' => $reggc['denomination'],
                'type' => 'regular'
            ];
        } elseif ($spgc) {
            $gc = SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request['lostbarcode'])->first();
            $lostBarcode = [
                'id' => $gc['spexgcemp_id'],
                'barcode' => $gc['spexgcemp_barcode'],
                'denom' => $gc['spexgcemp_denom'],
                'type' => 'special'
            ];
        } else {
            return back()->with([
                'msg' => "Error",
                'description' => "Barcode Not Found",
                'type' => "error",
            ]);
        }

        DB::transaction(function () use ($request, $lostBarcode) {
            LostGcDetail::create([
                'lostgcd_repnum' => $request['lostGCnumber'],
                'lostgcd_storeid' => $request->user()->store_assigned,
                'lostgcd_owname' => $request['owner'],
                'lostgcd_address' => $request['address'],
                'lostgcd_contactnum' => $request['contact'],
                'lostgcd_datereported' => now(),
                'lostgcd_datelost' => $request['dateLost'],
                'lostcd_remarks' => $request['remarks'],
                'lostgcd_prepby' => $request->user()->user_id,
                'lostcd_type' => $lostBarcode['type'],
            ]);

            LostGcBarcode::create([
                'lostgcb_barcode' => $lostBarcode['barcode'],
                'lostgcb_denom' => $lostBarcode['denom'],
                'lostgcb_repid' => $lostBarcode['id']
            ]);
        });

        return back()->with([
            'msg' => "Success",
            'description' => "Barcode Added to Lost Barcode List",
            'type' => "success",
        ]);
    }
    public function storeEOD(Request $request)
    {
        return inertia('Retail/RetailEod', [
            'data' => $this->retail->storeEOD($request)
        ]);
    }

    public function verifiedGc(Request $request)
    {
        return inertia('Retail/VerifiedGc', [
            'data' => $this->retail->verifiedGc($request)
        ]);

    }

    public function gcdetails(Request $request)
    {

        $data = StoreEodTextfileTransaction::where('seodtt_barcode', $request->barcode)->get();
        $data->transform(function ($item) {
            $item->time = Date::parse($item->seodtt_timetrnx)->format('H:i:s: A');
            return $item;
        });
        return response()->json(['data' => $data]);
    }
    public function verified_gc_report()
    {
        return inertia('Retail/VerifiedGcReports');
    }

    public function verified_gc_generate_pdf(Request $request)
    {

        $d1 = $request['date'][0];
        $d2 = $request['date'][1];


        $data = StoreVerification::select([
                'store_verification.vs_barcode',
                DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as verby"),
                'store_verification.vs_tf_denomination',
                'store_verification.vs_tf_used',
                'store_verification.vs_gctype',
                'store_verification.vs_date',
                'store_verification.vs_reverifydate',
                'store_verification.vs_tf_balance',
            ])
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->leftJoin('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->whereBetween(DB::raw("DATE_FORMAT(store_verification.vs_date, '%Y-%m-%d')"), [$d1, $d2])
            ->where('store_verification.vs_store', $request->user()->store_assigned)
            ->get();

            $pdf = $this->retail->generate_verified_gc_pdf($request, $data,$d1,$d2);



            return back()->with([
                'stream' => base64_encode($pdf ->output())
            ]);
    }





}
