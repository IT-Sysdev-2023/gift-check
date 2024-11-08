<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponDenomination;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function couponIndex()
    {
        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        return inertia('Treasury/Coupon/CouponTransaction', [
            'trans' => $transactionNumber + 1,
            'options' => self::options(),
            'barcodeStart' => CouponDenomination::where('coup_status', 'active')->get()
        ]);
    }
    private function options()
    {
        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_type', 2)
            ->orderByDesc('spcus_id')
            ->get();
    }

    public function submit(Request $request)
    {
        dd($request->all());
    }

}
