<?php

namespace App\Exports\DuplicateBarcodeExcel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TagbilaranExcel implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $barcode;

    public function __construct($barcode)
    {
        $this->barcode = $barcode;
    }

    public function headings(): array
    {
        return [
            ['ALTURAS GROUP OF COMPANIES'],
            ['DUPLICATED BARCODES'],
            [],
            [
                'Barcode',
                'Name',
                'Transaction No',
                'Terminal',
                'Business Unit',
                'Amount',
                'Date',
                'Time',
                'Store',
            ],
        ];
    }

    public function collection(): Collection
    {
        $data = $this->tagbilaranDataExcel($this->barcode);
        return collect($data)->map(function ($item) {
            return [
                $item->barcode ?? '',
                $item->name ?? '',
                $item->transno ?? '',
                $item->terminal ?? '',
                $item->bu ?? '',
                $item->amount ?? '',
                $item->verdate ?? '',
                $item->vertime ?? '',
                $item->store ?? '',
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A6:I' . $sheet->getHighestRow())->getAlignment()->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Tagbilaran';
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
