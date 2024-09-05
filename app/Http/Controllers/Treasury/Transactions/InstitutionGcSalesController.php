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
        $trNumber = InstitutTransaction::where('institutr_trtype', 'sales')->max('institutr_trnum') + 1;

        $customer = InstitutCustomer::select(
            'ins_name as label',
            'ins_date_created as date',
            'ins_id as value'
        )
            ->where('ins_status', 'active')->orderByDesc('ins_date_created')->get();

        $paymentFund = PaymentFund::select(
            'pay_id as value',
            'pay_desc as label',
            'pay_dateadded as date'
        )->where('pay_status', 'active')->orderByDesc('pay_dateadded')->get();

        $sessionBarcode = $request->session()->get('scanForReleasedCustomerGC');
        $scannedBarcode = ArrayHelper::paginate($sessionBarcode, 5) ?? [];

        // dd($sessionBarcode);
        return inertia('Treasury/Transactions/InstitutionGcSales/InstitutionSalesIndex', [
            'title' => 'Institution Gc Sales',
            'customer' => $customer,
            'paymentFund' => $paymentFund,
            'checkBy' => Assignatory::assignatories($request),
            'releasingNo' => $trNumber,
            'scannedBc' => $scannedBarcode,
            'totalScannedDenomination' => collect($sessionBarcode)->sum('denomination'), 
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
}
