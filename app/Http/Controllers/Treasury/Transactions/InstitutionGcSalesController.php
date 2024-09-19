<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Models\Assignatory;

use App\Models\InstitutCustomer;
use App\Models\InstitutTransaction;
use App\Models\PaymentFund;
use App\Services\Treasury\Transactions\InstitutionGcSalesService;
use Illuminate\Http\Request;

class InstitutionGcSalesController extends Controller
{
   public function __construct(public InstitutionGcSalesService $institutionGcSalesService)
   {}
    public function index(Request $request)
    {
        $record = $this->institutionGcSalesService->index($request);

        return inertia('Treasury/Transactions/InstitutionGcSales/InstitutionSalesIndex', [
            'title' => 'Institution Gc Sales',
            'customer' => $record->customer,
            'paymentFund' => $record->paymentFund,
            'checkBy' => Assignatory::assignatories($request),
            'releasingNo' => $record->trNo,
            'scannedBc' => $record->scannedBarcode,
            'totalScannedDenomination' => collect($record->sessionBarcode)->sum('denomination'), 
            'filters' => $request->only('date', 'search')
        ]);
    }

    public function scanBarcode(Request $request)
    {
        return $this->institutionGcSalesService->barcodeScanning($request);
    }

    public function removeBarcode(Request $request, $barcode){
        return $this->institutionGcSalesService->destroyBarcode($request, $barcode);
    }

    public function formSubmission(Request $request){
        return $this->institutionGcSalesService->store($request);
    }

    public function viewTransaction(){
        return inertia('Treasury/Dashboard/InstitutionGcSales', [
            'title' => 'Institution Gc Sales Transactions'
        ]);
    }
}
