<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\TempValidation;
use App\Services\Iad\IadServices;
use Illuminate\Http\Request;

class IadController extends Controller
{
    public function __construct(public IadServices $iadServices) {}

    public function index()
    {
        return inertia('Iad/IadDashboard');
    }

    public function receivingIndex()
    {
        return inertia('Iad/GcReceivingIndex', [
            'record' =>  $this->iadServices->gcReceivingIndex(),
            'columns' => ColumnHelper::$receiving_columns,
        ]);
    }

    public function setupReceiving(Request $request)
    {

        $data =  $this->iadServices->setupReceivingtxt($request);

        return inertia('Iad/SetupReceiving', [
            'denomination' => $this->iadServices->getDenomination($data->requisFormDenom, $request),
            'scannedGc' => $this->iadServices->getScannedGc(),
            'columns' => ColumnHelper::$denomination_column,
            'record' => $data,
            'recnum' => $this->iadServices->getRecNum(),
            'reqid' => $request->requisId,
            'date' => today()->toFormattedDateString()
        ]);
    }

    public function validateByRange(Request $request)
    {
        return $this->iadServices->validateByRangeServices($request);
    }

    public function removeScannedGc(Request $request)
    {
        TempValidation::where('tval_barcode', $request->barcode)->delete();

        return back()->with([
            'status' => 'success',
            'title' => 'Success!',
            'msg' => 'Remove Barcode Successfully',
        ]);
    }
    public function validateBarcode(Request $request)
    {
       return $this->iadServices->validateBarcodeFunction($request);

    }

    public function submitSetup(Request $request)
    {

        return $this->iadServices->submitSetupFunction($request);
    }
}
