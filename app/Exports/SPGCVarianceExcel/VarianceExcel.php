<?php

namespace App\Exports\SPGCVarianceExcel;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class VarianceExcel implements FromCollection, ShouldAutoSize, WithTitle, WithStyles, WithEvents, WithHeadings
{
    protected $VarianceData;

    public function __construct($request)
    {
        $this->VarianceData = $request;
    }

    public function collection()
    {
        $data = $this->VarianceExcelQuery($this->VarianceData);
        $tagbilaranData = [];
        $paddingRows = [
            ['', '', '', '', '', '', ''], // Row 1
            ['', '', '', '', '', '', ''], // Row 2
            ['', '', '', '', '', '', ''], // Row 3
            ['', '', '', '', '', '', ''], // Row 4
            ['', '', '', '', '', '', ''], // Row 5
        ];
        foreach ($data as $row) {
            $row->status = $this->getStatus($row->vs_date, $row->seodtt_transno);
            $row->verifydate = $row->vs_date ? Carbon::parse($row->vs_date)->format('Y-m-d') : 'N/A';
            $row->store = $row->store_name ?? 'N/A';
            $row->transno = $row->seodtt_transno ?? 'N/A';
            $row->denom = is_numeric($row->spexgcemp_denom) ? number_format($row->spexgcemp_denom, 2) : 'N/A';
            $row->cusname = strtoupper($row->customerName);

            $tagbilaranData[] = [
                'barcode' => $row->spexgcemp_barcode,
                'denom' => number_format($row->spexgcemp_denom, 2),
                'cusname' => strtoupper($row->customerName),
                'verifydate' => $row->verifydate,
                'store' => $row->store_name ?? 'N/A',
                'transno' => $row->seodtt_transno ?? 'N/A',
                'status' => $row->status
            ];
        }

        return collect(array_merge($paddingRows, $tagbilaranData));
    }

    private function VarianceExcelQuery($request)
    {
        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->where('special_external_gcrequest.spexgc_company', $this->VarianceData['customerName'])
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

    private function getStatus($vs_date, $seodtt_transno)
    {
        if (!$vs_date && !$seodtt_transno) {
            return 'Not verified / Not use';
        } elseif ($vs_date && !$seodtt_transno) {
            return 'Verified / Not use';
        } else {
            return 'Verified and Use';
        }
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
        return 'Tagbilaran';
    }

    public function styles(Worksheet $sheet)
    {
        $data = $this->collection();
        $rowCount = count($data);
        $lastColumn = chr(65 + count($this->headings()) - 1);
        $range = 'A6:' . $lastColumn . ($rowCount + 5);

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
                $event->sheet->setCellValue('A4', $this->VarianceData['formatCusName']);

                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');
                $event->sheet->mergeCells('A3:H3');
                $event->sheet->mergeCells('A4:H4');

                $event->sheet->getStyle('A1:H4')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');
                $event->sheet->getStyle('A1:H4')->getFont()->setBold(true);

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
}
