<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CancelledProductionRequestResource;
use App\Http\Resources\DenominationResource;
use App\Http\Resources\ProductionRequestItemResource;
use App\Http\Resources\ProductionRequestResource;

use App\Models\CancelledProductionRequest;
use App\Models\Denomination;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Rules\DenomQty;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GcProductionRequestController extends Controller
{
    public function __construct(
        public GcProductionRequestService $gcProductionRequestService,
    ) {
    }
    public function approvedProductionRequest(Request $request)
    {
        $record = $this->gcProductionRequestService->approvedRequest($request);

        return inertia(
            'Treasury/Dashboard/TableGcProduction',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Approved GC Production Request',
                'data' => ProductionRequestResource::collection($record),
                'columns' => ColumnHelper::$approvedProductionRequest,
            ]

        );
    }

    public function cancelledProductionRequest(Request $request)
    {
        $record = CancelledProductionRequest::with([
            'user:user_id,firstname,lastname',
            'productionRequest' => function ($q) {
                $q->with('user:user_id,firstname,lastname')->select('pe_requested_by', 'pe_id', 'pe_num', 'pe_date_request', 'pe_date_needed');
            }
        ])
            ->select('cpr_id', 'cpr_by', 'cpr_pro_id', 'cpr_at')
            ->orderByDesc('cpr_id')
            // ->get();
            ->paginate()->withQueryString();

        return inertia('Treasury/Dashboard/GcProduction/CancelledProduction', [
            'filters' => $request->only('search', 'date'),
            'title' => 'Cancelled Production Request',
            'records' => CancelledProductionRequestResource::collection($record),
            'columns' => ColumnHelper::$cancelledProductionRequest,
        ]);
    }

    public function viewCancelledProduction($id)
    {

        $pr = ProductionRequest::
            select('pe_id', 'pe_num', 'pe_date_request', 'pe_date_needed', 'pe_remarks', 'pe_file_docno', 'pe_requested_by')
            ->where([['pe_id', $id], ['pe_status', '2']])
            ->with([
                'cancelledProductionRequest' => function ($q) {
                    $q->with('user:user_id,firstname,lastname')->select('cpr_by', 'cpr_pro_id', 'cpr_at', 'cpr_isrequis_cancel');
                },
                'user:user_id,firstname,lastname'
            ])
            ->first();

        $barcodes = ProductionRequestItem::with(['denomination', 'barcodeStartEnd' =>  function ($q){
            $q->selectRaw('pe_entry_gc, MAX(barcode_no) as max_barcode_no, MIN(barcode_no) as min_barcode_no')
            ->groupBy('pe_entry_gc');
        }])->where('pe_items_request_id', $id)
            ->paginate()->withQueryString();

        return response()->json([
            'productionRequest' => $pr, 
            'barcodes' => [
                'data' => ProductionRequestItemResource::collection($barcodes->items()),
                'from' => $barcodes->firstItem(),
                'to' => $barcodes->lastItem(),
                'total' => $barcodes->total(),
                'links' => $barcodes->linkCollection(),

            ]
           
        ]);
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

    public function download(string $file)
    {
        return $this->gcProductionRequestService->downloadFile($file);
    }
    public function reprintRequest($id)
    {
        return $this->gcProductionRequestService->reprint($id);
    }
    public function pending(Request $request)
    {
        $pend = $this->gcProductionRequestService->pending($request);

        return inertia('Treasury/Dashboard/GcProduction/PendingGcProduction', [
            'title' => 'Update Pending Production Entry Form',
            'denomination' => DenominationResource::collection($pend->denomination),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'record' => new ProductionRequestResource($pend->production),
        ]);
    }

    public function pendingSubmission(Request $request)
    {
        return $this->gcProductionRequestService->pendingSubmit($request);
    }
}
