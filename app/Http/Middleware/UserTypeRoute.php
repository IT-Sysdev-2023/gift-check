<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeRoute
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
        '12' => 'eod.dashboard',
        '13' => 'storeaccounting.dashboard',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usertype = $request->usertype;
        $user_role = $request->user_role;

        if ($this->roleDashboardRoutes[$usertype]) { //&& $usertype != $user_role
            return redirect()->route($this->roleDashboardRoutes[$usertype]);
        }

        return $next($request);
    }
}
