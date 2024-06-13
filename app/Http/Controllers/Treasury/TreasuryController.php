<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(public DashboardClass $dashboardClass)
    {
    }

    public function index()
    {
        // dd(1);
        $record = $this->dashboardClass->treasuryDashboard();
        return inertia('Treasury/Dashboard', ['data' => $record]);
    }
}
