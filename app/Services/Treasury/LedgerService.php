<?php

namespace App\Services\Treasury;

use App\Helpers\Excel\ExcelWriter;
use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Http\Resources\BudgetLedgerApprovedResource;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\GcLedgerResource;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\LedgerSpgc;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Services\Treasury\Dashboard\excel\ExtendsExcelService;
use Faker\Core\Number;

class LedgerService
{
    protected $record;
    protected $border;
    protected $generateUserHeader;
    protected $borderFBN;
    protected $singleBorderWithBgColor;

    public function __construct()
    {
    }
    public function budgetLedger(Request $request) //ledger_budget.php
    {
        $record =  LedgerBudget::with('approvedGcRequest.storeGcRequest.store:store_id,store_name')
            ->filter($request->only('search', 'date'))
            ->select(
                [
                    'bledger_id',
                    'bledger_no',
                    'bledger_trid',
                    'bledger_datetime',
                    'bledger_type',
                    'bdebit_amt',
                    'bcredit_amt'
                ]
            )
            ->orderByDesc('bledger_no')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => ColumnHelper::$budget_ledger_columns,
        ]);
    }

    

    public function gcLedger(Request $request) // gccheckledger.php
    {

        $record = LedgerCheck::with('user:user_id,firstname,lastname')
            ->select(
                'cledger_id',
                'c_posted_by',
                'cledger_no',
                'cledger_datetime',
                'cledger_type',
                'cledger_desc',
                'cdebit_amt',
                'ccredit_amt',
                'c_posted_by'
            )
            ->orderBy('cledger_id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => GcLedgerResource::collection($record),
            'columns' => ColumnHelper::$gc_ledger_columns,
        ]);
    }
    public static function spgcLedger($filters)
    {
        return LedgerSpgc::select(
            'spgcledger_id',
            'spgcledger_no',
            'spgcledger_trid',
            'spgcledger_datetime',
            'spgcledger_type',
            'spgcledger_debit',
            'spgcledger_credit'
        )->filter($filters)
            ->paginate(10)->withQueryString();
    }
    public static function approvedSpgcPdfWriteResult($dateRange, $dataCus, $dataBar)
    {

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

        $headersBar = ['No.', 'Date Requested', 'Barcode', 'Denom', 'Customer', 'Approval#', 'Date Approved'];

        $dataCollectionBar = [];

        $no = 1;
        $totalDenom = 0;

        $dataBar->each(function ($item) use (&$dataCollectionBar, &$no, &$totalDenom) {

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
        });


        $headersCus = ['No.', 'Date Requested', 'Company', 'Approval#', 'Total Amount'];

        $dataCollectionCus[] = [];

        $dataCus->each(function ($item) use (&$dataCollectionCus, &$no) {
            $dataCollectionCus[] = [
                $no++,
                Date::parse($item->datereq)->toFormattedDateString(),
                $item->spcus_companyname,
                $item->spexgc_num,
                $item->totdenom,
            ];
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
        foreach ($headersCus as $header) {
            $html .= '<th style="font-size: 12px;">' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        foreach ($dataCollectionCus as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
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
        foreach ($dataCollectionBar as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="width:100%; border-collapse:collapse; margin: 0 auto; text-align: center; font-size: 10px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
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

