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
    ];

    protected function redirectToDashboardDestination(string $usertype, int $user_role)
    {

        if ($this->roleDashboardRoutes[$usertype] && $usertype != $user_role) {
            return route($this->roleDashboardRoutes[$usertype]);
        } else {
            return abort(404, 'Not Found');
        }

    }
}
