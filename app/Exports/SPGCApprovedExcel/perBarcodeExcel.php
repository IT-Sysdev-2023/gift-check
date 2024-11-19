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
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;


class perBarcodeExcel implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents, WithStartRow
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $generatedData;

    public function __construct($request)
    {
        $this->generatedData = $request;
    }

    public function startRow(): int
    {
        return 7; 
    }

    public function title(): string
    {
        return 'Per Barcode';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'HEAD OFFICE FINANCE DEPARTMENT');
                $sheet->setCellValue('A3', 'SPECIAL EXTERNAL GC REPORT- RELEASING');
                $sheet->setCellValue('A4', 'Start Date: ' . $this->generatedData['startDate']);
                $sheet->setCellValue('A5', 'End Date: ' . $this->generatedData['endDate']);

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                $sheet->mergeCells('A4:F4');
                $sheet->mergeCells('A5:F5');

                $sheet->getStyle('A1:A5')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');
                $sheet->getStyle('A1:A5')->getFont()->setBold(true);

                $headings = $this->headings();
                $column = 'A';
                foreach ($headings as $heading) {
                    $sheet->setCellValue($column . '6', $heading);
                    $sheet->getStyle($column . '6')->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($column . '6')->getFont()->setBold(true);
                    $column++;
                }
            },
        ];
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
        $paddingRows = collect(array_fill(0, 6, ['', '', '', '']));
        $dataWithPadding = $paddingRows->concat(collect($perBarcode));
        return collect($dataWithPadding);

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
