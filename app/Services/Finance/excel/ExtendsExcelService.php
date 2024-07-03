<?php

namespace App\Services\Finance\excel;

use App\Events\ApprovedReleasedEvents\ApprovedReleasedEach;
use App\Helpers\Excel\ExcelWriter;
use App\Helpers\NumberHelper;
use Illuminate\Support\Facades\Auth;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class ExtendsExcelService extends ExcelWriter
{
    protected $perBarcodeHeader;
    protected $perCustomerHeader;
    protected $border;
    protected $borderFBN;
    protected $headerBarCode;

    protected $approvedType;

    public function __construct()
    {
        parent::__construct();

        $this->border =  $this->initializedBorder();
        $this->borderFBN =  $this->initializedBorderFontBoldNone();
    }

    public function approvedType($approvedType)
    {
        $this->approvedType = $approvedType;

        return $this;
    }

    public function excelWorkSheetPerBarcode($dataBarcode, $dateRange)
    {
        $this->approvedType === 'special external releasing' ?  $headerNo  = 'Released No.' : 'Approved No';
        $this->approvedType === 'special external releasing' ?  $headerDate  = 'Released Date.' : 'Approved Date';
        $this->approvedType === 'special external releasing' ?  $headerGeneral  = 'Released' : 'Approved';

        $this->perBarcodeHeader = [
            "No.",
            "Transaction Date",
            "Voucher",
            "Barcode",
            "Denomination",
            "Customer",
            $headerNo,
            $headerDate,
        ];

        $spreadsheet = $this->spreadsheet;

        $this->headerExcelBarCus($dateRange);

        $excelRow = 7;

        $spreadsheet->getActiveSheet()->setTitle('Data Per Barcode');

        $this->getActiveSheetExcel()->fromArray($this->perBarcodeHeader, null, 'A' . $excelRow);

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
        $progressCount = 1;

        $dataBarcode->each(function ($item) use (&$excelRow, &$numCount, &$progressCount, $dataBarcode, $headerGeneral) {
            $dataBarcodeCollection[] = [
                $numCount++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->voucher,
                $item->spexgcemp_barcode,
                NumberHelper::currency($item->spexgcemp_denom),
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
            ApprovedReleasedEach::dispatch('Generating Excel of ' . $headerGeneral .' Reports Per Barcode. ', $progressCount++, $dataBarcode->count(), Auth::user());
            $excelRow++;
        });

        foreach (range('A', 'H') as $col) {
            $this->getActiveSheetExcel()->getColumnDimension($col)->setAutoSize(true);
        }

        $excelRow++;

        return $this;
    }

    public function excelWorkSheetPerCustomer($dataCustomer, $dateRange)
    {

        $this->approvedType === 'special external releasing' ?  $headerNo  = 'Released No.' : 'Approved No.';
        $this->approvedType === 'special external releasing' ?  $headerGeneral  = 'Released' : 'Approved';

        $this->perCustomerHeader = [
            "No.",
            "Date.",
            "Company.",
            $headerNo,
            "Total Denom",
        ];

        $spreadsheet = $this->spreadsheet;
        $newSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data Per Customer');
        $spreadsheet->addSheet($newSheet);
        $spreadsheet->setActiveSheetIndexByName('Data Per Customer');

        $this->headerExcelBarCus($dateRange);

        $excelRow = 7;

        $this->getActiveSheetExcel()->fromArray($this->perCustomerHeader, null, 'A' . $excelRow);

        foreach (range('A', 'E') as $col) {
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

        $progressCount = 1;

        $dataCustomer->each(function ($item) use (&$excelRow, &$numCount, &$progressCount, $dataCustomer, $headerGeneral) {

            $dataCustomerCollection[] = [
                $numCount++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spcus_acctname,
                $item->spexgc_num,
                NumberHelper::currency($item->totdenom),
            ];
            $this->spreadsheet->getActiveSheet()->fromArray($dataCustomerCollection, null, "A$excelRow");
            foreach (range('A', 'E') as $col) {
                $cell = $col . $excelRow;
                $this->getActiveSheetExcel()->getStyle($col . $excelRow)->applyFromArray($this->borderFBN);
                $this->getActiveSheetExcel()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            ApprovedReleasedEach::dispatch('Generating Excel of ' . $headerGeneral .' Reports Per Customer. ', $progressCount++, $dataCustomer->count(), Auth::user());
            $excelRow++;
        });



        foreach (range('A', 'E') as $col) {
            $this->getActiveSheetExcel()->getColumnDimension($col)->setAutoSize(true);
        }

        return $this;
    }

    public function save($dateRange)
    {
        $spreadsheet = $this->spreadsheet;
        $fileHeader = 'Generated Excel From ' . Date::parse($dateRange[0])->toFormattedDateString() . ' To ' . Date::parse($dateRange[0])->toFormattedDateString();

        $filename =  $fileHeader . '.xlsx';
        $filePathName = storage_path('app/' . $filename);

        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePathName);

        return route('download', ['filename' => $filename]);
    }


    public function headerExcelBarCus($dateRange)
    {

        $this->approvedType === 'special external releasing' ? $headerType  = 'RELEASING' : 'APPROVAL';

        $headerRow = 2;
        $this->getActiveSheetExcel()->setCellValue('A' . $headerRow, 'ALTURAS GROUP OF COMPANIES');
        $this->getActiveSheetExcel()->mergeCells('A' . $headerRow . ':E' . $headerRow);
        $style = $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':E' . $headerRow);
        $font = $style->getFont();
        $font->setBold(true);
        $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':E' . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow++;

        $this->getActiveSheetExcel()->setCellValue('A' . $headerRow, 'HEAD OFFICE FINANCE DEPARTMENT');
        $this->getActiveSheetExcel()->mergeCells('A' . $headerRow . ':E' . $headerRow);
        $style = $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow);
        $font = $style->getFont();
        $font->setBold(true);
        $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow++;

        $this->getActiveSheetExcel()->setCellValue('A' . $headerRow, 'SPECIAL EXTERNAL GC REPORT - ' . $headerType);
        $this->getActiveSheetExcel()->mergeCells('A' . $headerRow . ':E' . $headerRow);
        $style = $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow);
        $font = $style->getFont();
        $font->setBold(true);
        $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow++;

        $this->getActiveSheetExcel()->setCellValue('A' . $headerRow, Date::parse($dateRange[0])->toFormattedDateString() . ' To ' . Date::parse($dateRange[0])->toFormattedDateString());
        $this->getActiveSheetExcel()->mergeCells('A' . $headerRow . ':E' . $headerRow);
        $style = $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow);
        $font = $style->getFont();
        $font->setBold(true);
        $this->getActiveSheetExcel()->getStyle('A' . $headerRow . ':H' . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerRow++;
    }
}
