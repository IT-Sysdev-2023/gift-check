<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\SpecialExternalGcrequest;
use App\Services\Custodian\CustodianServices;
use Illuminate\Http\Request;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianservices) {}
    public function index()
    {
        return inertia('Custodian/CustodianDashboard');
    }

    public function barcodeCheckerIndex(Request $request)
    {
        // dd($request->all());
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
        ]);

    }
    public function pendingHolderSetup(Request $request)
    {

        return inertia('Custodian/SpecialGcRequestSetup', [
            'record' => $this->custodianservices->specialExternalGcSetup($request),
        ]);
    }

}
