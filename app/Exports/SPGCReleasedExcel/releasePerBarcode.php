<?php

namespace App\Exports\SPGCReleasedExcel;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;




class releasePerBarcode implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle, WithStyles, WithEvents, WithStartRow
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $perBarcodeData;
    public function __construct($request)
    {
        $this->perBarcodeData = $request;

        // dd($this->perBarcodeData);
    }
    public function title(): string
    {
        return 'Per Barcode';
    }

    public function startRow(): int
    {
        return 7;
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'HEAD OFFICE FINANCE DEPARTMENT');
                $sheet->setCellValue('A3', 'SPECIAL EXTERNAL GC REPORT- RELEASE');
                $sheet->setCellValue('A4', 'Start Date: ' . $this->perBarcodeData['startDate']);
                $sheet->setCellValue('A5', 'End Date: ' . $this->perBarcodeData['endDate']);

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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => 'true', 'Sans-serif' => 'true']],
            'A1:Z1000' => ['alignment' => ['horizontal' => 'left']]
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
            'Date Approved'
        ];
    }

    public function collection()
    {
        $perBarcodeRelease = $this->releasePerBarcodeData($this->perBarcodeData);
        $paddingRows = collect(array_fill(0, 6, ['', '', '', '']));
        $dataWithPadding = $paddingRows->concat(collect($perBarcodeRelease));
        return collect($dataWithPadding);
    }

    private function releasePerBarcodeData($request)
    {
        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('approved_request.reqap_approvedtype', 'special external releasing')
            ->whereBetween('approved_request.reqap_date', [$this->perBarcodeData['startDate'], $this->perBarcodeData['endDate']])
            ->select(
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%M %d %Y') as dateRequest0"),
                'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                'special_external_gcrequest_emp_assign.spexgcemp_denom',
                DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname,
                 ' ', special_external_gcrequest_emp_assign.spexgcemp_mname,
                 ' ', special_external_gcrequest_emp_assign.spexgcemp_lname ) as customer"),
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%M %d %Y') as dateRequest1")
            )
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->get();
    }
}
