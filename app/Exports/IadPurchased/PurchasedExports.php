<?php

namespace App\Exports\IadPurchased;

use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use App\Traits\VerifiedExportsTraits\VerifiedTraits;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;


class PurchasedExports implements FromView, WithEvents
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
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                foreach (range('A', $sheet->getHighestColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                ]);
            },
        ];
    }
    public function getVerificationData()
    {
        $request = collect($this->request);

        $query = [];

        $local = null;

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
                    $local = 0;
                    $query = $this->getLocalServerData($storeLocServer)->table('store_eod_textfile_transactions');
                }
            } else {
                $local = 1;
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
            ->when($local === 0, function ($query) use ($request) {
                $query->where('vs_store', $request['store']);
            }, function ($query) use ($request) {
                $query->where('trans_store', $request['store']);
            })
            ->whereRaw("stores.store_initial != SUBSTRING(store_eod_textfile_transactions.seodtt_bu, 1, 5)")
            ->orderBy('trans_sid', 'ASC')
            ->get();

            // dd($data);


        $data->transform(function ($row) use ($query) {

            dd($query->where('seodtt_barcode', 1310000000770)->get());
            $data = $query->select(
                'seodtt_bu',
                'seodtt_terminalno',
                'seodtt_credpuramt',
                'seodtt_barcode'
            )->where('seodtt_barcode', $row->sales_barcode)->get();

            return [
                'data' => $data,
                'bar' => $row->sales_barcode,
            ];
            // $purchasecred = 0;
            // $balance = 0;

            // if ($row->vs_reverifydate != '') {

            //     $purchasecred = 0;
            //     $balance = $row->vs_tf_denomination;
            // } else {

            //     $bdata = $this->getGCTextfileTR($row->sales_barcode, $query);

            //     $purchasecred = $row->vs_tf_purchasecredit;

            //     $balance = $row->vs_tf_balance;
            //     $vsdate = $row->vs_date;
            //     $vstime = $row->vs_time;
            // }

            // return (object) [
            //     'data'          =>  $bdata,
            //     'date'          =>  $row->vs_date,
            //     'barcode'       =>  $row->sales_barcode,
            //     'denomination'  =>  $row->vs_tf_denomination,
            //     'purchasecred'  =>  $purchasecred,
            //     'fullname'     =>  $row->cus_fname . ' ', $row->cus_lname . ' , ' . $row->cus_mname . ' ' .  $row->cus_namext,
            //     'balance'       =>  $balance,
            //     'valid_type'    =>  'VERIFIED',
            //     'gc_type'       =>  self::gcTypeTransaction($row->vs_gctype),
            //     // 'businessunit'  =>  $bdata->bus ?? '',
            //     // 'terminalno'    =>  $bdata->tnum ?? '',
            //     // 'vsdate'        =>  $vsdate ?? null,
            //     // 'vstime'        =>  $vstime ?? null,
            //     'seodtt_bu'     => $row->seodtt_bu,
            //     // 'purchaseamt'   =>  $bdata->puramt ?? 0,
            //     'trans_store'   =>  $row->trans_store,
            //     'vs_store'      =>  $row->vs_store,
            //     'trans_number'  =>  $row->seodtt_transno,
            //     'trans_datetime' =>  $row->trans_datetime,
            //     'storepurchased' => $this->storePurchasedSwitchCase($row->trans_store),
            //     'busnessunited' => $this->businessUnitSwitchCase($row->seodtt_bu),
            // ];
        });

        dd($data->toArray());

        return $data;
    }
    private function businessUnitSwitchCase($seod)
    {
        $type = explode("-", $seod);

        $transaction = [
            'ICM' => 'ISLAND CITY MALL',
            'PM' => 'PLAZA MARCELA',
            'ASC' => 'ALTURAS MALL',
            'TAL' => 'ALTURAS TALIBON',
            'TUB' => 'ALTURAS TUBIGON',
            'COLC' => 'COLONNADE COLON',
            'COLM' => 'COLONNADE MANDAUE',
        ];

        return $transaction[$type[0]] ?? null;
    }
    private function storePurchasedSwitchCase($type)
    {
        $transaction = [
            '1' => 'ALTURAS MALL',
            '2' => 'ALTURAS TALIBON',
            '3' => 'ISLAND CITY MALL',
            '4' => 'PLAZA MARCELA',
            '6' => 'COLONNADE COLON',
            '7' => 'COLONNADE MANDAUE',
            '8' => 'ALTA CITA',
        ];

        return $transaction[$type] ?? null;
    }
    private static function gcTypeTransaction($type)
    {
        // ALA WABALO
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

       return $query->select(
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_credpuramt',
            'seodtt_barcode'
        )->where('seodtt_barcode', $barcode)->get();

        // $puramnt = "";
        // $bus = "";
        // $tnum = "";

        if ($data->count() > 0) {
            // dd();
            if ($data->count() === 1) {
                // dd();
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


        // return (object) [
        //     'puramnt' => $puramnt,
        //     'bus' => $bus,
        //     'tnum' => $tnum,
        // ];
    }
}
