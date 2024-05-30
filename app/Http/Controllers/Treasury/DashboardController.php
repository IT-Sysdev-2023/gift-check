<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    public function __construct(protected DashboardClass $dashboardClass)
    {

    }

    public function index()
    {
        $userType = request()->user()->usertype === '2' && request()->user()->user_role === 2;

        if ($userType) { //only the the specialGcRequest() is displayed
            // $record = $this->dashboardClass->handleUserTypeTwo();
        } else {
            $record = $this->dashboardClass->treasuryDashboard();
        }

        return inertia('Dashboard', $record);
    }
}
