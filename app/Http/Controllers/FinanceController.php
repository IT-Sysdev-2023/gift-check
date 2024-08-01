<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Http\Requests\PromoForApprovalRequest;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\SpgcLedgerResource;
use App\Models\LedgerBudget;
use App\Services\Finance\ApprovedPendingPromoGCRequestService;
use App\Services\Finance\ApprovedReleasedPdfExcelService;
use App\Services\Finance\ApprovedReleasedReportService;
use App\Services\Finance\SpgcLedgerExcelService;
use App\Services\Finance\SpgcService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class FinanceController extends Controller
{

    public function __construct(public LedgerService $ledgerService,
    public ApprovedReleasedPdfExcelService $appRelPdfExcelService,
    public DashboardClass $dashboardClass)
    {
    }

    public function index()
    {
        return inertia('Finance/FinanceDashboard', [
            'count' => $this->dashboardClass->financeDashboard(),
        ]);
    }
    public function budgetLedger(Request $request)
    {
        $record = $this->ledgerService->budgetLedger($request);

        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => \App\Helpers\ColumnHelper::$budget_ledger_columns,
        ]);
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
        if ($request->ext  == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext  == 'excel') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);

        }
    }
    public function releasedSpgcPdfExcelFunction(Request $request)
    {
        if ($request->ext  == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext  == 'excel') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        }
    }

    public function generateSpgcPromotionalExcel(Request $request)
    {

        $record = LedgerService::spgcLedgerToExcel($request);

        $save = (new SpgcLedgerExcelService())->record($record)->date($request->date)->writeResult()->save();

        return Inertia::render('Finance/Results/SpgcLedgerResult', [
            'filePath' => $save,
        ]);

    }
    public function pendingPromoRequest(Request $request)
    {
        return (new ApprovedPendingPromoGCRequestService())->pendingPromoGCRequestIndex($request);
    }

    public function approveRequest(PromoForApprovalRequest $request)
    {

        return (new ApprovedPendingPromoGCRequestService())->approveRequest($request);
    }

    public function approvedPromoRequest(Request $request)
    {
        return (new ApprovedPendingPromoGCRequestService())->approvedPromoGCRequestIndex($request);
    }
}
