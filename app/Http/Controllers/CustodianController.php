<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Services\Custodian\CustodianServices;
use Illuminate\Http\Request;

class CustodianController extends Controller
{
    public function __construct(public CustodianServices $custodianServices)
    {
    }
    public function index()
    {
        return inertia('Custodian/CustodianDashboard');
    }

    public function barcodeCheckerIndex()
    {
        return inertia('Custodian/BarcodeChecker', [
            'data' => $this->custodianServices->barcodeChecker(),
            'columns' => ColumnHelper::$barcode_checker_columns,
            'date' => today()->toFormattedDateString(),
            'count' => [
                'regular' => $this->custodianServices->reqularGcScannedCount(),
                'special' => $this->custodianServices->specialExternalGcCount(),
                'total' => $this->custodianServices->totalGcCount(),
                'today' => $this->custodianServices->todaysCount(),
            ],
        ]);
    }

    public function scanBarcode(Request $request)
    {
        return $this->custodianServices->scanBarcodeFn($request);
    }

    public function receivedGcIndex()
    {
        return inertia('Custodian/ReceivedGc', [
            'record' => $this->custodianServices->receivedGcIndex(),
        ]);
    }
}
