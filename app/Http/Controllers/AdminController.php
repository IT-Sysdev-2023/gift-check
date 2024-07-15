<?php

namespace App\Http\Controllers;

use App\Models\Gc;
use App\Models\Promo;
use App\Models\PromoGcReleaseToItem;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AdminController extends Controller
{
    //
    public function index(Request $request)
    {

        $regular = new Gc();
        $barcodeNotFound = '';
        $steps = [];
        $transType = '';

        if ($regular->whereHas('barcode', fn (Builder $query) => $query->where('barcode_no', $request->barcode))->exists()) {
            //result regular gc
            $transType = 'Reqular Gift Check';
            $steps = self::regularGc($regular, $request);
        } elseif(SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request->barcode)->exists()) {
            //result specaial gc
            $transType = 'Special Gift Check';
            $steps = self::specialStatus($request);
        }elseif(PromoGcReleaseToItem::where('prreltoi_barcode', $request->barcode)->exists()){

        }else{
            $barcodeNotFound = 'Barcode Not Found';
        }

        return Inertia::render('Admin/AdminDashboard', [
            'data' => $steps,
            'latestStatus' => 0,
            'transType' => $transType,
            'statusBarcode' => $barcodeNotFound
        ]);
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
    public function barcodeStatus()
    {
    }

    public static function regularGc($step3, $request)
    {
        //Treasury & Marketing

        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'Marketing',
                'status' => 'finish',
                'description' => 'Request Approved'
            ]
        ]);

        if ($step3->exists()) {
            $steps->push((object) [
                'title' => 'FAD',
                'description' => 'Scanned By FAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'FAD',
                'description' => 'Not Scanned By FAD'
            ]);
        }


        if ($step3->whereHas('iadBarcode', fn (Builder $query) => $query->where('cssitem_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned By IAD'
            ]);
        }

        if ($step3->where('gc_validated', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Validated',
                'description' => 'Validated By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Not Validated',
                'description' => 'Not Validated By IAD'
            ]);
        }

        if ($step3->where('gc_allocated', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Allocated',
                'description' => 'Allocated By Treasury'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Not Allocated',
                'description' => 'Not yet Allocated By Treasury'
            ]);
        }
        if ($step3->whereHas('treasuryCfsBarcode', fn (Builder $query) => $query->where('strec_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Transfered',
                'description' => 'Transfered From Treasury To CFS'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Not Transfered',
                'description' => 'Not yet Transfered By Treasury To CFS'
            ]);
        }
        $q = $step3->join('store_received_gc', 'store_received_gc.strec_barcode', '=', 'gc.barcode_no')
            ->where('store_received_gc.strec_barcode', $request->barcode);
        $res =  $q->where('strec_sold', '*')->exists();
        if ($res) {
            $steps->push((object) [
                'title' => 'Sold',
                'description' => 'Sold to Customer'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'waiting',
                'title' => 'Sold',
                'description' => 'Not yet sold',

            ]);
        }
        $q2 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'gc.barcode_no')->where('vs_barcode', $request->barcode);

        $vs_date = $q2->first()->vs_date;
        $rev_date = $q2->first()->vs_reverifydate;
        $vs_pay = $q2->first()->vs_payto;

        if ($step3->whereHas('reverified', fn (Builder $query) => $query->where('vs_barcode', $request->barcode))->exists()) {

            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'waiting',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        if ($q2->where('vs_reverifydate', null)->exists()) {
        } else {
            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        }
        $q3 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'gc.barcode_no')->where('vs_barcode', $request->barcode);
        if ($q3->where('vs_tf_used', '*')->exists()) {
            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $vs_pay,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'wait',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }

        return $steps;
    }

    public static function specialStatus($request)
    {
        //Treasury & Marketing
        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'FAD',
                'status' => 'finish',
                'description' => 'Request Approved'
            ],
            [
                'title' => 'Finance',
                'status' => 'finish',
                'description' => 'Generated Barcode Success'
            ]
        ]);

        $special =  SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request->barcode);


        if ($special->where('spexgcemp_review', '*')->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned by IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned by IAD'
            ]);
        }
        return $steps;
    }
}
