<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Models\DtiGcRequest;
use App\Models\Gc;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Custodian\CustodianServices;
use App\Services\Custodian\ReprintPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianservices, public DashboardClass $dashboardClass)
    {
    }
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
        // dd($request->all());
        return $this->custodianservices->submitSpecialExternalGc($request);
    }
    public function approvedGcRequest(Request $request)
    {
        // dd($request->all());
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
        $pending = DtiGcRequest::where('dti_status', 'pending')
            ->join('dti_gc_request_items', 'dti_gc_request_items.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->paginate()->withQueryString();
        $pending->transform(function ($item) {
            $item->totalDenom = $item->dti_denoms * $item->dti_qty;
            $item->dateRequested = Date::parse($item->dti_datereq)->format('F d, Y');
            return $item;
        });

        return inertia('Custodian/DTI/PendingSpecialGc', ['pending' => $pending]);
    }

    public function dti_special_gc_count()
    {
        $pending = DtiGcRequest::where('dti_status', 'pending')->count();

        return response()->json([
            'pending' => $pending
        ]);
    }


    public function dti_gc_holder_entry(Request $request)
    {
        $data = DtiGcRequest::where('dti_gc_requests.id', $request->id)
            ->companyName()
            ->denomination()
            ->DtiDocumments()
            ->where('dti_status', 'pending')
            ->first();
        $data->dateRequested = Date::parse($data->dti_datereq)->format('F d, Y');
        $data->validity = Date::parse($data->dti_dateneed)->format('F d, Y');
        $data->amountInWords = Number::spell($data->dti_payment);
        $data->total = number_format($data->dti_denoms * $data->dti_qty, 2);

        return inertia('Custodian/DTI/HolderEntry', [
            'data' => $data
        ]);
    }
}
