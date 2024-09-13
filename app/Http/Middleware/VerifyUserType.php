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
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        if($this->isAuthenticatedUser($userType) != $request->user()->usertype){
            return redirect('/');
        }

        return $next($request);
    }

    public function isAuthenticatedUser($type)
    {
       return array_search($type, $this->roleDashboardRoutes);
    }
}
