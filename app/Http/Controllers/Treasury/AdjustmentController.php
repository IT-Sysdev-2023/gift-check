<?php

namespace App\Http\Controllers\Treasury;

use App\Helpers\NumberHelper;
use App\Models\BudgetAdjustment;
use App\Models\LedgerBudget;
use App\Services\Treasury\AdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    public function __construct(public AdjustmentService $adjustmentService){
    }
    public function budgetAdjustment()
    {
        return AdjustmentService::budgetAdjustment();
    }

    public function allocationAdjustment()
    {
        return AdjustmentService::allocationAdjustment();
    }

    public function viewAllocationAdjustment($id)
    {
        return AdjustmentService::viewAllocationAdjustment($id);
    }
    public function budgetAdjustments(Request $request){
        $adjustmentNo = DB::table('budgetadjustment')->max('adj_no');
        $adj = $adjustmentNo ? $adjustmentNo + 1 : 1;
        return inertia('Treasury/Adjustment/BudgetAdjustment', [
            'title' => 'Budget Adjustment',
            'adjustmentNo' => NumberHelper::leadingZero($adj),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'regularBudget' => LedgerBudget::regularBudget(),
            'specialBudget' => LedgerBudget::specialBudget()]);
    }

    public function storeBudgetAdjustment(Request $request)
    {
        return $this->adjustmentService->storeBudgetAdjustment($request);
    }
}
