<?php

namespace App\Http\Controllers\Treasury;

use App\Services\Treasury\AdjustmentService;
use Illuminate\Routing\Controller;

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
