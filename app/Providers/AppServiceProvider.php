<?php

namespace App\Providers;

use App\DashboardRoutesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

class AppServiceProvider extends ServiceProvider
{
    use DashboardRoutesTrait;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function ($req) {

            $currentGuard = Auth::guard();

            $usertype = $currentGuard->user()->usertype;
            $user_role = $currentGuard->user()->user_role;

            if ($this->roleDashboardRoutes[$usertype]) { //&& $usertype != $user_role
                return route($this->roleDashboardRoutes[$usertype]);
            }
        });
    }
}
