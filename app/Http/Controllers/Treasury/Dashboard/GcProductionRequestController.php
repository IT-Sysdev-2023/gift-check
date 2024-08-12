<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductionRequestResource;
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
}
