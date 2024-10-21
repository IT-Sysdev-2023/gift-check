<?php

namespace App\Services\Treasury\Dashboard;


use App\Models\AllocationAdjustment;
use App\Models\ApprovedGcrequest;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{

    protected function __construct()
    {

    }
    protected function budgetRequestTreasury($userType)
    {
        $pending = User::userTypeBudget($userType)->count();
        $statusCounts = BudgetRequest::selectRaw('
            SUM(CASE WHEN br_request_status = 1 THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN br_request_status = 2 THEN 1 ELSE 0 END) as cancelled
            ')->first();

        return (object) [
            'pending' => $pending,
            'approved' => $statusCounts->approved,
            'cancelled' => $statusCounts->cancelled
        ];

    }

    protected function storeGcRequest()
    {
        $pending = StoreGcrequest::where(function (Builder $query) {
            $query->whereIn('sgc_status', [0, 1]);
        })->where('sgc_cancel', '')->count();
        $released = ApprovedGcrequest::has('storeGcRequest.store')->has('user')->count();
        $cancelled = StoreGcrequest::where([['sgc_status', 0], ['sgc_cancel', '*']])->count();


        return (object) [
            'pending' => $pending,
            'released' => $released,
            'cancelled' => $cancelled
        ];
    }

    protected function gcProductionRequest($userType): object
    {

        $pending = ProductionRequest::whereRelation('user', 'usertype', $userType)->where('pe_status', 0)->count();
        $statusCounts = ProductionRequest::selectRaw('
            SUM(CASE WHEN pe_status = 1 THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN pe_status = 2 THEN 1 ELSE 0 END) as cancelled
            ')->first();

        return (object) [
            'pending' => $pending,
            'approved' => $statusCounts->approved,
            'cancelled' => $statusCounts->cancelled
        ];
    }

    protected function adjustments()
    {

        $budget = BudgetAdjustment::count();
        $allocation = AllocationAdjustment::count();
        return (object) [
            'budget' => $budget,
            'allocation' => $allocation
        ];
    }

    protected function specialGcRequest()
    {
        $statusCounts = SpecialExternalGcrequest::selectRaw('
        SUM(CASE WHEN spexgc_reviewed = ? AND spexgc_released = ? AND (spexgc_promo = ? OR spexgc_promo = ?) THEN 1 ELSE 0 END) as internalReviewed,
        SUM(CASE WHEN spexgc_status = ? THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN spexgc_status = ? THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN spexgc_released = ? THEN 1 ELSE 0 END) as released,
        SUM(CASE WHEN spexgc_status = ? THEN 1 ELSE 0 END) as cancelled
    ', [
            'reviewed',
            '',
            '*',
            '0',  // internalReviewed
            'pending',             // pending
            'approved',            // approved
            'released',            // released
            'cancelled'            // cancelled
        ])->first();

        return (object) [
            'pending' => $statusCounts->pending,
            'approved' => $statusCounts->approved,
            'released' => $statusCounts->released,
            'cancelled' => $statusCounts->cancelled,
            'internalReviewed' => $statusCounts->internalReviewed
        ];
    }

    protected function budget()
    {
        return LedgerBudget::currentBudget();
    }

}