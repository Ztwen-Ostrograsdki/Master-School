<?php

namespace App\Http\Middleware;

use App\Helpers\Redirectors\RedirectorsDriver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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

        if(Auth::user()){
            
            if(Auth::user()->isAdmin() || Auth::user()->id == 1){
                
                return $next($request);
            }
            return abort(403, "Vous n'êtes pas authorisé");
        }
        return redirect(route('login'));
    }





}
