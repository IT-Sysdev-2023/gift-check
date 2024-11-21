<?php

namespace App\Exports\SPGCVarianceExcel;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class varianceTalibonExcel implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents, WithStyles
{
    protected $TalibonData;

    public function __construct($request)
    {
        $this->TalibonData = $request;
    }

    public function collection()
    {
        $talibonData = $this->VarianceTalibonData($this->TalibonData);

        $variances2Formatted = [];
        $paddingRows = [
            ['', '', '', '', '', '', ''], // Row 1
            ['', '', '', '', '', '', ''], // Row 2
            ['', '', '', '', '', '', ''], // Row 3
            ['', '', '', '', '', '', ''], // Row 4
            ['', '', '', '', '', '', ''], // Row 5
        ];

        foreach ($talibonData as $row) {
            // Set status based on vs_date and seodtt_transno
            if ($row->vs_date != null && $row->seodtt_transno != null) {
                $status = "Verified and Use";
            } elseif ($row->vs_date != null) {
                $status = "Verified not used";
            } else {
                $status = "Not verified / not use";
            }

            // Format date if it's not null
            $date = $row->vs_date ? Carbon::parse($row->vs_date)->format('Y-m-d') : 'N/A';

            // Add data to the formatted array
            $variances2Formatted[] = [
                'barcode' => $row->spexgcemp_barcode,
                'denom' => number_format($row->spexgcemp_denom, 2),
                'cusname' => strtoupper($row->customerName),
                'verifydate' => $date,
                'store' => $row->store_name ?? 'N/A',
                'transno' => $row->seodtt_transno ?? 'N/A',
                'status' => $status
            ];
        }

        return collect(array_merge($paddingRows, $variances2Formatted));
    }

    private function VarianceTalibonData($request)
    {
        // Database query using the request data (assuming 'customerName' is passed in $request)
        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->where('special_external_gcrequest.spexgc_company', $this->TalibonData['customerName'])
            ->select(
                'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                'special_external_gcrequest_emp_assign.spexgcemp_denom',
                DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname, ' ', special_external_gcrequest_emp_assign.spexgcemp_lname) AS customerName"),
                'store_verification.vs_date',
                'stores.store_name',
                'store_eod_textfile_transactions.seodtt_transno'
            )
            ->get();
    }

    public function styles(Worksheet $sheet)
    {
        $data = $this->collection();
        $rowCount = count($data);
        $lastColumn = chr(65 + count($this->headings()) - 1);
        $range = 'A6:' . $lastColumn . ($rowCount + 6);

        return [
            1 => ['font' => ['bold' => true]],
            $range => ['alignment' => ['horizontal' => 'left']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $event->sheet->setCellValue('A2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $event->sheet->setCellValue('A3', 'VARIANCE REPORT');
                $event->sheet->setCellValue('A4', $this->TalibonData['formatCusName']);

                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');
                $event->sheet->mergeCells('A3:H3');
                $event->sheet->mergeCells('A4:H4');


                $event->sheet->getStyle('A1:A4')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');
                $event->sheet->getStyle('A1:A4')->getFont()->setBold(true);

                $headings = $this->headings();
                $column = 'A';
                foreach ($headings as $heading) {
                    $event->sheet->setCellValue($column . '5', $heading);
                    $event->sheet->getStyle($column . '5')->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle($column . '5')->getFont()->setBold(true);
                    $column++;
                }
                $event->sheet->getStyle('A5:H5')->getAlignment()->setVertical('center');
                $event->sheet->getRowDimension(5)->setRowHeight(20);
            }
        ];
    }

    public function headings(): array
    {
        return [
            'Barcode',
            'Denomination',
            'Customer Name',
            'Verify Date',
            'Store',
            'Transaction No',
            'Status',
        ];
    }

    public function title(): string
    {
        return "Talibon";
    }
}
