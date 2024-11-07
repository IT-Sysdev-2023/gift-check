<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class VerifiedExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function collection()
    {
        $request = $this->requestData;

        if ($request['datatype'] === 'vgc') {

            if ($this->checkIfExists()) {

                $storeLocServer = $this->getStoreLocalServer();

                if (is_null($storeLocServer)) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Server not found',
                    ]);
                } else {
                }
            } else {
                $this->getMonthYearVerifiedGc();
            }
        }
    }

    private function checkIfExists()
    {
        return Store::where('store_id', $this->requestData['store'])->where('has_local', 1)->exists();
    }

    private function getStoreLocalServer()
    {
        return StoreLocalServer::select(
            'stlocser_ip',
            'stlocser_username',
            'stlocser_password',
            'stlocser_db'
        )->where('stlocser_storeid', $this->requestData['store'])->first();
    }
    private function getMonthYearVerifiedGc()
    {
        $request = $this->requestData;

        $data = StoreVerification::select(
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
            ->whereLike('vs_date', '%' . $request['date'] . '%')
            ->where('vs_store', $request['store'])
            ->get();

        $array  = [];

        $data->transform(function ($item) use (&$array) {

            $datatxtfile = self::dataTextFile($item->vs_barcode);
                $array[] = [
                    'date' => $item->vs_date,
                    'barcode' => $item->vs_barcode,
                    'denomination' => $item->vs_tf_denomination,
                    'purchasecred' => !is_null($item->vs_reverifydate) ? 0 : $item->vs_tf_purchasecredit,
                    'customer' => $item->customer->full_name,
                    'balance' =>  !is_null($item->vs_reverifydate) ? $item->vs_tf_denomination : $item->vs_tf_balance,
                    'valid_type' =>  'VERIFIED',
                    'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                    'businessunit' => $datatxtfile->bus,
                    'terminalno' => $datatxtfile->tnum,
                    'vsdate' => $item->vs_date,
                    'vstime' => $item->vs_time,
                    'purchaseamt' => $datatxtfile->puramnt,
                ];
                
             if(!is_null($item->vs_reverifydate)) {

                $array[] = [
                    'date' => $item->vs_date,
                    'barcode' => $item->vs_barcode,
                    'denomination' => $item->vs_tf_denomination,
                    'purchasecred' => !is_null($item->vs_reverifydate) ? 0 : $item->vs_tf_purchasecredit,
                    'customer' => $item->customer->full_name,
                    'balance' =>  !is_null($item->vs_reverifydate) ? $item->vs_tf_denomination : $item->vs_tf_balance,
                    'valid_type' =>  'REVERIFIED',
                    'gc_type' =>  self::gcTypeTransaction($item->vs_gctype),
                    'businessunit' => $datatxtfile->bus,
                    'terminalno' => $datatxtfile->tnum,
                    'vsdate' => $item->vs_date,
                    'vstime' => $item->vs_time,
                    'purchaseamt' => $datatxtfile->puramnt,
                ];
            }

            return $array;
        });
        dd($array);
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
}
