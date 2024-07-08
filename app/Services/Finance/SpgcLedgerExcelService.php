<?php

namespace App\Services\Finance;

use App\Events\ApprovedReleasedEvents\ApprovedReleasedEach;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedHeader;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedInnerLoopEvents;
use App\Events\SpgcLedgerExcelEvents;
use App\Helpers\Excel\ExcelWriter;
use App\Helpers\NumberHelper;
use App\Services\Finance\excel\ExtendsExcelService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SpgcLedgerExcelService extends ExcelWriter
{

    protected $record;
    protected $date;
    protected $header;
    protected $border;
    protected $borderFBN;
    protected $legendHeader;
    protected $legendColors;

    public function __construct()
    {
        parent::__construct();

        $this->header = [
            "No.",
            "Ledger No.",
            "Date.",
            "Transaction #.",
            "Transaction Type.",
            "Debit.",
            "Credit.",
        ];
        $this->legendHeader = [
            "RFGCSEGC -  SPECIAL GC REQUEST",
            "RFGCSEGCREL -  SPECIAL GC RELEASE",
        ];
        $this->legendColors = [
            'CAF4FF',
            '15F5BA',
        ];

        $this->border =  $this->initializedBorder();
        $this->borderFBN =  $this->initializedBorderFontBoldNone();
    }
    public function record($record)
    {
        $this->record = $record;

        return $this;
    }
    public function date($date) {
        $this->date = $date;
        return $this;
    }

    public function legendHeader()
    {
        $headerRow = 5;
        $column = 'I';

        foreach ($this->legendHeader as $index => $header) {
            $cell = $column . $headerRow;


            $this->getActiveSheetExcel()->setCellValue($cell, $header);

            $this->getActiveSheetExcel()->mergeCells($cell);

            $style = $this->getActiveSheetExcel()->getStyle($cell);
            $font = $style->getFont();
            $font->setBold(true);

            $this->getActiveSheetExcel()->getColumnDimension($column)->setAutoSize(true);

            $color = $this->legendColors[$index];
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($color);

            $headerRow++;
        }
    }



    private function transactionType(string $type)
    {
        $transaction = [
            "RFGCSEGC" => 'CAF4FF',
            "RFGCSEGCREL" => '15F5BA',
        ];

        return $transaction[$type] ?? '362222';
    }

    public function titleHeader()
    {

        if(!empty($this->date)){
            $title = "Spgc Legder From " . Date::parse($this->date[0])->toFormattedDateString() . " To " . Date::parse($this->date[1])->toFormattedDateString();
        }else{
            $title = "All Spgc Legder as of " . today()->toFormattedDateString();

        }

        $headerRow = 1;
        $this->getActiveSheetExcel()->setCellValue('A' . $headerRow, $title);
        $this->getActiveSheetExcel()->mergeCells('A' . $headerRow . ':E' . $headerRow);
        $style = $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow);
        $font = $style->getFont();
        $font->setBold(true);
        $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow++;
    }


    public function  writeResult()
    {
        $excelRow = 5;

        $this->getActiveSheetExcel()->fromArray($this->header, null, 'A' . $excelRow);

        $this->legendHeader();

        $this->titleHeader($this->date);

        foreach (range('A', 'G') as $col) {
            $cell = $col . $excelRow;

            $fillColor = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F4F4'],
            ];
            $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->border);
            $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $this->getActiveSheetExcel()->getColumnDimension($col)->setAutoSize(true);
        }
        $excelRow++;
        $no = 1;

        $count = count($this->record);

        $this->record->each(function ($item) use (&$no, &$excelRow, $count) {

            $dataCollection[] = [
                $no++,
                ltrim($item->spgcledger_no, '0'),
                Date::parse($item->spgcledger_datetime)->toDateString(),
                $item->spgcledger_trid,
                $item->spgcledger_type,
                $item->spgcledger_debit,
                $item->spgcledger_credit,
            ];

            $this->spreadsheet->getActiveSheet()->fromArray($dataCollection, null, "A$excelRow");

            foreach (range('A', 'G') as $col) {
                $cell = $col . $excelRow;
                $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->borderFBN);
                $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $color =  $this->transactionType($item->spgcledger_type);

                $this->getActiveSheetExcel()->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($color);

                $this->getActiveSheetExcel()->getStyle($cell)->getFont()->getColor()->setARGB('000000');
            }

            SpgcLedgerExcelEvents::dispatch("Generating Excel in progress.. ", $no, $count, Auth::user());

            $excelRow++;
        });

        return $this;
    }

    public function save()
    {

        $spreadsheet = $this->spreadsheet;

        if (!empty($this->date)) {
            $fileHeader = 'Generated Spgc Legder Excel From ' . Date::parse($this->date[0])->toFormattedDateString() . ' To ' . Date::parse($this->date[0])->toFormattedDateString();
        } else {
            $fileHeader = 'Generated All Spgc Ledger Report as of ' . today()->toFormattedDateString();
        }

        $filename =  $fileHeader . '.xlsx';
        $filePathName = storage_path('app/' . $filename);

        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePathName);

        return route('download', ['filename' => $filename]);
    }
}
