<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->usertype === '2' && $request->user_role !== 2) {
            dd(1);
            //Treasury
            // return redirect('home');
        } elseif ($request->usertype === '7' && $request->user_role !== 7) {
            dd(2);
            //Retail
        } elseif ($request->usertype === '9' && $request->user_role !== 9){
            //Accounting
        } elseif ($request->usertype === '3' && $request->user_role !== 3){
            //Finance
        }  elseif ($request->usertype === '4' && $request->user_role !== 4){
            //Custodian
        } elseif ($request->usertype === '6' && $request->user_role !== 6){
            //Marketing
        }else{
            abort(500, 'Internal Server Error');
        }

        return $next($request);
    }
}
