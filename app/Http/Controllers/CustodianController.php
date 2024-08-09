<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Services\Custodian\CustodianServices;
use Illuminate\Http\Request;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianservices)
    {
    }
    public function index()
    {
        return inertia('Custodian/CustodianDashboard');
    }

    public function barcodeCheckerIndex()
    {
        return inertia('Custodian/BarcodeChecker', [
            'data' => $this->custodianservices->barcodeChecker(),
            'columns' => ColumnHelper::$barcode_checker_columns,
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
}
