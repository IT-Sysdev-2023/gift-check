<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetRequestResource;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetRequestController extends Controller
{
    public function __construct(
        public BudgetRequestService $budgetRequestService,
    ) {
    }
    public function approvedRequest(Request $request)
    {
        $record = $this->budgetRequestService->approvedRequest($request);
        return inertia(
            'Treasury/Dashboard/TableApproved',
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
            'Treasury/Dashboard/PendingRequestTreasury',
            [
                'regularBudget' => LedgerBudget::regularBudget(),
                'specialBudget' => LedgerBudget::specialBudget(),
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
            'Treasury/Dashboard/TableApproved',
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
}
