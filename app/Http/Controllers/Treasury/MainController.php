<?php

namespace App\Http\Controllers\Treasury;
use App\Services\Treasury\LedgerService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function __construct(public LedgerService $ledgerService){

    }
    public function budgetLedger(Request $request)
    {
        return $this->ledgerService->budgetLedger($request);
    }

    public function gcLedger(Request $request){
        return $this->ledgerService->gcLedger($request);
    }

    
}
