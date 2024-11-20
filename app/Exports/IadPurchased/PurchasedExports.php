<?php

namespace App\Exports\IadPurchased;

use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Traits\VerifiedExportsTraits\VerifiedTraits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchasedExports implements FromView
{
    use VerifiedTraits;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;

    public function __construct($requestdata)
    {
        $this->request = $requestdata;
    }
    public function view(): View
    {
        return view('excel.purchasedexcel', [
            'data' => $this->getVerificationData(),
        ]);
    }

    public function getVerificationData()
    {
        $request = collect($this->request);

        $query = [];

        $storeLocServer = $this->getStoreLocalServer();

        if ($request['datatype'] === 'vgc') {

            if ($this->checkIfExists()) {

                if (is_null($storeLocServer)) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Server not found',
                    ]);
                    // dd();
                } else {
                    $query = $this->getLocalServerData($storeLocServer)->table('store_eod_textfile_transactions');
                }
            } else {
                $query = new StoreEodTextfileTransaction();
            }
        }

        $data = $query->select(
            'vs_date',
            'vs_reverifydate',
            'vs_barcode',
            'vs_tf_denomination',
            'vs_tf_purchasecredit',
            'seodtt_bu',
            'sales_barcode',
            'trans_number',
            'trans_store',
            'seodtt_transno',
            'vs_store',
            'trans_datetime',
            'cus_fname',
            'cus_lname',
            'cus_mname',
            'cus_namext',
            'vs_tf_balance',
            'vs_gctype',
            'vs_date',
            'vs_time',
        )
            ->join('transaction_sales', 'transaction_sales.sales_barcode', '=', 'seodtt_barcode')
            ->join('transaction_stores', 'transaction_stores.trans_sid', '=', 'sales_transaction_id')
            ->join('stores', 'store_id', '=', 'trans_store')
            ->join('store_verification', 'vs_barcode', '=', 'sales_barcode')
            ->leftJoin('customers', 'cus_id', '=', 'vs_cn')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_date', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_date', $request['date']);
            })
            ->where('trans_store', $request['store'])
            ->whereRaw("stores.store_initial != SUBSTRING(store_eod_textfile_transactions.seodtt_bu, 1, 5)")
            ->orderBy('trans_sid', 'ASC')
            ->get();

        $data->transform(function ($row) use ($query) {
            $purchasecred = 0;
            $balance = 0;

            if ($row->daterev != '') {

                $purchasecred = 0;
                $balance = $row->vs_tf_denomination;

            } else {

                $bdata = $this->getGCTextfileTR($row->sales_barcode, $query);

                $purchasecred = $row->vs_tf_purchasecredit;
                $balance = $row->vs_tf_balance;
                $vsdate = $row->vs_date;
                $vstime = $row->vs_time;
            }

            return (object) [
                'date'          =>  $row->vs_date,
                'barcode'       =>  $row->sales_barcode,
                'denomination'  =>  $row->vs_tf_denomination,
                'purchasecred'  =>  $purchasecred,
                'cus_fname'     =>  $row->cus_fname,
                'cus_lname'     =>  $row->cus_lname,
                'cus_mname'     =>  $row->cus_mname,
                'cus_namext'    =>  $row->cus_namext,
                'balance'       =>  $balance,
                'valid_type'    =>  'VERIFIED',
                'gc_type'       =>  self::gcTypeTransaction($row->vs_gctype),
                'businessunit'  =>  $bdata->bus,
                'terminalno'    =>  $bdata->tnum,
                'vsdate'        =>  $vsdate,
                'vstime'        =>  $vstime,
                'seodtt_bu'     => $row->seodtt_bu,
                'purchaseamt'   =>  $bdata->puramt ?? 0,
                'trans_store'   =>  $row->trans_store,
                'vs_store'      =>  $row->vs_store,
                'trans_number'  =>  $row->seodtt_transno,
                'trans_datetime' =>  $row->trans_datetime
            ];
        });

        return $data;
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
    public function checkIfExists()
    {
        return Store::where('store_id', $this->request['store'])->where('has_local', 1)->exists();
    }

    public function getStoreLocalServer()
    {
        return StoreLocalServer::select(
            'stlocser_ip',
            'stlocser_username',
            'stlocser_password',
            'stlocser_db'
        )->where('stlocser_storeid', $this->request['store'])->first();
    }
    public function getGCTextfileTR($barcode, $query)
    {

        $data = $query->select(
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
