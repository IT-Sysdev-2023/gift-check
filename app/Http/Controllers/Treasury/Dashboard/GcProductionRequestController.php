<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\DenominationResource;
use App\Http\Resources\ProductionRequestResource;

use App\Models\Denomination;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
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
        $dept = $request->user()->usertype;

        $pr = ProductionRequest::select('pe_requested_by', 'pe_id', 'pe_file_docno', 'pe_date_needed', 'pe_remarks', 'pe_num', 'pe_date_request', 'pe_group')->withWhereHas('user', fn($q) => $q->select('user_id', 'firstname', 'lastname')
            ->where('usertype', $dept))
            ->where('pe_status', '0')->first();

        $denoms = Denomination::select('denomination', 'denom_id')->with([
            'productionRequestItems' => function ($query) use ($pr) {
                $query->select('pe_items_denomination', 'pe_items_quantity')
                    ->where('pe_items_request_id', $pr->pe_id);
            }
        ])
            ->where([
                ['denom_type', 'RSGC'],
                ['denom_status', 'active']
            ])
            ->get();
        return inertia('Treasury/Dashboard/GcProduction/PendingGcProduction', [
            'title' => 'Update Pending Production Entry Form',
            'denomination' => DenominationResource::collection($denoms),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'record' => new ProductionRequestResource($pr),
        ]);
    }

    public function pendingSubmission(Request $request)
    {
        return $this->gcProductionRequestService->pendingSubmit($request);
    }
}
