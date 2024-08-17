<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Http\Requests\PromoForApprovalRequest;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\SpgcLedgerResource;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Models\SpecialExternalGcrequest;
use App\Services\Finance\ApprovedPendingPromoGCRequestService;
use App\Services\Finance\ApprovedReleasedPdfExcelService;
use App\Services\Finance\ApprovedReleasedReportService;
use App\Services\Finance\SpgcLedgerExcelService;
use App\Services\Finance\SpgcService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class FinanceController extends Controller
{

    public function __construct(
        public LedgerService $ledgerService,
        public ApprovedReleasedPdfExcelService $appRelPdfExcelService,
        public DashboardClass $dashboardClass
    ) {
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
        if ($request->ext == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext == 'excel') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);

        }
    }
    public function releasedSpgcPdfExcelFunction(Request $request)
    {
        if ($request->ext == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext == 'excel') {
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


    public function specialGcPending(Request $request)
    {

        $gcType = $request->type;

        $external = SpecialExternalGcRequest::join('special_external_gcrequest_items', 'special_external_gcrequest_items.specit_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_num', '442')
            ->where('spexgc_addemp', 'done')
            ->where('spexgc_promo', '0')
            ->get();

        $external->transform(function ($item) {
            dd($item->toArray());
            $item->total = number_format($item->specit_denoms * $item->specit_qty,2);
            $item->fullname = ucwords($item->firstname . ' ' . $item->lastname);
            $item->dateRequeted = Date::parse($item->spexgc_datereq)->format('Y-F-d');
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('Y-F-d');
            return $item;
        });
        dd($external->toArray());

        // dd($external->count());


        $internal = SpecialExternalGcrequest::where('spexgc_status', 'pending')
            ->where('spexgc_addemp', 'done')
            ->where('spexgc_promo', '*')
            ->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->get();

        $internal->transform(function ($item) {
            $item->total = $item->specit_denoms * $item->specit_qty;
            $item->fullname = ucwords($item->firstname . ' ' . $item->lastname);
            $item->dateRequeted = Date::parse($item->spexgc_datereq)->format('Y-F-d');
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('Y-F-d');
            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['RFSEGC #', 'Date Requested', 'Date Needed', 'Total Denomination', 'Customer', 'Requested by', 'View'],
            ['spexgc_num', 'dateRequeted', 'dateNeed', 'total', 'spcus_acctname', 'fullname', 'View']
        );

        return Inertia::render('Finance/SpecialGcPending', [
            'external' => $external,
            'internal' => $internal,
            'columns' => ColumnHelper::getColumns($columns),
            'gctype' => $gcType
        ]);
    }

    public function SpecialGcApprovalForm(Request $request)
    {
        $gcType = $request->gcType;
        $id = $request->id;

        $request = SpecialExternalGcRequest::join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('access_page', 'access_page.access_no', '=', 'users.usertype')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('special_external_gcrequest.spexgc_id', $id)
            ->get();


        return Inertia::render('Finance/SpecialGcApprovalForm', [
            'data' => $request,
            'type' => $gcType
        ]);


    }


}
