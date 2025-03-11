<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\Gc;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Custodian\CustodianServices;
use App\Services\Custodian\ReprintPdf;
use App\Services\CustodianDtiServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianservices, public DashboardClass $dashboardClass, public CustodianDtiServices $custodianDtiServices) {}
    public function index()
    {
        return inertia('Custodian/CustodianDashboard', [
            'count' => $this->dashboardClass->custodianDashboard(),
            'denom' => $this->dashboardClass->custodianDashboardGetDenom(),
        ]);
    }

    public function barcodeCheckerIndex(Request $request)
    {
        return inertia('Custodian/BarcodeChecker', [
            'columns' => ColumnHelper::$barcode_checker_columns,
            'search' => $this->custodianservices->searchBarcode($request),
            'data' => $this->custodianservices->barcodeChecker(),
            'date' => today()->toFormattedDateString(),
            'count' => [
                'regular' => $this->custodianservices->reqularGcScannedCount(),
                'special' => $this->custodianservices->specialExternalGcCount(),
                'total' => $this->custodianservices->totalGcCount(),
                'today' => $this->custodianservices->todaysCount(),
            ],
        ]);
    }

    public function scanBarcode(Request $request)
    {
        return $this->custodianservices->scannedBarcodeFn($request);
    }

    public function receivedGcIndex(Request $request)
    {
        return inertia('Custodian/ReceivedGc', [
            'record' => $this->custodianservices->receivedgcIndex($request),
            'columns' => ColumnHelper::$received_gc_columns
        ]);
    }

    public function pendingHolderEntry(Request $request)
    {
        return inertia('Custodian/SpecialGcRequestHolder', [
            'specExRecord' => $this->custodianservices->specialExternalGcEntry($request),
            'columns' => ColumnHelper::$special_gc_request_holder,
            'activeKey' => $request->activeKey ?? '1',
        ]);
    }
    public function pendingHolderSetup(Request $request)
    {
        return inertia('Custodian/SpecialGcRequestSetup', [
            'record' => $this->custodianservices->specialExternalGcSetup($request),
        ]);
    }
    public function submitSpecialExternalGc(Request $request)
    {
        return $this->custodianservices->submitSpecialExternalGc($request);
    }
    public function approvedGcRequest(Request $request)
    {
        return inertia('Custodian/ApprovedGcRequest', [
            'columns' => ColumnHelper::$approved_gc_column,
            'record' => $this->custodianservices->approvedGcList($request)
        ]);
    }
    public function setupApproval(Request $request)
    {
        return inertia('Custodian/SetupApproval', [
            'record' => $this->custodianservices->setupApprovalSelected($request),
            'barcodes' => $this->custodianservices->setupApprovalBarcodes($request),
        ]);
    }

    public function barcodeOrRange(Request $request)
    {
        // dd($request->all());
        if ($request->status == '1') {
            $request->validate(rules: [
                'barcode' => 'required',
            ]);
        } else {
            $request->validate(rules: [
                'barcodeStart' => 'required|lt:barcodeEnd',
                'barcodeEnd' => 'gt:barcodeStart',
            ]);
        }

        if ($request->status == '1') {
            // dd();
            $exist = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)->where('spexgcemp_barcode', $request->barcode);
        } else {

            $exist = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)
                ->whereIn('spexgcemp_barcode', [$request->barcodeStart, $request->barcodeEnd]);
        }

        if ($exist->count() == 2 || $exist->exists()) {

            return inertia('Custodian/Result/GiftCheckGenerateResult', [
                'record' => $this->custodianservices->getSpecialExternalGcRequest($request),
                'url' => 'custodian.approved.request'
            ]);
        } else {

            return back()->with([
                'status' => 'error',
                'msg' => 'Ops Barcode Not Found',
                'title' => 'Error',
            ]);
        }
    }

    public function reprintRequest($id)
    {

        return (new ReprintPdf)->reprintRequestService($id);
    }

    public function textFileUploader()
    {

        return inertia('Custodian/TextfileUploader');
    }
    public function upload(Request $request)
    {

        return $this->custodianservices->upload($request);
    }
    public function productionIndex(Request $request)
    {
        return inertia('Custodian/ProductionIndex', [
            'record' => $this->custodianservices->getProductionApproved($request),
            'column' => ColumnHelper::$production_approved_column
        ]);
    }
    public function productionApprovedDetails($id)
    {
        return $this->custodianservices->getApprovedDetails($id);
    }
    public function barcodeApprovedDetails(Request $request, $id)
    {
        return $this->custodianservices->getBarcodeApprovedDetails($request, $id);
    }
    public function getEveryBarcode(Request $request, $id)
    {
        return $this->custodianservices->getEveryBarcodeDetails($request, $id);
    }
    public function getRequisitionDetails($id)
    {
        return $this->custodianservices->getRequisitionDetailsData($id);
    }

    public function productionCancelled(Request $request)
    {
        return inertia('Custodian/Cancelled/ProductionCancelled', [
            'records' => $this->custodianservices->getCancelledViewing($request),
            'columns' => ColumnHelper::$cancelled_production_columns
        ]);
    }
    public function productionCancelledDetails($id)
    {
        return $this->custodianservices->getProductionCancelledDetails($id);
    }
    public function getAvailableGcAllocation()
    {
        return $this->custodianservices->getAvailableGcRecords();
    }
    public function getAvailableGc()
    {
        return $this->custodianservices->getAvailableGcRecords();
    }
    public function gcTracking()
    {
        return inertia('Custodian/GcTracking');
    }
    public function gcTrackingSubmition(Request $request)
    {
        return $this->custodianservices->gcTrackingSubmission($request);
    }

    public function releasedIndex(Request $request)
    {
        return inertia('Custodian/Released', [
            'records' => $this->custodianservices->fetchReleased($request)
        ]);
    }
    public function releasedDetails($id)
    {
        return inertia('Custodian/ReleasedDetailComponent', [
            'records' => $this->custodianservices->fetchReleasedDetails($id),
            'id' => $id
        ]);
    }

    public function dti_special_gc_pending()
    {
        $pending = DtiGcRequest::with('specialDtiGcrequestItemsHasMany')->where([
            ['dti_status', 'pending'],
            ['dti_addemp', 'pending'],
        ])
            ->paginate()
            ->withQueryString();


        $pending->transform(function ($item) {
            $collect = collect($item->specialDtiGcrequestItemsHasMany);

            $collect->each(function ($item) {
                $item->subtotal = $item->dti_denoms * $item->dti_qty;
                return $item;
            });
            $item->totalDenom = $collect->sum('subtotal');
            // $item->totalDenom = ;
            $item->dateRequested = Date::parse($item->dti_datereq)->format('F d, Y');
            return $item;
        });


        return inertia('Custodian/DTI/PendingSpecialGc', ['pending' => $pending]);
    }

    public function dti_special_gc_count()
    {
        // dd();
        $pending = DtiGcRequest::where([
            ['dti_status', 'pending'],
            ['dti_addemp', 'pending'],
        ])->count();

        return response()->json([
            'pending' => $pending
        ]);
    }


    public function dti_gc_holder_entry(Request $request)
    {
        $data = DtiGcRequest::with('dtiDocuments', 'specialDtiGcrequestItemsHasMany')
            ->where('dti_gc_requests.dti_num', $request->id)
            ->companyName()
            ->where('dti_status', 'pending')
            ->where('dti_addemp', 'pending')
            ->first();

        if (!$data) {
            return redirect()->route('custodian.dti_special_gcdti_special_gc_pending');
        }
        $count = 1;
        $data->specialDtiGcrequestItemsHasMany->each(function ($subitem) use (&$count) {
            $subitem->tempId = $count++;
            $subitem->subtotal = $subitem->specit_denoms * $subitem->specit_qty;
            return $subitem;
        });

        $data->dateRequested = Date::parse($data->dti_datereq)->format('F d, Y');
        $data->validity = Date::parse($data->dti_dateneed)->format('F d, Y');
        $data->total = number_format($data->dti_denoms * $data->dti_qty, 2);

        // dd($data->toArray());

        return inertia('Custodian/DTI/HolderEntry', [
            'data' => $data
        ]);
    }

    public function submit_dti_special_gc(Request $request)
    {
        $request->validate([
            'holders' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request['holders'] as $key => $value) {
                DtiBarcodes::create([
                    'dti_trid' => $request['existingData']['dti_num'],
                    'dti_denom' => $value['denom'],
                    'fname' => $value['fname'],
                    'lname' => $value['lname'],
                    'mname' => $value['mname'],
                    'extname' => $value['ext'],
                    'voucher' => $value['voucher'],
                    'bunit' => $value['bu'],
                    'address' => $value['address'],
                ]);
            }

            DtiGcRequest::where('dti_num', $request['existingData']['dti_num'])
                ->where('dti_status', 'pending')
                ->where('dti_addemp', 'pending')
                ->update([
                    'dti_empaddby' =>  $request->user()->user_id,
                    'dti_addempdate' => now(),
                    'dti_addemp' => 'done',
                ]);
        });

        return redirect()->route('custodian.dti_special_gcdti_special_gc_pending');
    }
    public function dtiApprovedGcRequest(){

        $records = $this->custodianDtiServices->getDtiApprovedRequest();

        return inertia('Custodian/DTI/Approved/DtiApprovedGcRequest', [
            'records' => $records,
        ]);
    }
    public function dtiSetupGcRequest($id){

        $data = $this->custodianDtiServices->getDataRequest($id);

        return inertia('Custodian/DTI/Setup/DtiSetupGcRequest',[
            'records' => $data->records,
            'barcodes' => $data->barcodes,
        ]);
    }

    public function barcodeOrRangeDti(Request $request)
    {
        // dd($request->barcodestart, $request->barcodeend);
        if ($request->status == '2') {
            $request->validate(rules: [
                'barcode' => 'required',
            ]);
        } else {
            $request->validate(rules: [
                'barcodestart' => 'required|lt:barcodeend',
                'barcodeend' => 'gt:barcodestart',
            ]);
        }

        if ($request->status == '2') {
            $exist = DtiBarcodes::where('dti_trid', $request->id)->where('dti_barcode', $request->barcode);
        } else {

            $exist = DtiBarcodes::where('dti_trid', $request->id)
                ->whereIn('dti_barcode', [$request->barcodestart, $request->barcodeend]);
        }

        if ($exist->count() == 2 || $exist->exists()) {
            return inertia('Custodian/Result/GiftCheckGenerateResult', [
                'record' => $this->custodianservices->getSpecialExternalGcRequestDti($request),
                'url' => 'custodian.dti.approved.index'
            ]);
        } else {
            return back()->with([
                'status' => 'error',
                'msg' => 'Ops Barcode Not Found',
                'title' => 'Error',
            ]);
        }
    }

}
