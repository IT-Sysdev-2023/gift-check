<?php

namespace App\Http\Controllers\Auth;

use App\DashboardRoutesTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    use DashboardRoutesTrait;
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        
        $request->authenticate();
        
        $request->session()->regenerate();

        $usertype = Auth::user()->usertype;
        $user_role = Auth::user()->user_role;

        if ($this->roleDashboardRoutes[$usertype] ) { //&& $usertype != $user_role
            return redirect()->intended(route("{$this->roleDashboardRoutes[$usertype]}.dashboard", absolute: false));

        }else{
            return abort(404, 'Usertype Not Found');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
