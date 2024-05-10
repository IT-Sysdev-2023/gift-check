<?php

namespace App\Http\Controllers;

use App\Models\AllocationAdjustment;
use App\Models\ApprovedGcrequest;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\GcAdjustment;
use App\Models\InstitutTransaction;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Dashboard extends Controller
{
    public function index()
    {

        // dd(BudgetRequest::with('user')->get());

        // dd(request()->user()->usertype);

        $bud = BudgetAdjustment::count();
        $production = GcAdjustment::count();
        $allocate = AllocationAdjustment::count();


        //BUDGET REQUEST

        //Pending Request
        $budPenReq = User::userTypeBudget(request()->user()->usertype)->count();
        //Approved Request
        $budAppReq = BudgetRequest::where('br_request_status', 1)->count();
        //Cancelled Request
        $budCanReq = BudgetRequest::where('br_request_status', 2)->count();


        //STORE GC REQUEST

        //Pending Request
        $storePenReq = StoreGcrequest::where(function (Builder $query) {
            $query->where('sgc_status', 0)
                ->orWhere('sgc_status', 1);
        })->where('sgc_cancel', '')->count();
        //Release Gc
        $storeAppReq = ApprovedGcrequest::has('storeGcRequest.stores')->has('user')->count();
        //Cancelled Request
        $storeCanReq= StoreGcrequest::where('sgc_status',0)->where('sgc_cancel','*')->count();


        //PROMO GC RELEASED 

        //Released GC
        $promoGCREl = PromoGcReleaseToDetail::count();

        //Institution GC Sales
        $instr = InstitutTransaction::count();

        //GC PRODUCTION REQUEST
        
        //Pending Request
        $proPenReq = ProductionRequest::whereHas('user', function ($query) {
            $query->where('usertype', request()->user()->usertype);
        }  )->where('pe_status', 0)->count();

        //Approved Request
        $proAppReq= ProductionRequest::where('pe_status', 1)->count();
        //Cancelled Request
        $proCanReq = ProductionRequest::where('pe_status', 2)->count();

        //Special GC Request 
        $segcpending = SpecialExternalGcrequest::where('spexgc_status','pending')->count();
        //Approved GC
        $segcapproved  = SpecialExternalGcrequest::where('spexgc_status','approved')->count();
        //Reviewed GC For Releasing
        $segcreviewed  = SpecialExternalGcrequest::where('spexgc_reviewed', 'reviewed')->where('spexgc_released', '')->where('spexgc_promo', '0')->count();
        //Released GC
        $segcapproved  = SpecialExternalGcrequest::where('spexgc_released','released')->count();
        //Cancelled Request
        $segccancelled  = SpecialExternalGcrequest::where('spexgc_status','cancelled')->count();

        //Current Budget
        // number_format( , 2) //to be continued

    }

    // function currentBudget($link)
	// {
	// 	$query = "SELECT SUM(bdebit_amt),SUM(bcredit_amt) FROM ledger_budget WHERE bcus_guide != 'dti'";

	// 	$query = $link->query($query) or die('unable to query');
	// 	$budget_row		= $query->fetch_array();
	// 	$debit 	= $budget_row['SUM(bdebit_amt)'];
	// 	$credit = $budget_row['SUM(bcredit_amt)'];

	// 	$budget = $debit - $credit;

	// 	return $budget;
	// }
}
