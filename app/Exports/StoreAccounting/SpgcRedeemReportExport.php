<?php

namespace App\Exports\StoreAccounting;

use App\Events\AccountingReportEvent;
use App\Events\StoreAccountReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class SpgcRedeemReportExport extends Progress implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell
{

    public function __construct(protected $database, protected $request, protected bool $isLocal, protected User $user)
    {
        parent::__construct($user);
        $this->progress['name'] = 'Store Gc Purchased Report';

    }
    public function collection()
    {
        $data = $this->query();

        $this->progress['progress']['totalRow'] += $data->count();

        $purchasecred = 0;
        $balance = 0;
        $bus = "";
        $tnum = "";

        $transformedData = collect();

        $data->each(function ($item) use ($purchasecred, $balance, $bus, $tnum, &$transformedData) {

            $this->broadcast("Generating Report!", StoreAccountReportEvent::class);

            $gctype = match ($item->vs_gctype) {
                1 => 'REGULAR',
                3 => 'SPECIAL EXTERNAL',
                6 => 'BEAM AND GO',
                4 => 'PROMOTIONAL GC'
            };

            if ($item->daterev != '') {
                $balance = $item->vs_tf_denomination;
            } else {

                $barcodeData = $this->database->table('store_eod_textfile_transactions')
                    ->select(
                        'seodtt_bu',
                        'seodtt_terminalno',
                        'seodtt_credpuramt'
                    )
                    ->where('seodtt_barcode', $item->vs_barcode)
                    ->get();

                if ($barcodeData->isNotEmpty()) {
                    $puramt = $barcodeData->pluck('seodtt_credpuramt')->implode(', ');
                    $bus = $barcodeData->pluck('seodtt_bu')->implode(', ');
                    $tnum = $barcodeData->pluck('seodtt_terminalno')->implode(', ');
                }

                $purchasecred = $item->vs_tf_purchasecredit;
                $balance = $item->vs_tf_balance;
            }

            $vsdate = $item->vs_date;
            $vstime = $item->vs_time;

            $transformedData->push([
                'date' => $item->datever,
                'barcode' => $item->vs_barcode,
                'denomination' => $item->vs_tf_denomination,
                'purchasecred' => $purchasecred,
                'balance' => $balance,
                'customer' => $item->spexgcemp_lname,
                'assign' => $item->spcus_acctname,
                'businessunit' => $bus,
                'trans_number' => $item->seodtt_transno,
                'vs_store' => $item->vs_store,
                'terminalno' => $tnum,
                'valid_type' => 'VERIFIED',
                'gc_type' => $gctype,
                'vsdate' => $vsdate,
                'vstime' => $vstime,
                'cus_fname' => $item->cus_fname,
                'cus_lname' => $item->cus_lname,
                'cus_mname' => $item->cus_mname,
                'cus_namext' => $item->cus_namext,
                'seodtt_bu' => $item->seodtt_bu
            ]);


        });

        return $transformedData;
    }

    public function query()
    {
        // if ($this->isLocal) {

        // } else {

        return $this->database->table('store_verification')
            ->selectRaw(" DATE(store_verification.vs_date) as datever,
            DATE(store_verification.vs_reverifydate) as daterev,
            store_verification.vs_barcode,
            store_verification.vs_tf_denomination,
            store_verification.vs_tf_purchasecredit,
            store_verification.vs_tf_balance,
            special_external_gcrequest_emp_assign.spexgcemp_lname,
            special_external_customer.spcus_acctname,
            store_eod_textfile_transactions.seodtt_transno,
            store_eod_textfile_transactions.seodtt_bu,
            store_eod_textfile_transactions.seodtt_terminalno,
            store_verification.vs_store,
            store_verification.vs_gctype,
            store_verification.vs_date,
            customers.cus_fname,
            customers.cus_lname,
            customers.cus_mname,
            customers.cus_namext,
            store_verification.vs_time")
            ->join('special_external_gcrequest_emp_assign', 'special_external_gcrequest_emp_assign.spexgcemp_barcode', '=', 'store_verification.vs_barcode')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $this->request['year'])
            ->when(isset($this->request['month']), fn($q) => $q->whereMonth('vs_date', $this->request['month']))
            ->where('vs_store', $this->request['selectedStore'])
            ->where('special_external_gcrequest.spexgc_promo', '*')
            ->orderBy('vs_id')
            ->get();

        // }
    }
    public function startCell(): string
    {
        return 'A8';
    }
    public function registerEvents(): array
    {

        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $storeName = Store::where('store_id', $this->request['selectedStore'])->value('store_name');

                $sheet->setCellValue('C1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('C2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('B3', 'SPECIAL GC REDEMPTION');
                $sheet->setCellValue('C4', 'PERIOD COVER:' . $this->request['year']);
                $sheet->setCellValue('C6', 'STORE VERIFIED:' . $storeName);

                $sheet->mergeCells('C1:E1');
                $sheet->mergeCells('C2:E2');
                $sheet->mergeCells('B3:F3');
                $sheet->mergeCells('C6:E6');

                $sheet->getStyle('B1:C6')->getFont()->setBold(true);
                $sheet->getStyle('B1:C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

        ];
    }
    public function map($data): array
    {
        // Build the full name
        $fullname = trim("{$data['cus_fname']} {$data['cus_lname']} " .
            ($data['cus_mname'] ?? '') .
            ($data['cus_namext'] ? " {$data['cus_namext']}." : ''));


        return [
            (new \DateTime($data['date']))->format('F j, Y'),
            $data['barcode'],
            $data['denomination'],
            $data['purchasecred'],
            $data['balance'],
            Str::upper($data['customer']),
            $data['assign'],
            $fullname,
            $data['trans_number'],
            $data['businessunit'],
            $data['terminalno'],
            $data['valid_type']
        ];
    }

    public function title(): string
    {
        return 'SPGC REDEEM';
    }

    public function headings(): array
    {
        return [
            "REDEMPTION DATE", 
            "BARCODE", 
            "DENOMINATION", 
            "AMOUNT REDEEM",
            "BALANCE", 
            "EMPLOYEE NAME",
            "CUSTOMER ASSIGN",
            "CLAIMED BY",
            "TRANSACTION #",
            "STORE REDEEM",
            "TERMINAL #",
            "VALIDATION"
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            8 => ['font' => ['bold' => true]],
        ];
    }

}
