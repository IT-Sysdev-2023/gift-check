<?php

namespace App\Exports\SPGCReleasedExcel;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStartRow;




class releasePerCustomer implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents, WithStartRow
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $releaseData;
    public function __construct($request)
    {
        $this->releaseData = $request;
    }

    public function startRow(): int
    {
        return 7;
    }
   

    public function title(): string
    {
        return 'Per Customer';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'HEAD OFFICE FINANCE DEPARTMENT');
                $sheet->setCellValue('A3', 'SPECIAL EXTERNAL GC REPORT- Releasing');
                $sheet->setCellValue('A4', 'Start Date: ' . $this->releaseData['startDate']);
                $sheet->setCellValue('A5', 'End Date: ' . $this->releaseData['endDate']);

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
            'Company',
            'RELEASING #',
            'Total Denomination'
        ];
    }

    public function collection()
    {
        $perCustomer =  $this->releasedPerCustomerData($this->releaseData);
        $paddingRows = collect(array_fill(0, 6, ['', '', '', '']));
        $dataWithPadding = $paddingRows->concat(collect($perCustomer));
        return collect($dataWithPadding);
    }

    private function releasedPerCustomerData($request)
    {
        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('approved_request.reqap_approvedtype', 'special external releasing')
            ->whereRaw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d') >= ?", [$this->releaseData['startDate']])
            ->whereRaw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d') <= ?", [$this->releaseData['endDate']])
            ->select([
            DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                'special_external_customer.spcus_companyname',
            'special_external_gcrequest.spexgc_num',
                DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),
            ])
            ->groupBy(
                'special_external_gcrequest.spexgc_num',
                'special_external_gcrequest.spexgc_datereq',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )
            ->orderBy('special_external_gcrequest.spexgc_datereq', 'asc')
            ->get();
    }
}
