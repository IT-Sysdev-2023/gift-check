<?php

namespace App\Exports\DuplicateBarcodeExcel;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class talibonExcel implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $barcodeData;

    public function __construct($talibonData)
    {
        $this->barcodeData = $talibonData;
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

    public function array(): array
    {
        return array_map(function ($item) {
            return [
                $item['barcode'] ?? '',
                $item['name'] ?? '',
                $item['transno'] ?? '',
                $item['terminal'] ?? '',
                $item['bu'] ?? '',
                $item['amount'] ?? '',
                $item['verdate'] ?? '',
                $item['vertime'] ?? '',
                $item['store'] ?? '',
            ];
        }, $this->barcodeData);
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title rows (up to column I, not N)
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        // Center align the merged cells
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

        // Bold the titles
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);

        // Bold and center column headers
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');

        // Center all data in each row
        $sheet->getStyle('A6:I' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Talibon';
    }
}

