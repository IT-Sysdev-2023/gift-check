<?php

namespace App\Services;

use App\Models\AllocationAdjustment;
use App\Models\ApprovedGcrequest;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{

    protected function __construct()
    {

    }
    protected function budgetRequest()
    {
        //Pending Request
        $pending = User::userTypeBudget(request()->user()->usertype)->count();
        //Approved Request
        $approved = BudgetRequest::where('br_request_status', 1)->count();
        //Cancelled Request
        $cancelled = BudgetRequest::where('br_request_status', 2)->count();

        return (object) [
            'pending' => $pending,
            'approved' => $approved,
            'cancelled' => $cancelled
        ];

    }

    protected function storeGcRequest()
    {
        //Pending Request
        $pending = StoreGcrequest::where(function (Builder $query) {
            $query->where('sgc_status', 0)
                ->orWhere('sgc_status', 1);
        })->where('sgc_cancel', '')->count();

        //Release Gc
        $released = ApprovedGcrequest::has('storeGcRequest.stores')->has('user')->count();

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
        $pending = ProductionRequest::whereHas('user', function ($query) {
            $query->where('usertype', request()->user()->usertype);
        })->where('pe_status', 0)->count();

        //Approved Request
        $approved = ProductionRequest::where('pe_status', 1)->count();

        //Cancelled Request
        $cancelled = ProductionRequest::where('pe_status', 2)->count();
        return (object) [
            'pending' => $pending,
            'approved' => $approved,
            'cancelled' => $cancelled
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
        $pending = SpecialExternalGcrequest::spexgcStatus('pending')->count();
        //Approved GC
        $approved = SpecialExternalGcrequest::spexgcStatus('approved')->count();
        //Reviewed GC For Releasing
        $reviewed = SpecialExternalGcrequest::where([['spexgc_reviewed', 'reviewed'], ['spexgc_released', ''], ['spexgc_promo', '0']])->count();
        //Released GC
        $released = SpecialExternalGcrequest::countSpexgcReleased('released');
        //Cancelled Request
        $cancelled = SpecialExternalGcrequest::spexgcStatus('cancelled')->count();

        return (object) [
            'pending' => $pending,
            'approved' => $approved,
            'reviewed' => $reviewed,
            'released' => $released,
            'cancelled' => $cancelled
        ];
    }

    protected function budget()
    {
        $res = LedgerBudget::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))->whereNot('bcus_guide', 'dti')->first();
        return bcsub($res->debit, $res->credit, 2);
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