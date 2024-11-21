<?php

namespace App\Exports\SPGCApprovedExcel;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use  Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;



class ExcelExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents, WithStartRow
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $requestedData;
    public function __construct($request)
    {
        $this->requestedData = $request;
    }

    public function startRow(): int
    {
        return 7;
    }

    public function title(): string
    {
        return 'Per Customer';
    }

    public function headings(): array
    {
        return [
            'Date Requested',
            'Company',
            'Approval #',
            'Total Amount',

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => 'true', 'Sans-serif' => 'true']],
            'A1:Z1000' => ['alignment' => ['horizontal' => 'left']]
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'HEAD OFFICE FINANCE DEPARTMENT');
                $sheet->setCellValue('A3', 'SPECIAL EXTERNAL GC REPORT- APPROVAL');
                $sheet->setCellValue('A4', 'Start Date: ' . $this->requestedData['startDate']);
                $sheet->setCellValue('A5', 'End Date: ' . $this->requestedData['endDate']);

                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A4:D4');
                $sheet->mergeCells('A5:D5');

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

    public function collection()
    {

        $datacus1 = $this->getDataPerCustomer($this->requestedData);
        $paddingRows = collect(array_fill(0, 6, ['', '', '', '']));
        $dataWithPadding = $paddingRows->concat(collect($datacus1));

        return collect($dataWithPadding);
    }

    private function getDataPerCustomer($request)
    {

        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$this->requestedData['startDate'], $this->requestedData['endDate']])
            ->select(
            // DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
            // 'special_external_customer.spcus_acctname',
            DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                'special_external_customer.spcus_companyname',
                'special_external_gcrequest.spexgc_num',
                DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),

                // DB::raw("IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) as totcnt"),
                // DB::raw("CONCAT(reqby.firstname, ' ', reqby.lastname) as trby"),
            )
            ->groupBy(
                'special_external_gcrequest.spexgc_num',
                'special_external_gcrequest.spexgc_datereq',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )
            ->orderByDesc('special_external_gcrequest.spexgc_datereq')
            ->get();
    }
}
