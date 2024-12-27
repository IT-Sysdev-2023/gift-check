<?php

namespace App\Exports\StoreAccounting;

use App\Events\AccountingReportEvent;
use App\Events\StoreAccountReportEvent;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\User;
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

class StoreGcPurchasedReportExport implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles, WithEvents
{

    public function __construct(protected $database, protected $request, protected &$progress = null, protected $reportId = null, protected ?User $user = null)
    {
    }
    public function collection()
    {
        $data = $this->database->table('store_eod_textfile_transactions')
            ->selectRaw("DATE(store_verification.vs_date) as datever,
                    DATE(store_verification.vs_reverifydate) as daterev,
                    store_verification.vs_barcode,
                    store_verification.vs_tf_denomination,
                    store_verification.vs_tf_purchasecredit,
                    store_eod_textfile_transactions.seodtt_bu,
                    store_eod_textfile_transactions.seodtt_transno,
                    store_verification.vs_store,
                    customers.cus_fname,
                    customers.cus_lname,
                    customers.cus_mname,   
                    customers.cus_namext,
                    store_verification.vs_tf_balance,
                    store_verification.vs_gctype,
                    store_verification.vs_date,
                    store_verification.vs_time")
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $this->request['year'])
            ->when(isset($this->request['month']), fn($q) => $q->whereMonth('vs_date', $this->request['month']))
            ->where('vs_store', $this->request['selectedStore'])
            ->orderBy('store_verification.vs_date')->get();



        $purchasecred = 0;
        $balance = 0;
        $bus = "";
        $tnum = "";
        $puramt = "";
        $initial = '';

        $transformedData = collect();

        $data->each(function ($item) use ($purchasecred, $balance, $bus, $tnum, $puramt, $initial, &$transformedData) {

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
                'cus_fname' => $item->cus_fname,
                'cus_lname' => $item->cus_lname,
                'cus_mname' => $item->cus_mname,
                'cus_namext' => $item->cus_namext,
                'balance' => $balance,
                'valid_type' => 'VERIFIED',
                'gc_type' => $gctype,
                'businessunit' => $bus,
                'terminalno' => $tnum,
                'vsdate' => $vsdate,
                'vstime' => $vstime,
                'seodtt_bu' => $item->seodtt_bu,
                'purchaseamt' => $puramt,
                'trans_store' => $item->vs_store,
                'vs_store' => $item->vs_store,
                'trans_number' => $item->seodtt_transno,
                'trans_datetime' => $item->vs_date . ' ' . $item->vs_time
            ]);


        });

        return $transformedData;
    }
    public function registerEvents(): array
    {

        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $storeName = Store::where('store_id', $this->request['selectedStore'])->value('store_name');

                $sheet->setCellValue('C1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('C2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('B3', 'MONTHLY REPORT ON GIFT CHECK (Store Billing)');
                $sheet->setCellValue('C4', 'PERIOD COVER:' . $this->request['year']);
                $sheet->setCellValue('C6', 'STORE VERIFIED:' . $storeName);

                $sheet->mergeCells('C1:E1');
                $sheet->mergeCells('C2:E2');
                $sheet->mergeCells('B3:F3');
                $sheet->mergeCells('C6:E5');
            
                $sheet->getStyle('B1:C6')->getFont()->setBold(true);
                $sheet->getStyle('B1:C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

        ];
    }
    public function map($data): array
    {
        // $this->broadcast("GC report Per Day!");

        // Build the full name
        $fullname = trim("{$data['cus_fname']} {$data['cus_lname']} " .
            ($data['cus_mname'] ?? '') .
            ($data['cus_namext'] ? " {$data['cus_namext']}." : ''));

        // Combine date and time
        $datetime = "{$data['vsdate']} {$data['vstime']}";

        // Extract the first three characters of the business unit
        $businessunit = substr($data['seodtt_bu'], 0, 3);

        $bu = match ($businessunit) {
            'ICM' => 'ISLAND CITY MALL',
            'PM-' => 'PLAZA MARCELA',
            'ASC' => 'ALTURAS MALL',
            'TAL' => 'ALTURAS TALIBON',
            'TUB' => 'ALTURAS TUBIGON',
            'COLC' => 'COLONNADE COLON',
            'COLM' => 'COLONNADE MANDAUE',
        };

        $storePurchased = match ($data['trans_store']) {
            1 => 'ALTURAS MALL',
            2 => 'ALTURAS TALIBON',
            3 => 'ISLAND CITY MALL',
            4 => 'PLAZA MARCELA',
            6 => 'COLONNADE COLON',
            7 => 'COLONNADE MANDAUE',
            8 => 'ALTA CITA',
        };

        return [
            (new \DateTime($data['trans_datetime']))->format('F j, Y'),
            $data['barcode'],
            $data['denomination'],
            $data['purchasecred'],
            $data['balance'],
            Str::upper($fullname),
            $storePurchased,
            $data['trans_number'],
            $bu,
            $data['terminalno'],
            $data['valid_type'],
            $data['gc_type'],
            $datetime
        ];
    }

    public function title(): string
    {
        return 'Store Billing';
    }

    public function headings(): array
    {
        return [
            'DATE PURCHASED',
            'BARCODE',
            'DENOMINATION',
            'AMOUNT REDEEM',
            'CUSTOMER NAME',
            'BALANCE',
            'CUSTOMER NAME',
            'STORE PURCHASED',
            'TRANSACTION #',
            'STORE REDEEM',
            'TERMINAL #',
            'VALIDATION',
            'GC TYPE',
            'GC TYPE VERIFIED',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function broadcast(string $info, bool $isDone = false, $id = null)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        $this->progress['isDone'] = $isDone;

        StoreAccountReportEvent::dispatch($this->user, $this->progress, $id ?? $this->reportId);
    }
}
