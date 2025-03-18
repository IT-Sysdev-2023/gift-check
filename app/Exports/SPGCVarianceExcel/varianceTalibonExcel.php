<?php

namespace App\Exports\SPGCVarianceExcel;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class varianceTalibonExcel implements WithHeadings, WithTitle, WithStyles, FromArray, ShouldAutoSize
{
    protected $TalibonData;
    protected $StoreName;

    public function __construct($request)
    {
        $this->TalibonData = $request['data'] ?? [];
        $this->StoreName = $request['formatCusName'] ?? '';
    }

    public function headings(): array
    {
        return [
            ['ALTURAS GROUP OF COMPANIES'],
            ['CUSTOMER FINANCIAL SERVICES CORP'],
            ['VARIANCE REPORT'],
            [$this->StoreName],
            [],
            [
                'Barcode',
                'Denomination',
                'Customer Name',
                'Verify Date',
                'Store',
                'Transaction No',
                'Status',
            ],
        ];
    }

    public function array(): array
    {
        return array_map(function ($item) {
            return [
                $item['barcode'] ?? '',
                $item['denom'] ?? '',
                $item['cusname'] ?? '',
                $item['verifydate'] ?? '',
                $item['store'] ?? '',
                $item['transno'] ?? '',
                $item['status'] ?? '',
            ];
        }, $this->TalibonData);
    }

    public function styles(Worksheet $sheet)
    {
        // Merge header title rows
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');

        // Center align and bold the title
        $sheet->getStyle('A1:G4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:G4')->getFont()->setBold(true)->setSize(14);

        // Bold and center align column headers
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getAlignment()->setHorizontal('center');

        // Center align all data rows
        $sheet->getStyle('A7:G' . $sheet->getHighestRow())->getAlignment()->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Talibon';
    }
}
