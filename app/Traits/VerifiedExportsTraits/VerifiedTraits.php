<?php

namespace App\Traits\VerifiedExportsTraits;

use App\Events\VerifiedExcelReports\VerifiedExcelReports;
use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

trait VerifiedTraits
{
    //

    public function checkIfExists($requests)
    {
        return Store::where('store_id', $requests['store'])->where('has_local', 1)->exists();
    }

    public function getStoreLocalServer($requests)
    {
        return StoreLocalServer::select(
            'stlocser_ip',
            'stlocser_username',
            'stlocser_password',
            'stlocser_db'
        )->where('stlocser_storeid', $requests['store'])->first();
    }
    private function getMonthYearVerifiedGc($requests)
    {


        if ($requests['datatype'] === 'vgc') {

            if ($this->checkIfExists($requests)) {

                $storeLocServer = $this->getStoreLocalServer($requests);

                return $this->getMonthYearVerifiedGcLocalServer($requests, $storeLocServer);
            } else {
                return $this->getMonthYearVerifiedGcData($requests, 'Kanding Sheet');
            }
        }
    }
    private function getMonthYearVerifiedGcData($requests)
    {
        $request = collect($requests);

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
            // ->whereYear('vs_reverifydate', $request['date'])
            ->where('vs_store', $request['store'])
            ->get();


        $array  = [];
        $count = 1;

        $vCount = $vsdata->count();

        $vsdata->transform(function ($item) use (&$array, &$count, $vCount) {

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


            VerifiedExcelReports::dispatch('Searching for verified barcodes ' . $item->vs_barcode . ' ... ', $count++, $vCount, Auth::user());
            return $array;
        });

        $countrev = 1;

        $vsrevCount = $vsrevdata->count();



        $vsrevdata->transform(function ($item) use (&$array, &$countrev, $vsrevCount) {

            $datatxtfile = self::dataTextFile($item->vs_barcode);

            $array[] = [
                'date' => $item->vs_reverifydate->toFormattedDateString(),
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

            VerifiedExcelReports::dispatch('Searching for reverified barcodes ' . $item->vs_barcode . ' ... ', $countrev++, $vsrevCount, Auth::user());

            return $array;
        });
        // dd($array);
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

    private function dataTextFileLocServer($stlocal, $barcode)
    {

        $data = $this->getLocalServerData($stlocal)->table('store_eod_textfile_transactions')->select(
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

    private function getLocalServerData($stlocal)
    {
        $config = [
            'driver'    => 'mysql',
            'host'      => $stlocal->stlocser_ip,
            'database'  => $stlocal->stlocser_db,
            'username'  => $stlocal->stlocser_username,
            'password'  => $stlocal->stlocser_password,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ];

        DB::purge('localserver');

        config(['database.connections.localserver' => $config]);

        return DB::connection('localserver');
    }

    public function getMonthYearVerifiedGcLocalServer($requests, $stlocal)
    {

        $request = collect($requests);

        try {

            $connection = $this->getLocalServerData($stlocal);


            $vsdate = $connection->table('store_verification')
                ->select(
                    'vs_date',
                    'vs_reverifydate',
                    'vs_barcode',
                    'vs_tf_denomination',
                    'vs_tf_purchasecredit',
                    'cus_fname',
                    'cus_lname',
                    'cus_mname',
                    'cus_namext',
                    'vs_tf_balance',
                    'vs_gctype',
                    'vs_date',
                    'vs_time',
                )
                ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
                ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                    $q->whereLike('vs_date', '%' . $request['date'] . '%');
                }, function ($q) use ($request) {
                    $q->whereYear('vs_date', $request['date']);
                })
                ->where('vs_store', $request['store'])
                ->get();


            $vsrevdate = $connection->table('store_verification')
                ->select(
                    'vs_date',
                    'vs_reverifydate',
                    'vs_barcode',
                    'vs_tf_denomination',
                    'vs_tf_purchasecredit',
                    'cus_fname',
                    'cus_lname',
                    'cus_mname',
                    'cus_namext',
                    'vs_tf_balance',
                    'vs_gctype',
                    'vs_date',
                    'vs_time',
                )
                ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
                ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                    $q->whereLike('vs_reverifydate', '%' . $request['date'] . '%');
                }, function ($q) use ($request) {
                    $q->whereYear('vs_reverifydate', $request['date']);
                })
                ->where('vs_store', $request['store'])
                ->get();

            $array = [];

            $count = 1;

            $vsdate->transform(function ($item) use (&$array, $stlocal, $vsdate, &$count) {

                $datatxtfile = $this->dataTextFileLocServer($stlocal, $item->vs_barcode);

                $array[] = [
                    'date' => Date::parse($item->vs_date)->toFormattedDateString(),
                    'barcode' => $item->vs_barcode,
                    'denomination' => $item->vs_tf_denomination,
                    'purchasecred' => !is_null($item->vs_reverifydate) ? 0 : $item->vs_tf_purchasecredit,
                    'balance' =>  !is_null($item->vs_reverifydate) ? $item->vs_tf_denomination : $item->vs_tf_balance,
                    'customer' => $item->cus_fname . ', ' . $item->cus_mname . ' ' . $item->cus_lname . ' ' . $item->cus_namext,
                    'businessunit' => $datatxtfile->bus,
                    'terminalno' => $datatxtfile->tnum,
                    'valid_type' =>  'VERIFIED',
                    'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                    'vsdate' => Date::parse($item->vs_date)->toFormattedDateString(),
                    'vstime' => $item->vs_time,
                    'purchaseamt' => $datatxtfile->puramnt,
                ];

                VerifiedExcelReports::dispatch('Searching for verified barcodes ' . $item->vs_barcode . ' ... ', $count++, $vsdate->count(), Auth::user());

                return $array;
            });
            $vsrevcount = 1;

            $vsrevdate->transform(function ($item) use (&$array, $stlocal, &$vsrevcount, $vsrevdate) {

                $datatxtfile = $this->dataTextFileLocServer($stlocal, $item->vs_barcode);

                $array[] = [
                    'date' => Date::parse($item->vs_reverifydate)->toFormattedDateString(),
                    'barcode' => $item->vs_barcode,
                    'denomination' => $item->vs_tf_denomination,
                    'purchasecred' => $item->vs_tf_purchasecredit,
                    'balance' =>  $item->vs_tf_balance,
                    'customer' => $item->cus_fname . ', ' . $item->cus_mname . ' ' . $item->cus_lname . ' ' . $item->cus_namext,
                    'businessunit' => $datatxtfile->bus,
                    'terminalno' => $datatxtfile->tnum,
                    'valid_type' =>  'REVERIFIED',
                    'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                    'vsdate' => Date::parse($item->vs_date)->toFormattedDateString(),
                    'vstime' => $item->vs_time,
                    'purchaseamt' => $datatxtfile->puramnt,
                ]; 


                VerifiedExcelReports::dispatch('Searching for reverified barcodes ' . $item->vs_barcode . ' ... ', $vsrevcount++, $vsrevdate->count(), Auth::user());

                return $array;
            });

            return collect($array);
        } catch (\Exception $e) {
            // Handle connection errors
            echo "Error connecting to the server: " . $e->getMessage();
        }
    }
}
