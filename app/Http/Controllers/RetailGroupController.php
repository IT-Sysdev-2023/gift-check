<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Services\RetailGroup\RetailGroupServices;
use Illuminate\Http\Request;

class RetailGroupController extends Controller
{
    public function __construct(public RetailGroupServices $retailgroup) {}
    //
    public function index()
    {
        return inertia('RetailGroup/RetailGroupDashboard', []);
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
            'record' => $this->retailgroup->setupRecommendation($request),
            'denom' => $this->retailgroup->denomination($request)->data,
            'total' => $this->retailgroup->denomination($request)->total,
        ]);
    }
}
