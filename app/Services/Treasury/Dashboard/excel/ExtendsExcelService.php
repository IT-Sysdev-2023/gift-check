<?php

namespace App\Services\Treasury\Dashboard\excel;

use App\Helpers\Excel\ExcelWriter;
use App\Models\BudgetRequest;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class ExtendsExcelService extends ExcelWriter
{
    protected $generateUserHeader;
    protected $border;
    protected $borderFBN;

    public function __construct()
    {
        parent::__construct();


        $this->generateUserHeader = [
            "No.",
            "Transaction Date",
            "Voucher",
            "Barcode",
            "Denomination",
            "Customer",
            "Apporval#",
            "Date Approved",
        ];

        $this->border =  $this->initializedBorder();
        $this->borderFBN =  $this->initializedBorderFontBoldNone();
    }

    public function excelWorkSheetPerBarcode($dataBarcode)
    {
        $spreadsheet = $this->spreadsheet;

        $excelRow = 5;

        $spreadsheet->getActiveSheet()->setTitle('Data Per Barcode');

        $this->getActiveSheetExcel()->fromArray($this->generateUserHeader, null, 'A' . $excelRow);

        foreach (range('A', 'H') as $col) {
            $cell = $col . $excelRow;

            $fillColor = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F4F4'],
            ];

            $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->border);
            $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $this->getActiveSheetExcel()->getStyle($cell)->getFill()->setFillType($fillColor['fillType']);
            $this->getActiveSheetExcel()->getStyle($cell)->getFill()->getStartColor()->setRGB($fillColor['startColor']['rgb']);
        }
        $excelRow++;
        $numCount = 1;

        $dataBarcode->each(function ($item) use (&$excelRow, &$numCount) {
            $dataBarcodeCollection[] = [
                $numCount++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->voucher,
                $item->spexgcemp_barcode,
                $item->spexgcemp_denom,
                $item->full_name,
                $item->spexgc_num,
                Date::parse($item->daterel)->toFormattedDateString(),
            ];

            $this->spreadsheet->getActiveSheet()->fromArray($dataBarcodeCollection, null, "A$excelRow");
            foreach (range('A', 'H') as $col) {
                $cell = $col . $excelRow;
                $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->borderFBN);
                $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $excelRow++;
        });

        foreach (range('A', 'H') as $col) {
            $this->getActiveSheetExcel()->getColumnDimension($col)->setAutoSize(true);
        }



        $excelRow++;

        return $this;
    }

    public function excelWorkSheetPerCustomer()
    {

        $spreadsheet = $this->spreadsheet;
        $newSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Per Customer');
        $spreadsheet->addSheet($newSheet);
        $spreadsheet->setActiveSheetIndexByName('Data Per Customer');

        $headerRowNewSheet = 1;
        $spreadsheet->getActiveSheet()->setCellValue('A' . $headerRowNewSheet, 'DATA FROM NEW SHEET');

        return $this;
    }

    public function save()
    {
        $spreadsheet = $this->spreadsheet;

        $filename = 'sample.xlsx';
        $filePathName = storage_path('app/' . $filename);

        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePathName);

        return route('download', ['filename' => $filename]);
    }
}
