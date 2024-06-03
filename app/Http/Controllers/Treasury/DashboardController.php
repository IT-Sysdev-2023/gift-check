<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\UserTypeTraits;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    use UserTypeTraits;

    public function __construct(public DashboardClass $dashboardClass)
    {
    }

    public function index()
    {

        // if ($this->userType('2') && $this->userRole(2)) { //only the the specialGcRequest() is displayed
        //     // $record = $this->dashboardClass->handleUserTypeTwo();
        // } else {
            $record = $this->dashboardClass->treasuryDashboard();
        // }

        return inertia('Dashboard', ['data' => $record]);
    }
}
