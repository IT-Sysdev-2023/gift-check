<?php

namespace App;

use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\PromoGcRequest;
use App\Models\RequisitionForm;
use App\Models\SpecialExternalGcrequest;
use App\Services\Finance\FinanceDashboardService;
use App\Services\Treasury\Dashboard\DashboardService;

class DashboardClass extends DashboardService
{
    /**
     * Create a new class instance.
     */

    public function __construct() {}
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

    public function retailDashboard()
    {
        //
    }
    public function financeDashboard()
    {
        return [
            'appPromoCount' =>  PromoGcRequest::with('userReqby')
                ->whereFilterForApproved()
                ->selectPromoRequest()
                ->count(),
            'penPomoCount' =>  PromoGcRequest::with('userReqby')
                ->whereFilterForPending()
                ->selectPromoRequest()
                ->count(),
        ];
    }
    public function marketingDashboard()
    {
        //
    }
    public function iadDashboard()
    {
        return [
            'countReceiving' => RequisitionForm::where('used', null)->count(),
        ];
    }
    public function custodianDashboard()
    {
        return [
            'countIntExRequest' => SpecialExternalGcrequest::selectFilterEntry()
                ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
                ->where('spexgc_status', 'pending')
                ->where('spexgc_addemp', 'pending')
                ->orderByDesc('spexgc_num')
                ->count(),

            'countApproved' =>   SpecialExternalGcrequest::with('specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
                ->selectFilterApproved()
                ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
                ->where('spexgc_status', 'approved')
                ->where('reqap_approvedtype', 'Special External GC Approved')->count(),

        ];
    }
}
