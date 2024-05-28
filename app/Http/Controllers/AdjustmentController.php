<?php

namespace App\Http\Controllers;

use App\Services\Adjustment\BudgetAdjustment;
use App\Services\AdjustmentService;
use App\Services\IndexService;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    public function budgetAdjustment()
    {
        return AdjustmentService::budgetAdjustment();
    }

    public function allocationAdjustment()
    {
        return AdjustmentService::allocationAdjustment();
    }
}
