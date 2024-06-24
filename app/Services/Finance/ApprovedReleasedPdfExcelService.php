<?php

namespace App\Services\Finance;

use App\Events\ApprovedReleasedEvents;
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


        $headersCus = ['No.', 'Date Requested', 'Company', 'Approval#', 'Total Amount'];

        $dataCollectionCus[] = [];

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
        foreach ($headersCus as $header) {
            $html .= '<th style="font-size: 12px;">' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        $html = '';
        $progressCount = 0;

        $dataCus->each(function ($item) use (&$dataCollectionCus, &$no, &$progressCount, $dataCus, &$html) {

            $dataCollectionCus = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spcus_companyname,
                $item->spexgc_num,
                $item->totdenom,
            ];

            $html .= '<tr>';
            foreach ($dataCollectionCus as $cell) {
                $html .= '<td style="text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            ApprovedReleasedEvents::dispatch('Generating Excel of Approved Reports Per Cus. ', ++$progressCount, $dataCus->count(), Auth::user());
        });
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br><br>';

        $html .= '<table border="1" cellspacing="0" cellpadding="1" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr>';
        $html .= '<th colspan="' . count($headersBar) . '" style="text-align: center; font-size: 13px;">Table Per Barcode</th>';

        $html .= '</tr></thead>';
        $html .= '<thead><tr>';
        foreach ($headersBar as $header) {
            $html .= '<th style="font-size: 12px;">' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        $html = '';

        $progressCount = 0;

        $dataBar->each(function ($item) use (&$dataCollectionBar, &$no, &$totalDenom,  &$progressCount, $dataBar, &$html) {
            $totalDenom += (float)$item->spexgcemp_denom;
            $dataCollectionBar = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spexgcemp_barcode,
                $item->spexgcemp_denom,
                $item->full_name,
                $item->spexgc_num,
                Date::parse($item->daterel)->toFormattedDateString(),
            ];
            $html .= '<tr>';
            foreach ($dataCollectionBar as $cell) {
                $html .= '<td style="text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            ApprovedReleasedEvents::dispatch('Generating Excel of Approved Reports Per Barcode. ', ++$progressCount, $dataBar->count(), Auth::user());
        });

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
