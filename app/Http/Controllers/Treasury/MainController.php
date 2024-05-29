<?php

namespace App\Http\Controllers\Treasury;
use App\Services\Treasury\LedgerService;
use Illuminate\Routing\Controller;

class MainController extends Controller
{
    public function budgetLedger()
    {
        return LedgerService::budgetLedger();
    }

    public function gcLedger(){
        return LedgerService::gcLedger();
    }

    
}
