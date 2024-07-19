<?php

namespace App\Services\Finance;

use App\Models\PromoGcRequest;

class FinanceDashboardService
{
    public function dashboard()
    {
        return inertia('Finance/FinanceDashboard', [
            'count' => [
               'appPromoCount' =>  PromoGcRequest::with('userReqby')->whereFilter()->selectPromoApproved()->count(),
            ]
        ]);
    }
}
