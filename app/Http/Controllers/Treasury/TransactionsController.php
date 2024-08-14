<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Helpers\NumberHelper;
use App\Http\Resources\DenominationResource;
use App\Models\BudgetRequest;
use App\Models\Denomination;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
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
    ) {

    }
    public function budgetRequest(Request $request)
    {
        $br = BudgetRequest::max('br_no');

        return inertia('Treasury/Transactions/BudgetRequest', [
            'title' => 'Budget Request',
            'br' => NumberHelper::leadingZero($br + 1),
            'remainingBudget' => LedgerBudget::currentBudget(),

        ]);
    }

    public function budgetRequestSubmission(Request $request)
    {
        return $this->budgetRequestService->budgetRequestSubmission($request);
    }

    //Production Requests
    public function giftCheck()
    {

        $denomination = Denomination::select('denomination', 'denom_id')->where([['denom_type', 'RSGC'], ['denom_status', 'active']])->get();
        $latestRecord = ProductionRequest::max('pe_num');
        $increment = $latestRecord ? $latestRecord + 1 : 1;

        return inertia('Treasury/Transactions/GiftCheck', [
            'title' => 'Gift Check',
            'denomination' => DenominationResource::collection($denomination),
            'prNo' => NumberHelper::leadingZero($increment),
            'remainingBudget' => LedgerBudget::currentBudget(),
        ]);
    }
    public function giftCheckStore(Request $request)
    {
        return $this->transactionProductionRequest->storeGc($request);
    }
    public function acceptProductionRequest(Request $request, $id)
    {
        $this->regularGcProcessService->approveProductionRequest($request, $id);
        return redirect()->back()->with('success', 'Successfully Processed!');
    }
}
