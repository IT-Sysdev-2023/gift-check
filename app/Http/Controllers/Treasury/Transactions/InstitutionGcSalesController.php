<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Assignatory;
use App\Models\Gc;
use App\Models\InstitutCustomer;
use App\Models\InstitutTransaction;
use App\Models\PaymentFund;
use Illuminate\Http\Request;

class InstitutionGcSalesController extends Controller
{
    private string $sessionName;
    public function __construct()
    {
        $this->sessionName = 'scanForReleasedCustomerGC';
    }
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

        return inertia('Treasury/Transactions/InstitutionGcSales/InstitutionSalesIndex', [
            'title' => 'Institution Gc Sales',
            'customer' => $customer,
            'paymentFund' => $paymentFund,
            'checkBy' => Assignatory::assignatories($request),
            'releasingNo' => $trNumber,
            'filters' => $request->only('date', 'search')

        ]);
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required_if:switch,false',
            'startBarcode' => 'required_if:switch,true',
            'endBarcode' => 'required_if:switch,true',
        ]);

        $barcode = $request->barcode;

        // $gc = Gc::leftJoin('custodian_srr_items', 'custodian_srr_items.cssitem_barcode', '=', 'gc.barcode_no')
        //     ->leftJoin('promo_gc_release_to_items', 'promo_gc_release_to_items.prreltoi_barcode', '=', 'gc.barcode_no')
        //     ->leftJoin('gc_location', 'gc_location.loc_barcode_no', '=', 'gc.barcode_no')
        //     ->leftJoin('institut_transactions_items', 'institut_transactions_items.instituttritems_barcode', '=', 'gc.barcode_no')
        //     ->leftJoin('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
        //     ->select(
        //         'gc.barcode_no',
        //         'gc.gc_ispromo',
        //         'custodian_srr_items.cssitem_barcode',
        //         'promo_gc_release_to_items.prreltoi_barcode',
        //         'gc_location.loc_barcode_no',
        //         'institut_transactions_items.instituttritems_barcode',
        //         'denomination.denomination'
        //     )
        //     ->where('barcode_no', $barcode)
        //     ->get();

        //Relationships
        //      'custodianSrrItems',
        //     'promoGcReleasedToItems',
        //     'gcLocation',
        //     'institutTransactionsItem',
        //     'denomination'
        $gc = Gc::where('barcode_no', $barcode);

        $scannedBc = collect($request->session()->get($this->sessionName, []));
        if ($gc->exists()) {

            if ($gc->has('custodianSrrItems')->exists()) {
                if ($gc->has('gcLocation')->doesntExist()) {
                    if ($gc->has('promoGcReleasedToItems')->doesntExist()) {
                        if ($gc->has('institutTransactionsItem')->doesntExist()) {

                            if ($scannedBc->doesntContain(fn($item) => $item['barcode'] === $barcode)) {

                                //Define balik Gc ky null og $gc gamiton
                                $denom = Gc::where('barcode_no', $barcode)->with('denomination')->first()->denomination->denomination;
                                $request->session()->push($this->sessionName, [
                                    "barcode" => $barcode,
                                    "denomination" => $denom
                                ]);
                                return redirect()->back()->with('success', 'Succesfully Scanned for Releasing.');
                            } else {
                                return redirect()->back()->with('error', "Barcode {$barcode} already Scanned.");
                            }
                        } else {
                            return redirect()->back()->with('error', "Barcode {$barcode} already released to treasury customer.");
                        }
                    } else {
                        return redirect()->back()->with('error', "Barcode {$barcode} already released for promotional GC.");
                    }
                } else {
                    return redirect()->back()->with('error', "Barcode {$barcode} already allocated");
                }
            } else {
                return redirect()->back()->with('error', "Barcode {$barcode} needs validation");
            }
        } else {
            return redirect()->back()->with('error', "Barcode {$barcode} not Found!");
        }
    }
}
