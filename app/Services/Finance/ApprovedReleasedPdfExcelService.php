<?php

namespace App\Services\Finance;

use App\Events\ApprovedReleasedEvents\ApprovedReleasedEach;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedHeader;
use App\Events\ApprovedReleasedEvents\ApprovedReleasedInnerLoopEvents;
use App\Helpers\Excel\ExcelWriter;
use App\Helpers\NumberHelper;
use App\Services\Finance\excel\ExtendsExcelService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// im watching you dont cha ever refractor this :)

class ApprovedReleasedPdfExcelService extends ExcelWriter
{


    public function __construct()
    {
    }
    public  function approvedSpgcPdfWriteResult($dateRange, $dataCus, $dataBar)
    {
        self::executionTime();

        $html = $this->htmlStructure($dateRange, $dataCus, $dataBar);


        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);

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


    public function htmlStructure($dateRange, $dataCus, $dataBar)
    {


        $no = 1;
        $totalDenom = 0;
        $progressCount = 1;
        $barcount = count($dataBar);
        $cuscount = count($dataCus);

        $html = '<html><body style="font-family: Calibri, Arial, Helvetica, sans-serif;">';
        $html .= '<div style="text-align: center;">';
        $html .= '<p style="font-size: 12px;">' . 'ALTURAS GROUP OF COMPANIES' . '</p>';
        $html .= '<p style="font-size: 11px;">' . 'Head Office - Finance Department' . '</p>';
        $html .= '<p style="font-size: 11px;">' . 'Special External GC Report-Approvalt' . '</p>';
        $html .= '<p style="font-size: 11px;">' . Date::parse($dateRange[0])->toFormattedDateString() . ' To ' . Date::parse($dateRange[1])->toFormattedDateString() . '</p>';
        $html .= '</div>';
        $html .= '<br><br>';
        $html .= '<table border="1" cellpadding="2" cellspacing="0" style="width: 100%">
        <thead>
           <tr>
                <th colspan="5" style="text-align: center; font-size: 12px;">Table Per Customer</th>
            </tr>
            <tr style="text-align: center; font-size: 11px;">
                <th>No</th>
                <th>Date Requested</th>
                <th>Company</th>
                <th>Approval#</th>
                <th>Amount</th>
            </tr>

        </thead>

        <tbody>';
        $dataCus->each(function ($item) use (&$no, &$html, &$progressCount, $cuscount){
            $dataCollectionCus[] = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spcus_companyname,
                $item->spexgc_num,
                $item->totdenom,
            ];

            // dd($dataCollectionCus[0][0]);

            $html .= '<tr style="font-size: 9px;">
                <td>' . $dataCollectionCus[0][0] . '</td>
                <td>' . $dataCollectionCus[0][1] . '</td>
                <td>' . $dataCollectionCus[0][2] . '</td>
                <td  style="text-align: center;">' . $dataCollectionCus[0][3] . '</td>
                <td>' . $dataCollectionCus[0][4] . '</td>
              </tr>';

            ApprovedReleasedEach::dispatch('Generating Pdf of Approved Reports Per Customer', $progressCount++, $cuscount, Auth::user());
        });


        $progressCount = 1;
        $no = 1;

        $html .= '</tbody></table>';
        $html .= '<br><br>';
        $html .= '<table border="1" cellpadding="2" cellspacing="0" style="width: 100%">
        <thead>
           <tr>
                <th colspan="7" style="text-align: center; font-size: 12px;">Table Per Barcode</th>
            </tr>
            <tr style="text-align: center; font-size: 11px;">
                <th>No</th>
                <th>Date Requested</th>
                <th>Barcode</th>
                <th>Denomination</th>
                <th>Customer</th>
                <th>Approval#</th>
                <th>Date Approved</th>
            </tr>

        </thead>

        <tbody>';

        $dataBar->each(function ($item) use ($barcount, &$no, &$html, &$progressCount, &$totalDenom){

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

             $html .= '<tr style="font-size: 9px;">
                <td>' . $dataCollectionBar[0][0] . '</td>
                <td>' . $dataCollectionBar[0][1] . '</td>
                <td  style="text-align: center;">' . $dataCollectionBar[0][2] . '</td>
                <td>' . $dataCollectionBar[0][3] . '</td>
                <td>' . $dataCollectionBar[0][4] . '</td>
                <td  style="text-align: center;">' . $dataCollectionBar[0][5] . '</td>
                <td>' . $dataCollectionBar[0][6] . '</td>
              </tr>';


            ApprovedReleasedEach::dispatch('Generating Pdf of Approved Reports Per Barcode. ', $progressCount++, $barcount, Auth::user());
        });

        $html .= '</tbody></table>';
        $html .= '<br><br>';

        $html .= '<div style="text-align: center;">';
        $html .= '<p style="font-size: 12px; "> ' . 'Total Count Per Barcode: ' . $barcount . '</p>';
        $html .= '<p style="font-size: 12px; "> ' . 'Total Count Per Customer: ' . $cuscount . '</p>';
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

    public function releasedSpgcPdfWriteResult()
    {
    }
    public function releasedSpgcExcelWriteResult()
    {
    }
}
