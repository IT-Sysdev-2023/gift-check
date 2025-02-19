<?php

namespace App\Exports\BillingExcelReport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BillingExcelReportPerDay implements FromArray, WithHeadings
{
    protected $BillingExcelReportPerDay;

    public function __construct($request)
    {
        //  dd($request);
        $this->BillingExcelReportPerDay = $request['data'] ?? [];
    }

    public function headings(): array
    {
        return [
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
        ];
    }

    public function array(): array
    {
        return array_map(function ($item) {
            return [
                $item['vs_date'] ?? '',
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
}
