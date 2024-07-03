<?php

namespace App\Services\Documents;

use App\Helpers\Excel\ExcelWriter;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentBudgetLedgerService extends ExcelWriter
{
    protected $border;
    protected $borderFBN;
    protected $record;
    protected $header;
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
            "RFBR  - BUDGET ENTRY",
            "RFGCP - GC PRODUCTION",
            "GCRELINS - INSTITUTION GC RELEASING",
            "STORESALES  - REGULAR GC",
            "RFGCSEGC -  SPECIAL GC REQUEST",
            "RFGCSEGCREL -  SPECIAL GC RELEASE",
            "RFGCPROM -  PROMO GC REQUEST",
            "PROMOGCRELEASING -  PROMO GC RELEASING",
            "BEAMANDGO -  BEAM AND GO GC",
        ];

        $this->legendColors = [
            '5B8FB9',
            '15F5BA',
            'E8C5E5',
            'D6EFD8',
            'FCDC94',
            'FBF3D5',
            'E8EFCF',
            '9575DE',
            'FFCCD2',
        ];


        $this->border =  $this->initializedBorder();
        $this->borderFBN =  $this->initializedBorderFontBoldNone();
    }

    public function record($record)
    {

        $this->record = $record;

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

    public function titleHeader($date)
    {
        // dd($date);
        if(!empty($date)){
            $title = "Budget Legder From " . Date::parse($date[0])->toFormattedDateString() . " To " . Date::parse($date[1])->toFormattedDateString();
        }else{
            $title = "All Budget Legder as of " . today()->toFormattedDateString();

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

    public function writeResult($date)
    {
        $excelRow = 5;

        $this->getActiveSheetExcel()->fromArray($this->header, null, 'A' . $excelRow);

        $this->titleHeader($date);

        $this->legendHeader();

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

        $this->record->each(function ($item) use (&$no, &$excelRow) {
            $dataCollection[] = [
                $no++,
                ltrim($item->bledger_no, '0'),
                $item->bledger_datetime,
                $item->bledger_trid,
                $item->bledger_type,
                $item->bdebit_amt,
                $item->bcredit_amt,
            ];

            $this->spreadsheet->getActiveSheet()->fromArray($dataCollection, null, "A$excelRow");

            foreach (range('A', 'G') as $col) {
                $cell = $col . $excelRow;
                $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->borderFBN);
                $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $color =  $this->transactionType($item->bledger_type);

                $this->getActiveSheetExcel()->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($color);

                $this->getActiveSheetExcel()->getStyle($cell)->getFont()->getColor()->setARGB('000000');
            }

            $excelRow++;
        });

        return $this;
    }
    private function transactionType(string $type)
    {
        $transaction = [
            "RFBR" => '5B8FB9',
            "RFGCP" => '15F5BA',
            "GCRELINS" => 'E8C5E5',
            "STORESALES" => 'D6EFD8',
            "RFGCSEGC" => 'FCDC94',
            "RFGCSEGCREL" => 'FBF3D5',
            "RFGCPROM" => 'E8EFCF',
            "PROMOGCRELEASING" => '9575DE',
            "BEAMANDGO" => 'FFCCD2',
        ];

        return $transaction[$type] ?? '362222';
    }
    public function save($date)
    {
        $spreadsheet = $this->spreadsheet;

        if (!empty($date)) {
            $fileHeader = 'Generated Excel From ' . Date::parse($date[0])->toFormattedDateString() . ' To ' . Date::parse($date[0])->toFormattedDateString();
        } else {
            $fileHeader = 'Generated All Report as of ' . today()->toFormattedDateString();
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
