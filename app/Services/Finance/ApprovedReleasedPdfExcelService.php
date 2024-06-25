<?php

namespace App\Services\Finance;

use App\Events\ApprovedReleasedEvents\ApprovedReleasedEach;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedHeader;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedInnerLoopEvents;
use App\Helpers\Excel\ExcelWriter;
use App\Helpers\NumberHelper;
use App\Services\Finance\excel\ExtendsExcelService;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// im watching you dont cha ever refractor this :)

class ApprovedReleasedPdfExcelService extends ExcelWriter
{
    public static function approvedSpgcPdfWriteResult($dateRange, $dataCus, $dataBar)
    {

        self::executionTime();

        $html = self::htmlStructure($dateRange, $dataCus, $dataBar);

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = 'Generated Pdf From ' . Date::parse($dateRange[0])->toFormattedDateString() . ' To ' . Date::parse($dateRange[1])->toFormattedDateString() . '.pdf';
        $filePathName = storage_path('app/' . $filename);

        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }
        Storage::put($filename, $output);

        $filePath = route('download', ['filename' => $filename]);

        return Inertia::render('Finance/Results/ApprovedSpgcPdfResult', [
            'filePath' => $filePath
        ]);
    }


    public static function htmlStructure($dateRange, $dataCus, $dataBar)
    {

        self::executionTime();

        $headersBar = ['No.', 'Date Requested', 'Barcode', 'Denom', 'Customer', 'Approval#', 'Date Approved'];

        $dataCollectionBar = [];

        $no = 1;
        $totalDenom = 0;

        $progressCount = 1;

        $dataBar->each(function ($item) use (&$dataCollectionBar, &$no, &$totalDenom, $dataBar, &$progressCount) {

            $totalDenom += (float)$item->spexgcemp_denom;

            $dataCollectionBar[] = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spexgcemp_barcode,
                $item->spexgcemp_denom,
                $item->full_name,
                $item->spexgc_num,
                Date::parse($item->daterel)->toFormattedDateString(),
            ];

            ApprovedReleasedEach::dispatch('Generating Excel of Approved Reports Per Barcode. ', $progressCount++, $dataBar->count(), Auth::user());
        });


        $headersCus = ['No.', 'Date Requested', 'Company', 'Approval#', 'Total Amount'];
        $dataCollectionCus = [];
        $progressCount = 1;

        $dataCus->each(function ($item) use (&$dataCollectionCus, &$no, &$progressCount, $dataCus) {
            $dataCollectionCus[] = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spcus_companyname,
                $item->spexgc_num,
                $item->totdenom,
            ];
            ApprovedReleasedEach::dispatch('Generating Excel of Approved Reports Per Customer. ', $progressCount++, $dataCus->count(), Auth::user());
        });
        $html = '<html><body style="font-family: Calibri, Arial, Helvetica, sans-serif;">';
        $html .= '<div style="text-align: center;">';
        $html .= '<p style="font-size: 12px;">' . 'ALTURAS GROUP OF COMPANIES' . '</p>';
        $html .= '<p style="font-size: 11px;">' . 'Head Office - Finance Department' . '</p>';
        $html .= '<p style="font-size: 11px;">' . 'Special External GC Report-Approvalt' . '</p>';
        $html .= '<p style="font-size: 11px;">' . Date::parse($dateRange[0])->toFormattedDateString() . ' To ' . Date::parse($dateRange[1])->toFormattedDateString() . '</p>';
        $html .= '</div>';
        $html .= '<br><br>';
        $html .= '<table border="1" cellspacing="0" cellpadding="1" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr>';
        $html .= '<th colspan="' . count($headersCus) . '" style="text-align: center; font-size: 13px;">Table Per Customers</th>';
        $html .= '</tr></thead>';
        $html .= '<thead><tr>';

        $progressCount = 1;
        $headerCusCount = count($headersCus);
        foreach ($headersCus as $header) {
            $html .= '<th style="font-size: 12px;">' . htmlspecialchars($header) . '</th>';
            ApprovedReleasedHeader::dispatch('Writing Header Pdf reports per customer... ', $progressCount++, $headerCusCount , Auth::user());
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        $progressCount = 1;

        foreach ($dataCollectionCus as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            ApprovedReleasedInnerLoopEvents::dispatch('Writing table pdf reports per customer... ', ++$progressCount, $dataCus->count(), Auth::user());
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br><br>';
        $html .= '<table border="1" cellspacing="0" cellpadding="1" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr>';
        $html .= '<th colspan="' . count($headersBar) . '" style="text-align: center; font-size: 13px;">Table Per Barcode</th>';
        $html .= '</tr></thead>';
        $html .= '<thead><tr>';

        $progressCount = 1;

        $headerBarCount = count($headersBar);

        foreach ($headersBar as $header) {
            $html .= '<th style="font-size: 12px;">' . htmlspecialchars($header) . '</th>';
            ApprovedReleasedHeader::dispatch('Writing header Pdf reports per barcode... ', $progressCount++, $headerBarCount, Auth::user());
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        $progressCount = 1;

        foreach ($dataCollectionBar as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="width:100%; border-collapse:collapse; margin: 0 auto; text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            ApprovedReleasedInnerLoopEvents::dispatch('Writing table pdf per barcode... ', ++$progressCount, $dataBar->count(), Auth::user());
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '<br><br>';
        $html .= '<div style="text-align: center;">';
        $html .= '<p style="font-size: 12px; "> ' . 'Total Count: ' . count($dataBar) . '</p>';
        $html .= '<p style="font-size: 11px; color: blue; text-decoration: underline">' . 'Total Amount: ' . NumberHelper::format($totalDenom) . '</p>';
        $html .= '</div>';
        $html .= '<br><br>';
        $html .= '</body></html>';
        return $html;
    }

    public function approvedSpgcExcelWriteResult($dateRange, $dataCus, $dataBar)
    {
        $save = (new ExtendsExcelService())
            ->excelWorkSheetPerBarcode($dataBar, $dateRange)
            ->excelWorkSheetPerCustomer($dataCus, $dateRange)
            ->save($dateRange);

        return Inertia::render('Finance/Results/ApprovedSpgcExcelResult', [
            'filePath' => $save,
        ]);
    }
}
