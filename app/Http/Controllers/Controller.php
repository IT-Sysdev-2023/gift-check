<?php

namespace App\Http\Controllers;
use App\Services\IndexService;

abstract class Controller
{
    public function budgetLedger()
    {
        return IndexService::budgetLedger();
    }

    public function gcLedger(){
        return IndexService::gcLedger();
    }

    
}
