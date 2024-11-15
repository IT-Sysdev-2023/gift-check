<?php

namespace App\Traits\VerifiedExportsTraits;

use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreVerification;

trait VerifiedTraits
{
    //
    private function getMonthYearVerifiedGc($request)
    {
        $request = collect($request);

        $vsdata = StoreVerification::select(
            'vs_cn',
            'vs_date',
            'vs_reverifydate',
            'vs_barcode',
            'vs_tf_denomination',
            'vs_tf_purchasecredit',
            'vs_tf_balance',
            'vs_gctype',
            'vs_date',
            'vs_time',
        )
            ->with('customer:cus_id,cus_fname,cus_lname,cus_mname,cus_namext')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_date', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_date', $request['date']);
            })

            ->whereYear('vs_date', $request['date'])
            ->where('vs_store', $request['store'])
            ->get();

        $vsrevdata = StoreVerification::select(
            'vs_cn',
            'vs_date',
            'vs_reverifydate',
            'vs_barcode',
            'vs_tf_denomination',
            'vs_tf_purchasecredit',
            'vs_tf_balance',
            'vs_gctype',
            'vs_date',
            'vs_time',
        )
            ->with('customer:cus_id,cus_fname,cus_lname,cus_mname,cus_namext')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_reverifydate', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_reverifydate', $request['date']);
            })
            ->where('vs_store', $request['store'])
            ->get();


        $array  = [];

        $vsdata->transform(function ($item) use (&$array) {

            $datatxtfile = self::dataTextFile($item->vs_barcode);

            $array[] = [
                'date' => $item->vs_date->toFormattedDateString(),
                'barcode' => $item->vs_barcode,
                'denomination' => $item->vs_tf_denomination,
                'purchasecred' => !is_null($item->vs_reverifydate) ? 0 : $item->vs_tf_purchasecredit,
                'balance' =>  !is_null($item->vs_reverifydate) ? $item->vs_tf_denomination : $item->vs_tf_balance,
                'customer' => $item->customer->full_name,
                'businessunit' => $datatxtfile->bus,
                'terminalno' => $datatxtfile->tnum,
                'valid_type' =>  'VERIFIED',
                'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                'vsdate' => $item->vs_date->toFormattedDateString(),
                'vstime' => $item->vs_time,
                'purchaseamt' => $datatxtfile->puramnt,
            ];
            return $array;
        });



        $vsrevdata->transform(function ($item) use (&$array) {

            $datatxtfile = self::dataTextFile($item->vs_barcode);

            $array[] = [
                'date' => $item->vs_date->toFormattedDateString(),
                'barcode' => $item->vs_barcode,
                'denomination' => $item->vs_tf_denomination,
                'purchasecred' => $item->vs_tf_purchasecredit,
                'balance' =>  $item->vs_tf_balance,
                'customer' => $item->customer->full_name,
                'businessunit' => $datatxtfile->bus,
                'terminalno' => $datatxtfile->tnum,
                'valid_type' =>  'REVERIFIED',
                'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                'vsdate' => $item->vs_date->toFormattedDateString(),
                'vstime' => $item->vs_time,
                'purchaseamt' => $datatxtfile->puramnt,
            ];

            return $array;
        });
        return collect($array);
    }

    private static function dataTextFile($barcode)
    {

        $data = StoreEodTextfileTransaction::select(
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_credpuramt'
        )->where('seodtt_barcode', $barcode)->get();

        $puramnt = "";
        $bus = "";
        $tnum = "";

        if ($data->count() > 0) {
            if ($data->count() === 1) {
                $data->each(function ($item) use (&$puramnt, &$bus, &$tnum) {
                    $puramnt .= $item->seodtt_credpuramt;
                    $bus .= $item->seodtt_bu;
                    $tnum .= $item->seodtt_terminalno;
                });
            } else {
                $data->each(function ($item, $index) use ($data, &$puramt, &$bus, &$tnum) {

                    $separator = $index !== $data->count() - 1 ? ', ' : '';

                    $puramt .= $item->seodtt_credpuramt . $separator;
                    $bus .= $item->seodtt_bu . $separator;
                    $tnum .= $item->seodtt_terminalno . $separator;
                });
            }
        }

        return (object) [
            'puramnt' => $puramnt,
            'bus' => $bus,
            'tnum' => $tnum,
        ];
    }

    private static function gcTypeTransaction($type)
    {

        $transaction = [
            '1' => 'REGULAR',
            '3' => 'SPECIAL EXTERNAL',
            '4' => 'PROMOTIONAL GC',
            '6' => 'BEAM AND GO',
        ];

        return $transaction[$type] ?? null;
    }
}
