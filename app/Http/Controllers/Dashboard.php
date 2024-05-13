<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Models\AllocationAdjustment;
use App\Models\ApprovedGcrequest;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\GcAdjustment;
use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Dashboard extends Controller
{

    public function __construct(protected DashboardClass $dashboardClass){

    }

    public function index()
    {
        $userType = request()->user()->usertype === '2' && request()->user()->user_role === 2;
        
        if($userType){
            $record = $this->dashboardClass->handleUserTypeTwo();
        }else{
            $record = $this->dashboardClass->handleUserOtherTypes();
        }

        return inertia('Dashboard', $record);
    }
}
