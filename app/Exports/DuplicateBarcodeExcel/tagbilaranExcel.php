<?php

namespace App\Exports\DuplicateBarcodeExcel;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class tagbilaranExcel implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $barcode;

    public function __construct($barcode)
    {
        $this->barcode = $barcode;
        // dd($this->barcode);
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

    public function startRow(): int{
        return 4;
    }

    public function styles(Worksheet $sheet)
    {
        // return [];
        return [
            1 => ['font' => ['bold' => true]],
           'A1:Z1000' => ['alignment' => ['horizontal' => 'left']],
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
                    $sheet->setCellValue($column . '3', $heading);
                    $sheet->getStyle($column . '3')->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($column . '3')->getFont()->setBold(true);
                    $column++;
                }
            },
        ];
    }


    public function title(): string
    {
        return 'Tagbilaran';
    }

    public function collection()
    {
        $data = $this->tagbilaranDataExcel($this->barcode);
        $paddingRows = collect(array_fill(0, 2, ['', '', '', '']));
        $dataWithPadding = $paddingRows->concat(collect($data));
        return collect($dataWithPadding);
    }

    private function tagbilaranDataExcel($barcode)
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
            ->whereIn('store_verification.vs_barcode', $barcode)
            ->get();
    }
}
