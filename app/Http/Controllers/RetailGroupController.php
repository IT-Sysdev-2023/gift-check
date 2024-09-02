<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Services\RetailGroup\RetailGroupServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetailGroupController extends Controller
{
    public function __construct(public RetailGroupServices $retailgroup, public DashboardClass $dashboard) {}
    //
    public function index()
    {
        return inertia('RetailGroup/RetailGroupDashboard', [
            'count' => $this->dashboard->retailGroupDashboard()
        ]);
    }
    public function pendingGcRequest(Request $request)
    {
        return inertia('RetailGroup/PendingGc', [
            'record' => $this->retailgroup->pendingGcList($request),
            'columns' => ColumnHelper::$retail_group_pending_colums,
        ]);
    }
    public function setup(Request $request)
    {
        return inertia('RetailGroup/SetupRecommendation', [
            'approved' => $this->retailgroup->approvedData($request),
            'record' => $this->retailgroup->setupRecommendation($request) ?? [],
            'denom' => $this->retailgroup->denomination($request)->data,
            'total' => $this->retailgroup->denomination($request)->total,
        ]);
    }

    public function submitPendingRequest(Request $request)
    {
        $request->validate([
            'remarks' => 'required',
            'status' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            return $this->retailgroup->updatePromoGcRequest($request)
                ->insertIntoApprovedRequest($request)
                ->insertIntoPromoLedger($request);
        });
    }
}
