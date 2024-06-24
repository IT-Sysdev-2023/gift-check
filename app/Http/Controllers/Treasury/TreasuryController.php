<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetLedgerApprovedResource;
use App\Models\BudgetRequest;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(public DashboardClass $dashboardClass, public LedgerService $ledgerService)
    {
    }

    public function index()
    {
        $record = $this->dashboardClass->treasuryDashboard();
        return inertia('Treasury/Dashboard', ['data' => $record]);
    }
    public function budgetLedger(Request $request)
    {
        return $this->ledgerService->budgetLedger($request);
    }

    public function budgetRequestApproved(Request $request)
    {
        return $this->ledgerService->budgetRequestApproved($request);
    }

    public function viewBudgetRequestApproved(BudgetRequest $id)
    {
        return $this->ledgerService->viewBudgetRequestApproved($id);
    }

    public function gcLedger(Request $request)
    {
        return $this->ledgerService->gcLedger($request);
    }
}
