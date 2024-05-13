<?php

namespace App;

use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\SpecialExternalGcrequest;
use App\Services\DashboardHandler;

class DashboardClass extends DashboardHandler
{
    /**
     * Create a new class instance.
     */

    protected function handleUserTypeTwo()
    {
        //Pending Request
        $segcpending = SpecialExternalGcrequest::countSpexgcStatus('pending');
        //Approved Request
        $segcapproved = SpecialExternalGcrequest::countSpexgcStatus('approved');
        //Reviewed Gc FOr releasing
        $segcreviewed = SpecialExternalGcrequest::where([['spexgc_reviewed', 'reviewed'], ['spexgc_released', '']])->count();
        //Reviewed Gc
        $segcreleased = SpecialExternalGcrequest::countSpexgcReleased('released');
        //Cancelled Request
        $segccancelled = SpecialExternalGcrequest::countSpexgcStatus('cancelled');

        return [
            'pending' => $segcpending,
            'approved' => $segcapproved,
            'reviewed' => $segcreviewed,
            'released' => $segcreleased,
            'cancelled' => $segccancelled
        ];
    }

    protected function handleUserOtherTypes()
    {
        return [
            'budgetRequest' => $this->budgetRequest(),
            'storeGcRequest' => $this->storeGcRequest(),
            'promoGcReleased' => PromoGcReleaseToDetail::count(),
            'institutionGcSales' => InstitutTransaction::count(),
            'gcProductionRequest' => $this->gcProductionRequest(),
            'adjustment' => $this->adjustments(),
            'specialGcRequest' => $this->specialGcRequest(),
            'budget' => $this->budget(),
            'eod' => InstitutEod::count(),
            'productionRequest' => ProductionRequest::where([['pe_generate_code', 0], ['pe_status', 1]])->get()
        ];
    }

}
