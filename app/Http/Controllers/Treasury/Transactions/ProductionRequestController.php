<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Envelope;
use App\Models\EnvelopeProductionReq;
use Illuminate\Http\Request;
use App\Helpers\NumberHelper;
use App\Http\Resources\DenominationResource;
use App\Models\Denomination;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Services\Treasury\RegularGcProcessService;
use App\Services\Treasury\Transactions\TransactionProductionRequest;

class ProductionRequestController extends Controller
{
    public function __construct(
        public TransactionProductionRequest $transactionProductionRequest,
        public RegularGcProcessService $regularGcProcessService
    ) {

    }

    public function giftCheck()
    {

        $denomination = Denomination::select('denomination', 'denom_id')->where([['denom_type', 'RSGC'], ['denom_status', 'active']])->get();
        $latestRecord = ProductionRequest::max('pe_num');
        $increment = $latestRecord ? $latestRecord + 1 : 1;

        return inertia('Treasury/Transactions/ProductionRequest/GiftCheck', [
            'title' => 'Gift Check',
            'denomination' => DenominationResource::collection($denomination),
            'prNo' => NumberHelper::leadingZero($increment),
            'remainingBudget' => (float) str_replace(',', '', LedgerBudget::budget()),
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
    public function envelope()
    {

        $val = Envelope::max('env_amount');
        $q = EnvelopeProductionReq::max('env_num');
        $pr = $q ? $q + 1:1;    

        return inertia('Treasury/Transactions/ProductionRequest/Envelope', [
            'title'=> 'Envelope Production Request Form',
            'prNo' => NumberHelper::leadingZero($pr),
            'envVal' => NumberHelper::currency($val),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'regularBudget' => LedgerBudget::regularBudget(),
            'specialBudget' => LedgerBudget::specialBudget()
        ]);
    }

    public function envelopeStore(Request $request)
    {
        $request->validate([
            "remarks" => 'required',
            "dateNeeded" => "required",
            "qty" => 'required|not_in:0'
        ]);

        dd('To be Continued walay code sa daan system lodi');
    }
}
