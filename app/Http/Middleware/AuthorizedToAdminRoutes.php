<?php

namespace App\Http\Middleware;

use App\Helpers\Redirectors\RedirectorsDriver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizedToAdminRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        RedirectorsDriver::setUlrFromToSessionBeforeRedirection($request);
        
        $user = $request->user();
        
        if(Auth::user()){

            if($user->__hasAdminAuthorization()){

                return $next($request);
            }
            else{
                return redirect()->route('get-admin-authorization');
            }
        }
        else{
            return redirect()->route('login');
        }
        
    }
}
