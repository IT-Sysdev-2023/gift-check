<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\GcLedgerResource;
use App\Http\Resources\ProductionRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\BudgetRequest;
use App\Models\Gc;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\RequisitionEntry;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(
        public DashboardClass $dashboardClass,
        public LedgerService $ledgerService,
        public BudgetRequestService $budgetRequestService,
        public StoreGcRequestService $storeGcRequestService,
        public GcProductionRequestService $gcProductionRequestService,
    ) {
    }
    public function index()
    {
        $record = $this->dashboardClass->treasuryDashboard();

        return inertia('Treasury/TreasuryDashboard', ['data' => $record]);
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
    public function gcLedger(Request $request)
    {
        $record = $this->ledgerService->gcLedger($request);
        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => GcLedgerResource::collection($record),
            'columns' =>  \App\Helpers\ColumnHelper::$gc_ledger_columns,
        ]);
    }

    //BUDGET REQUEST
    public function approvedRequest(Request $request)
    {
        $record = $this->budgetRequestService->approvedRequest($request);
        return inertia(
            'Treasury/BudgetRequest/TableApproved',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Approved Budget Request',
                'data' => BudgetRequestResource::collection($record),
                'columns' => ColumnHelper::$approved_buget_request,
            ]

        );
    }
    public function viewApprovedRequest(BudgetRequest $id): JsonResponse
    {
        $data = $this->budgetRequestService->viewApprovedRequest($id);
        return response()->json($data);
    }
    public function pendingRequest()
    {
        $record = $this->budgetRequestService->pendingRequest();

        return inertia(
            'Treasury/BudgetRequest/PendingRequest',
            [
                'currentBudget' => LedgerBudget::currentBudget(),
                'title' => 'Update Budget Entry Form',
                'data' => $record,
            ]

        );
    }
    public function submitBudgetEntry(BudgetRequest $id, Request $request)
    {
        return $this->budgetRequestService->submitBudgetEntry($id, $request);
    }
    public function downloadDocument($file)
    {
        return $this->budgetRequestService->downloadDocument($file);
    }
    public function cancelledRequest(Request $request)
    {
        $record = $this->budgetRequestService->cancelledRequest($request);

        return inertia(
            'Treasury/BudgetRequest/TableApproved',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Cancelled Budget Request',
                'data' => BudgetRequestResource::collection($record),
                'columns' => ColumnHelper::$cancelled_buget_request,
            ]

        );
    }
    public function viewCancelledRequest(BudgetRequest $id): JsonResponse
    {
        $record = $this->budgetRequestService->viewCancelledRequest($id);
        return response()->json($record);
    }

    //STORE GC
    public function pendingRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->pendingRequest($request);

        return inertia(
            'Treasury/StoreGcRequest/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Pending Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingStoreGcRequest,
            ]

        );
    }
    public function releasedGc(Request $request)
    {
        $record = $this->storeGcRequestService->releasedGc($request);

        return inertia(
            'Treasury/StoreGcRequest/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Released Gc',
                'data' => ApprovedGcRequestResource::collection($record),
                'columns' => ColumnHelper::$releasedStoreGcRequest,
            ]

        );
    }
    public function cancelledRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->cancelledRequest($request);
        return inertia(
            'Treasury/StoreGcRequest/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Cancelled Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$cancelledStoreGcRequest,
            ]

        );
    }
    public function reprint($id)
    {
        $pdfContent = $this->storeGcRequestService->reprint($id);

        return response($pdfContent, 200)->header('Content-Type', 'application/pdf');
    }
    public function viewCancelledGc($id): JsonResponse
    {
        $record = $this->storeGcRequestService->viewCancelledGc($id);
        return response()->json($record);
    }

    //GC PRODUCTION REQUEST
    public function approvedProductionRequest(Request $request)
    {

        $record = $this->gcProductionRequestService->approvedRequest($request);

        return inertia(
            'Treasury/ProductionRequest/TableProduction',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Approved GC Production Request',
                'data' => ProductionRequestResource::collection($record),
                'columns' => ColumnHelper::$approvedProductionRequest,
            ]

        );
    }
    public function viewApprovedProduction($id): JsonResponse
    {
       
        $record = $this->gcProductionRequestService->viewApprovedProduction($id);
        $data = [
            'total' => $record->totalRow,
            'productionRequest' => new ProductionRequestResource($record->productionRequest),
            'items' => $record->transformItems
        ];
        return response()->json($data);
    }
    public function viewBarcodeGenerate($id): JsonResponse
    {
        $record = $this->gcProductionRequestService->viewBarcodeGenerated($id);

        return response()->json($record);
    }
    public function viewRequisition($id): JsonResponse
    {
        $record = $this->gcProductionRequestService->viewRequisition($id);
        return response()->json($record);
    }

    //SPECIAL GC REQUEST
    public function pendingSpecialGc(){
        dd(1);
    }
}
