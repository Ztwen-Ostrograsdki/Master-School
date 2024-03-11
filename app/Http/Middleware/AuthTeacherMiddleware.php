<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthTeacherMiddleware
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
        if(Auth::user() && $request->user()->teacher){

            if($request->user()->teacher->teaching){

                return $next($request);
            }
            else{

                $date = $request->user()->teacher->getLastTeachingDate();

                return abort(403, "Vous n'êtes plus authorisé à accéder à une telle page, Vous n'êtes plus considéré comme enseignant de la plateforme depuis le $date !");
            }
        }
        else{

            return abort(403, "Vous n'êtes pas authorisé à accéder à une telle page, Cette paeg est dédiée aux enseigants elligibles de la plateforme!");
            
        }
    }
}
