<?php

namespace App;

trait DashboardRoutesTrait
{
    protected $roleDashboardRoutes = [
        '2' => 'treasury.dashboard',
        '7' => 'retail.dashboard',
        '9' => 'accounting.dashboard',
        '3' => 'finance.dashboard',
        '4' => 'custodian.dashboard',
        '6' => 'marketing.dashboard',
        '1' => 'admin.dashboard',
    ];
}
