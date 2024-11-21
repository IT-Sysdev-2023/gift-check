<?php

namespace App\Exports\DuplicateBarcodeExcel;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class talibonExcel implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $barcodeData;

    public function __construct($barcode)
    {
        $this->barcodeData = $barcode;
    }

    public function headings(): array
    {
        return [
            'Barcode',
            'Name',
            'Transaction No',
            'Terminal',
            'Business Unit',
            'Amount',
            'Date',
            'Time',
            'Store',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $data = $this->collection();
        $rowCount = count($data);
        $range = 'A1:Z' . ($rowCount + 1);

        return [
            1 => ['font' => ['bold' => true]],
            $range => ['alignment' => ['horizontal' => 'left']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'STORE VERIFIED / VALIDATED GC');

                $sheet->mergeCells('A1:I1');


                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');
                $sheet->getStyle('A1')->getFont()->setBold(true);

                $headings = $this->headings();
                $column = 'A';
                foreach ($headings as $heading) {
                    $sheet->setCellValue($column . '2', $heading);
                    $sheet->getStyle($column . '2')->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($column . '2')->getFont()->setBold(true);
                    $column++;
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Talibon';
    }

    public function collection()
    {
        return collect($this->fetchTalibonData());
    }

    private function fetchTalibonData()
    {
        return DB::table('store_verification')
        ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
        ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
        ->select(
            'store_eod_textfile_transactions.seodtt_transno as transno',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_mname, ' ', customers.cus_lname) as name"),
            'store_verification.vs_barcode as barcode',
            'store_eod_textfile_transactions.seodtt_terminalno as terminal',
            'store_eod_textfile_transactions.seodtt_bu as bu',
            'store_eod_textfile_transactions.seodtt_crditpurchaseamt as amount',
            'store_verification.vs_date as verdate',
            'store_verification.vs_time as vertime',
            DB::raw("(CASE 
                    WHEN LEFT(store_eod_textfile_transactions.seodtt_bu, 3) = 'ICM' THEN 'ICM'
                    WHEN LEFT(store_eod_textfile_transactions.seodtt_bu, 3) = 'ASC' THEN 'ASC'
                    WHEN LEFT(store_eod_textfile_transactions.seodtt_bu, 3) = 'TAL' THEN 'TAL'
                    WHEN LEFT(store_eod_textfile_transactions.seodtt_bu, 3) = 'TUB' THEN 'TUB'
                    ELSE 'PM' 
                END) as store")
        )
            ->whereIn('store_verification.vs_barcode', $this->barcodeData)
            ->get();
    }
}
