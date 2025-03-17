<?php

namespace App\Exports\BillingExcelReport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BillingExcelReportPerDay implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $BillingExcelReportPerDay;
    protected $dateSelected;

    public function __construct($request)
    {
        // dd($this->BillingExcelReportPerDay = $request['data'] ?? []);
        $this->BillingExcelReportPerDay = $request['data'] ?? [];

        $this->dateSelected = $request['date'] ?? 'N/A';
    }

    public function headings(): array
    {
        return [
            ['ALTURAS GROUP OF COMPANIES'],
            ['CUSTOMER FINANCIAL SERVICES CORP'],
            ['PERIOD COVER: ' . $this->dateSelected],
            [],
            [
                'Date Purchased',
                'Barcode',
                'Denomination',
                'Amount Redeem',
                'Balance',
                'Customer Name',
                'Store Purchased',
                'Transaction #',
                'Store Redeem',
                'Terminal #',
                'Staff Name',
                'Validation',
                'GC Type',
                'GC Type Verified',
            ],
        ];
    }

    public function array(): array
    {
        return array_map(function ($item) {
            return [
                $item['full_date'] ?? '',
                $item['seodtt_barcode'] ?? '',
                $item['vs_tf_denomination'] ?? '',
                $item['seodtt_credpuramt'] ?? '',
                $item['seodtt_balance'] ?? '',
                $item['vs_fullname'] ?? '',
                $item['store_name'] ?? '',
                $item['seodtt_transno'] ?? '',
                $item['store_name'] ?? '',
                $item['seodtt_terminalno'] ?? '',
                $item['staff_name'] ?? '',
                $item['valid_type'] ?? '',
                $item['vs_gctype'] ?? '',
                $item['full_date'] ?? '',
            ];
        }, $this->BillingExcelReportPerDay);
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title rows
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');
        $sheet->mergeCells('A3:N3');

        // Center align the merged cells
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

        // Bold the titles
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);

        // Bold and center column headers
        $sheet->getStyle('A5:N5')->getFont()->setBold(true);
        $sheet->getStyle('A5:N5')->getAlignment()->setHorizontal('center');

        // center all the data in each rows
        $sheet->getStyle('A6:N' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal('center');
        return [];
    }
}
