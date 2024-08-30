<?php

namespace App;

trait DashboardRoutesTrait
{
    protected $roleDashboardRoutes = [
        '1' => 'admin.dashboard',
        '2' => 'treasury.dashboard',
        '3' => 'finance.dashboard',
        '4' => 'custodian.dashboard',
        '6' => 'marketing.dashboard',
        '7' => 'retail.dashboard',
        '8' => 'retailgroup.dashboard',
        '9' => 'accounting.dashboard',
        '10' => 'iad.dashboard',
    ];
}
