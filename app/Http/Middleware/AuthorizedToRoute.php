<?php

namespace App\Http\Middleware;

use App\Helpers\Redirectors\RedirectorsDriver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthorizedToRoute
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
        
        $user = auth()->user();

        $routeName = Route::currentRouteName();

        $theUrl = $request->url();
        
        if($user){

            if($user->__canAccessToThisRoute($routeName)){

                return $next($request);
            }
            else{
                return abort('403', "Vous n'êtes pas authorisé à accéder à cette page");
            }
        }
        else{
            return redirect()->route('login');
        }
    }
}
