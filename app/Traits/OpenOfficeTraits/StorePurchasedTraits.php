<?php

namespace App\Traits\OpenOfficeTraits;

use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreLocalServer;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;

trait StorePurchasedTraits
{
    protected $header;
    protected $sheet;
    protected $spreadsheet;
    protected $request;


    public function initializeSpreadsheet()
    {
        $this->header = [
            "DATE PURCHASED",
            "BARCODE",
            "DENOMINATION",
            "AMOUNT REDEEM",
            "BALANCE",
            "CUSTOMER NAME",
            "STORE PURCHASED",
            "TRANSACTION #",
            "STORE REDEEM",
            "TERMINAL #",
            "VALIDATION",
            "GC TYPE",
            "GC DATE VERIFIED"
        ];

        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }
    public function header()
    {
        foreach ($this->header as $key => $header) {
            // dd($key);
            $this->sheet->setCellValueByColumnAndRow($key + 1, 1, $header); // Row 1 for headers
        }

        return $this;
    }
    public function record($request)
    {
        $this->request = collect($request);
        return $this;
    }
    public function data()
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

        $data =  $this->getDataInStoreVerEodTextFiles($query, $request, $local);
        // dd($data);

        $rowIndex = 2;

        foreach ($data as $row) {
            foreach (collect($row)->values() as $colIndex => $value) {
                $cellCoordinate = $this->sheet->getCellByColumnAndRow($colIndex + 1, $rowIndex)->getCoordinate();

                // Set cell value
                $this->sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $value);

                // Apply font styles
                $this->sheet->getStyle($cellCoordinate)->getFont()->applyFromArray([ // Font family
                    'size' => 10,      // Font size
                ]);
            }
            $rowIndex++;
        }

        $lastColumn = $this->sheet->getHighestColumn(); // E.g., 'C'

        $this->sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);

        $highestColumnIndex = Coordinate::columnIndexFromString($lastColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $this->sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }



        return $this;
    }
    public function save()
    {
        // dd($this->spreadsheet);
        $fileName = 'user_data.xlsx';
        $filePath = storage_path($fileName);

        // Save the spreadsheet as ODS file
        $writer = new Ods($this->spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend();
    }

    public function getLocalServerData($stlocal)
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

    public function getDataInStoreVerEodTextFiles($query, $request, $local)
    {
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



        $data->transform(function ($row) use (&$query) {

            $purchasecred = 0;
            $balance = 0;

            if ($row->vs_reverifydate !== '') {

                $purchasecred = 0;
                $balance = $row->vs_tf_denomination;
                
                $vsdate = $row->vs_date;
                $vstime = $row->vs_time;

            } else {

                $bdata  = $this->getSeodTransaction($query, $row->sales_barcode);

                $purchasecred = $row->vs_tf_purchasecredit;

                $balance = $row->vs_tf_balance;
                $vsdate = $row->vs_date;
                $vstime = $row->vs_time;
            }

            return (object) [
                'date'          =>  $row->vs_date,
                'barcode'       =>   (string) $row->sales_barcode,
                'denomination'  =>  $row->vs_tf_denomination,
                'purchasecred'  =>  $purchasecred,
                'balance'       =>  $balance,
                'fullname' => ucfirst($row->cus_fname) . ' ' . ucfirst($row->cus_lname) . ', ' . ucfirst($row->cus_mname) . ' ' . ucfirst($row->cus_namext),
                'storepurchased' => $this->storePurchasedSwitchCase($row->trans_store),
                'trans_number'  =>  $row->seodtt_transno,
                'busnessunited' => $this->businessUnitSwitchCase($row->seodtt_bu),
                'terminalno'    =>  $bdata->tnum ?? '',
                'valid_type'    =>  'VERIFIED',
                'gc_type'       =>  self::gcTypeTransaction($row->vs_gctype),
                'datetime'        =>  $vsdate . ':' . $vstime,
            ];
        });

        return $data;
    }



    private function getSeodTransaction($query, $sbarcode)
    {

        $seod = $query->select(
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_credpuramt',
            'seodtt_barcode'
        )->get();

        $collected = collect($seod)->where('seodtt_barcode', $sbarcode);

        $puramnt = "";
        $bus = "";
        $tnum = "";

        if ($collected->count() > 0) {
            // dd();
            if ($collected->count() === 1) {
                // dd();
                $collected->each(function ($item) use (&$puramnt, &$bus, &$tnum) {
                    $puramnt .= $item->seodtt_credpuramt;
                    $bus .= $item->seodtt_bu;
                    $tnum .= $item->seodtt_terminalno;
                });
            } else {
                $collected->each(function ($item, $index) use ($collected, &$puramt, &$bus, &$tnum) {

                    $separator = $index !== $collected->count() - 1 ? ', ' : '';

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
}
