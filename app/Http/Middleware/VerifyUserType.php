<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\DashboardRoutesTrait;

class VerifyUserType
{
    use DashboardRoutesTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$userType): Response
    {

    if(in_array($this->roleDashboardRoutes[$request->user()->usertype], $userType)){
        return $next($request);
    }

    return response()->view('errors.401', [], 401);

    }
}
