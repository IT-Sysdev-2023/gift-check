<?php

namespace App\Exports\SPGCApprovedExcel;

use Dompdf\Css\Color;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class perBarcodeExcel implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $generatedData;

    public function __construct($request)
    {
        $this->generatedData = $request;
    }

    public function title(): string
    {
        return 'Per Barcode';
    }

    public function headings(): array
    {
        return [
            'Date Requested',
            'Barcode',
            'Denomination',
            'Customer',
            'Approval #',
            'Date Approved',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'Sans-serif' => true]],
            'A1:Z1000' => ['alignment' => ['horizontal' => 'left']],
         
        ];
    }

    public function collection()
    {
        $perBarcode = $this->getAllDataPerBarcode($this->generatedData);
        return collect($perBarcode);

        // return User::all();
    }

    private function getAllDataPerBarcode($request)
    {

        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$this->generatedData['startDate'], $this->generatedData['endDate']])
            ->select(
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                'special_external_gcrequest_emp_assign.spexgcemp_denom',
                DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_mname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_lname ) AS customer_name"),
                // 'special_external_gcrequest_emp_assign.voucher',
                // 'special_external_gcrequest_emp_assign.spexgcemp_extname',
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel")
            )
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->get();
    }
}
