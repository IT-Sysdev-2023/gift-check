<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Helpers\NumberHelper;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\RegularGcProcessService;
use App\Services\Treasury\Transactions\TransactionProductionRequest;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{

    public function __construct(
        public BudgetRequestService $budgetRequestService,
        public TransactionProductionRequest $transactionProductionRequest,
        public RegularGcProcessService $regularGcProcessService
    ) {}
    public function budgetRequest(Request $request)
    {
        $br = BudgetRequest::max('br_no');

        if ($br) {
            $br = BudgetRequest::max('br_no');
        } else {
            $br = 1;
        }
        return inertia('Treasury/Transactions/BudgetRequest', [
            'title' => 'Budget Request',
            'br' => NumberHelper::leadingZero($br + 1),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'regularBudget' => LedgerBudget::regularBudget(),
            'specialBudget' => LedgerBudget::specialBudget()
        ]);
    }

    public function budgetRequestSubmission(Request $request)
    {
        return $this->budgetRequestService->budgetRequestSubmission($request);
    }
}

