<?php

namespace App\Services\Admin;

use App\Models\Denomination;
use App\Models\Gc;
use App\Models\PromoGcReleaseToItem;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class AdminServices
{
    public function purchaseOrderDetails()
    {
        $collect = RequisitionForm::with('requisFormDenom')->get();

        $collect->transform(function ($item) {
            $item->trans_date = Date::parse($item->trans_date)->toFormattedDateString();
            $item->pur_date = Date::parse($item->pur_date)->toFormattedDateString();
            return $item;
        });

        return $collect;
    }

    public function denomination()
    {
        return Denomination::where('denom_status', 'active')->select('denom_id', 'denom_fad_item_number', 'denomination')->get();
    }

    public function statusScanned(Request $request)
    {
        // dd(1);
        $regular = new Gc();
        $special = new SpecialExternalGcrequestEmpAssign();
        $promo = new PromoGcReleaseToItem();
        $barcodeNotFound = false;
        $empty = false;
        $steps = [];
        $transType = '';
        $success = false;


        if (
            $regular->where('barcode_no', $request->barcode)->exists()
            && !$regular->whereHas('barcodePromo', fn($query) => $query->where('barcode_no', $request->barcode))->exists()
        ) {

            $transType = 'Reqular Gift Check';
            $steps = self::regularGc($regular, $request);
            $success = true;
        } elseif ($special->where('spexgcemp_barcode', $request->barcode)->where('spexgcemp_barcode', '!=', '0')->exists()) {
            $transType = 'Special Gift Check';
            $steps = self::specialStatus($special, $request);
            $success = true;
        } elseif (PromoGcReleaseToItem::where('prreltoi_barcode', $request->barcode)->exists()) {
            $transType = 'Promo Gift Check';
            $steps = self::promoStatus($promo, $request);
            $success = true;
        } elseif (empty($request->barcode)) {
            $empty = true;
        } else {
            $barcodeNotFound = true;
        }
        // dd($steps);

        return (object) [
            'steps' => $steps,
            'transType' => $transType,
            'success' => $success,
            'barcodeNotFound' => $barcodeNotFound,
            'empty' => $empty,
        ];
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

        // dd($step3->exists());

        if ($step3->whereHas('barcode', fn($query) => $query->where('barcode_no', $request->barcode))->exists()) {
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


        if ($step3->whereHas('iadBarcode', fn($query) => $query->where('cssitem_barcode', $request->barcode))->exists()) {
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
        // dd($step3->where('barcode_no', $request->barcode)->where('gc_validated', '*')->exists());
        if ($step3->where('barcode_no', $request->barcode)->where('gc_validated', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Validated',
                'description' => 'Validated By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Validated',
                'description' => 'Not Validated By IAD'
            ]);
        }
        // $step3->where('gc_allocated', '*')
        if ($step3->where('barcode_no', $request->barcode)->where('gc_allocated', '*')->exists()) {
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
        if ($step3->whereHas('treasuryCfsBarcode', fn($query) => $query->where('strec_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Transfered',
                'description' => 'Transfered From Treasury To CFS'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Transfered',
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
                'status' => 'error',
                'title' => 'Sold',
                'description' => 'Not yet sold',

            ]);
        }
        $q2 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'gc.barcode_no')->where('vs_barcode', $request->barcode);

        $vs_date = $q2->first()->vs_date ?? null;
        $rev_date = $q2->first()->vs_reverifydate ?? null;
        $vs_pay = $q2->first()->vs_payto ?? null;

        if ($step3->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {

            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $isRevDateExists = $q2->where('barcode_no', $request->barcode)->where('vs_reverifydate', $rev_date)->exists();

        $isRevDateNull = $q2->where('barcode_no', $request->barcode)->where('vs_reverifydate', null)->exists();

        if ($isRevDateNull) {
            //no result here..
        } elseif ($isRevDateExists) {

            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
            //no result here..
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
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }

        return $steps;
    }

    public static function specialStatus($special, $request)
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

        if ($special->where('spexgcemp_barcode', $request->barcode)->where('spexgcemp_review', '*')->exists()) {
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
        $data = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')->where('vs_barcode', $request->barcode);;

        // dd($data->first()->vs_date);
        if ($special->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($data->first()->vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $rev_date = $data->first()->vs_reverifydate ?? null;

        if ($data->where('vs_barcode', $request->barcode)->whereNotNull('vs_reverifydate')->exists()) {
            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
        }
        $data2 = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')->where('vs_barcode', $request->barcode);;

        if ($data2->where('vs_barcode', $request->barcode)->where('vs_tf_used', '*')->exists()) {

            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $data2->first()->vs_payto,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }


        return $steps;
    }
    public static function promoStatus($promo, $request)
    {
        // dd(1);

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

        if ($promo->exists()) {
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


        if ($promo->whereHas('iadBarcode', fn($query) => $query->where('cssitem_barcode', $request->barcode))->exists()) {
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
        if ($promo->join('gc', 'gc.barcode_no', '=', 'prreltoi_barcode')->where('barcode_no', $request->barcode)->where('gc_ispromo', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Treasury',
                'description' => 'Scanned And Validated By Treasury'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Treasury',
                'description' => 'Not yet Scanned and Validated By Treasury'
            ]);
        }
        $query =  $promo->join('promo_gc', 'promo_gc.prom_barcode', '=', 'prreltoi_barcode')
            ->leftJoin('promo', 'promo.promo_id', '=', 'promo_gc.prom_promoid')
            ->leftJoin('promogc_released', 'prgcrel_barcode', '=', 'prreltoi_barcode')
            ->where('promo_gc.prom_barcode', $request->barcode)->first();

        // dd($query->promo_name);
        if (empty($query->promo_name)) {
            $steps->push((object) [
                'status' => 'current',
                'title' => 'Marketing',
                'description' => 'Gift Check is Available'
            ]);
        } elseif (!is_null($query->promo_name) && is_null($query->prgcrel_at)) {
            $steps->push((object) [
                'status' => 'wait',
                'title' => 'Marketing',
                'description' => 'Gift Check is Pending'
            ]);
        } else {
            $steps->push((object) [
                'title' => 'Marketing',
                'description' => 'Gift Check is Released'
            ]);
        }
        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('store_verification.vs_barcode', $request->barcode);

        if ($promo->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS at ' . Date::parse($q2->first()->vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS'
            ]);
        }

        // dd($q2->first()->vs_reverifydate);
        // dd($q2->first()->vs_reverifydate);

        $isRevDateExists = $q2->where('vs_barcode', $request->barcode);


        if ($isRevDateExists) {
            $isRev =  $isRevDateExists->whereNotNull('vs_reverifydate')->exists();
        } else {
        }

        $isRevDateNull = $q2->where('vs_barcode', $request->barcode)->where('vs_reverifydate', null)->exists();

        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('store_verification.vs_barcode', $request->barcode);

        if ($isRevDateNull) {
            //no result here..
        } elseif ($isRev) {

            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($q2->first()->vs_reverifydate)->toFormattedDateString(),
            ]);
        } else {
            //no result here..
        }
        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('vs_barcode', $request->barcode);

        if ($q2->where('vs_tf_used', '*')->exists()) {
            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $q2->first()->vs_payto,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }




        return $steps;
    }
}
