<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Services\Custodian\BarcodeCheckerServices;
use Illuminate\Http\Request;

class CustodianController extends Controller
{
    public function __construct(public BarcodeCheckerServices $barcodeCheckerServices)
    {
    }
    public function index()
    {
        return inertia('Custodian/CustodianDashboard');
    }

    public function barcodeCheckerIndex()
    {
        return inertia('Custodian/BarcodeChecker', [
            'data' => $this->barcodeCheckerServices->barcodeChecker(),
            'columns' => ColumnHelper::$barcode_checker_columns,
            'date' => today()->toFormattedDateString(),
            'count' => [
                'regular' => $this->barcodeCheckerServices->reqularGcScannedCount(),
                'special' => $this->barcodeCheckerServices->specialExternalGcCount(),
                'total' => $this->barcodeCheckerServices->totalGcCount(),
                'today' => $this->barcodeCheckerServices->todaysCount(),
            ],
        ]);
    }

    public function scanBarcode(Request $request)
    {
        return $this->barcodeCheckerServices->scanBarcodeFn($request);
    }
}
