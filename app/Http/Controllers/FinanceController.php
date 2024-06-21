<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Http\Resources\SpgcLedgerResource;
use App\Services\Finance\ApprovedReleasedReportService;
use App\Services\Finance\SpgcService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;
class FinanceController extends Controller
{

    public function __construct(public LedgerService $ledgerService)
    {
    }

    public function index()
    {
        return inertia('Finance/FinanceDashboard');
    }
    public function budgetLedger(Request $request)
    {
        return $this->ledgerService->budgetLedger($request);
    }

    public function spgcLedger(Request $request)
    {
        $data = LedgerService::spgcLedger($request);

        $operators = SpgcService::operatorsFn();

        return inertia('Finance/SpgcLedger', [
            'data' => SpgcLedgerResource::collection($data),
            'columns' => ColumnHelper::$budget_ledger_columns,
            'operators' => $operators,
            'filters' => $request->only([
                'search',
                'date'
            ])
        ]);
    }

    public function approvedAndReleasedSpgc(Request $request)
    {
        // dd($request->approvedType);
        $dataCus = ApprovedReleasedReportService::approvedReleasedQueryCus($request);
        $dataBar = ApprovedReleasedReportService::approvedReleasedQueryBar($request);

        return inertia('Finance/ApprovedAndReleaseSpgc', [
            'columns' => [
                'columnsCus' => ColumnHelper::$cus_table_columns,
                'columnsBar' => ColumnHelper::$bar_table_columns,
            ],
            'data' => [
                'dataCus' => $dataCus,
                'dataBar' => $dataBar
            ],
            'filters' => $request->only([
                'dateRange',
                'search',
                'key'
            ])
        ]);
    }
    public function approvedSpgdcPdfExcelFunction(Request $request)
    {
        if($request->ext  == 'pdf') {

            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());

            return $this->ledgerService->approvedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar);

        }elseif($request->ext  == 'excel'){

            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());

            return $this->ledgerService->approvedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar);
        }
    }
    public function releasedSpgcPdfExcelFunction(Request $request)
    {

    }
}
