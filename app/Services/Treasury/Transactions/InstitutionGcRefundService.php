<?php

namespace App\Services\Treasury\Transactions;

use Illuminate\Http\Request;

class InstitutionGcRefundService
{
    public function index(Request $request){
        // $trNumber = InstitutTransaction::where('institutr_trtype', 'sales')->max('institutr_trnum') + 1;

        // $customer = InstitutCustomer::select(
        //     'ins_name as label',
        //     'ins_date_created as date',
        //     'ins_id as value'
        // )
        //     ->where('ins_status', 'active')->orderByDesc('ins_date_created')->get();

        // $paymentFund = PaymentFund::select(
        //     'pay_id as value',
        //     'pay_desc as label',
        //     'pay_dateadded as date'
        // )->where('pay_status', 'active')->orderByDesc('pay_dateadded')->get();

        // $sessionBarcode = $request->session()->get('scanForReleasedCustomerGC');
        // $scannedBarcode = ArrayHelper::paginate($sessionBarcode, 5) ?? [];

        // return (object) [
        //     'trNo' => $trNumber,
        //     'customer' => $customer,
        //     'paymentFund' => $paymentFund,
        //     'scannedBarcode' => $scannedBarcode,
        //     'sessionBarcode' => $sessionBarcode
        // ];
    }
}