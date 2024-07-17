<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\ProductionRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return $this->ledgerService->budgetLedger($request);
    }
    public function gcLedger(Request $request)
    {
        return $this->ledgerService->gcLedger($request);
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
    public function viewApprovedRequest(BudgetRequest $id)
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
    public function viewCancelledRequest(BudgetRequest $id)
    {
        return $this->budgetRequestService->viewCancelledRequest($id);
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
    public function viewCancelledGc($id)
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

    public function viewApprovedProduction($id)
    {
        $pr = ProductionRequest::find($id)
            ->load(
                'user:user_id,firstname,lastname',
                'approvedProductionRequest.user:user_id,firstname,lastname'
            );
        $ap = new ProductionRequestResource($pr);

        $totalprodReq = ProductionRequestItem::selectRaw('SUM(production_request_items.pe_items_quantity * denomination) as total')->join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
            ->where('pe_items_request_id', $id)
            ->get();

        $items = ProductionRequestItem::selectRaw(
            "pe_items_quantity * denomination AS totalRow,
            denomination.denomination,
            (SELECT barcode_no FROM gc WHERE gc.denom_id = production_request_items.pe_items_denomination AND gc.pe_entry_gc = $id LIMIT 1) AS barcode_start,
            (SELECT barcode_no FROM gc WHERE gc.denom_id = production_request_items.pe_items_denomination AND gc.pe_entry_gc = $id ORDER BY barcode_no DESC LIMIT 1 ) AS barcode_end,
            production_request_items.pe_items_quantity,
            production_request_items.pe_items_denomination"
        )
            ->join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
            ->where('pe_items_request_id', $id)
            ->get();

        $proreq = ProductionRequestItem::join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
            ->select('denomination.denomination', 'production_request_items.pe_items_quantity')
            ->where('production_request_items.pe_items_request_id', $id)
            ->get();

        $data = [
            'total' => NumberHelper::currency($items->sum('totalRow')),
            'productionRequest' => $ap,
            'totalProductionRequest' => $totalprodReq,
            'items' => $items->map(function ($i) {
                $i->denomination = NumberHelper::currency($i->denomination);
                $i->totalRow = NumberHelper::currency($i->totalRow);
                return $i;
            }),
        ];
        return response()->json($data);
    }
}
