<?php

namespace App;

use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\SpecialExternalGcrequest;
use App\Services\Treasury\Dashboard\DashboardService;

class DashboardClass extends DashboardService
{
    /**
     * Create a new class instance.
     */

     public function __construct() {
     }
    public function treasuryDashboard()
    {
        return [
            'budgetRequest' => $this->budgetRequest(),
            'storeGcRequest' => $this->storeGcRequest(),
            'promoGcReleased' => PromoGcReleaseToDetail::count(),
            'institutionGcSales' => InstitutTransaction::count(),
            'gcProductionRequest' => $this->gcProductionRequest(),
            'adjustment' => $this->adjustments(),
            'specialGcRequest' => $this->specialGcRequest(), //Duplicated above use Spatie Permission instead
            'budget' => $this->budget(),
            'eod' => InstitutEod::count(),
            'productionRequest' => ProductionRequest::where([['pe_generate_code', 0], ['pe_status', 1]])->get()
        ];
    }

    public function retailDashboard(){
        //
    }
    public function financeDashboard(){
        //
    }
    public function marketingDashboard(){
        //
    }


}
