<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Custodian\CustodianServices;
use App\Services\Custodian\ReprintPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianservices, public DashboardClass $dashboardClass) {}
    public function index()
    {
        return inertia('Custodian/CustodianDashboard', [
            'count' => $this->dashboardClass->custodianDashboard(),
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

    public function receivedGcIndex()
    {
        return inertia('Custodian/ReceivedGc', [
            'record' => $this->custodianservices->receivedgcIndex(),
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
        return  $this->custodianservices->submitSpecialExternalGc($request);
    }
    public function approvedGcRequest()
    {
        return inertia('Custodian/ApprovedGcRequest', [
            'columns' => ColumnHelper::$approved_gc_column,
            'record' => $this->custodianservices->approvedGcList()
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
        if ($request->status == '1') {
            $request->validate([
                'barcode' => 'required',
            ]);
        } else {
            $request->validate([
                'barcodeStart' => 'required|lt:barcodeEnd',
                'barcodeEnd' => 'gt:barcodeStart',
            ]);
        }

        if ($request->status == '1') {
            // dd();
            $exist =  SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)->where('spexgcemp_barcode', $request->barcode);
        } else {

            $exist =  SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)
                ->whereIn('spexgcemp_barcode', [$request->barcodeStart, $request->barcodeEnd]);
        }

        if ($exist->count() == 2 || $exist->exists()) {

            return inertia('Custodian/Result/GiftCheckGenerateResult', [
                'record' =>  $this->custodianservices->getSpecialExternalGcRequest($request),
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
}
