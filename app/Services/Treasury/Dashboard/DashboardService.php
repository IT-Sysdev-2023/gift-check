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
    protected function budgetRequestTreasury()
    {
        //Pending Request
        $pending = User::userTypeBudget(request()->user()->usertype)->count();

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
        //Pending Request
        $pending = StoreGcrequest::where(function (Builder $query) {
            $query->whereIn('sgc_status', [0, 1]);
        })->where('sgc_cancel', '')->count();

        //Release Gc
        $released = ApprovedGcrequest::has('storeGcRequest.store')->has('user')->count();

        //Cancelled Request
        $cancelled = StoreGcrequest::where([['sgc_status', 0], ['sgc_cancel', '*']])->count();

        return (object) [
            'pending' => $pending,
            'released' => $released,
            'cancelled' => $cancelled
        ];
    }

    protected function gcProductionRequest()
    {

        //Pending Request
        $pending = ProductionRequest::whereRelation('user', 'usertype', request()->user()->usertype)->where('pe_status', 0)->count();

        //Approved Request //Cancelled
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
        //Budget
        $budget = BudgetAdjustment::count();
        //Allocation
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

    //REFERENCES
    // public function aintUserType2(){

    //     //BUDGET Request

    //     //Pending Request
    //     $budPenReq = User::userTypeBudget(request()->user()->usertype)->count();
    //     //Approved Request
    //     $budAppReq = BudgetRequest::where('br_request_status', 1)->count();
    //     //Cancelled Request
    //     $budCanReq = BudgetRequest::where('br_request_status', 2)->count();

    //     //STORE GC REQUEST

    //     //Pending Request
    //     $storePenReq = StoreGcrequest::where(function (Builder $query) {
    //         $query->where('sgc_status', 0)
    //             ->orWhere('sgc_status', 1);
    //     })->where('sgc_cancel', '')->count();
    //     //Release Gc
    //     $storeAppReq = ApprovedGcrequest::has('storeGcRequest.stores')->has('user')->count();
    //     //Cancelled Request
    //     $storeCanReq= StoreGcrequest::where([['sgc_status',0], ['sgc_cancel','*']])->count();


    //     //PROMO GC RELEASED 

    //     //Released GC
    //     $promoGCREl = PromoGcReleaseToDetail::count();

    //     //Institution GC Sales
    //     $instr = InstitutTransaction::count();

    //     //GC PRODUCTION REQUEST

    //     //Pending Request
    //     $proPenReq = ProductionRequest::whereHas('user', function ($query) {
    //         $query->where('usertype', request()->user()->usertype);
    //     }  )->where('pe_status', 0)->count();

    //     //Approved Request
    //     $proAppReq= ProductionRequest::where('pe_status', 1)->count();
    //     //Cancelled Request
    //     $proCanReq = ProductionRequest::where('pe_status', 2)->count();

    //     //SPECIAL GC Request 
    //     $segcpending = SpecialExternalGcrequest::countSpexgcStatus('pending');
    //     //Approved GC
    //     $segcapproved  = SpecialExternalGcrequest::countSpexgcStatus('approved');
    //     //Reviewed GC For Releasing
    //     $segcreviewed  = SpecialExternalGcrequest::where([['spexgc_reviewed', 'reviewed'], ['spexgc_released', ''], ['spexgc_promo', '0']])->count();
    //     //Released GC
    //     $segcapproved  = SpecialExternalGcrequest::countSpexgcReleased('released');
    //     //Cancelled Request
    //     $segccancelled  = SpecialExternalGcrequest::countSpexgcStatus('cancelled');

    //     //Current Budget
    //     // $data = LedgerBudget::where('bcus_guide','<>', 'dti')->sum('bcredit_amt', 'bdebit_amt');


    //     $dataR = LedgerBudget::select(DB::raw('SUM(bdebit_amt) as debit_amount'), DB::raw('SUM(bcredit_amt) as credit_amount'))->whereNot('bcus_guide','dti')->first();
    //     $debit = (float) $dataR->debit_amount;
    //     $credit = (float) $dataR->credit_amount;
    //     $budget = $debit - $credit;  // number_format( $budget, 2)


    //     //EOD
    //     $eod = InstitutEod::count();

    //     //SODEXO Unknown


    //     $ProductionRequestNo = ProductionRequest::where([['pe_generate_code', 0], ['pe_status', 1]])->get();

    // }

}