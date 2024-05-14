<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentableAuthorizedToPupilProfilMiddleware
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
        if(Auth::user() && $request->user()->parentable){

            if($request->user()->parentable){

                $pupil_id = $request->route('id');

                $can = $request->user()->parentable->isMySon($pupil_id);

                if($can){

                    return $next($request);

                }
                else{

                    return abort(403, "Vous n'êtes pas authorisé à accéder à une telle page, vous n'êtes pas parent de cet apprenant! ");
                }
            }
            else{


                return abort(403, "Vous n'êtes plus authorisé à accéder à une telle page!");
            }
        }
        else{

            return abort(403, "Vous n'êtes pas authorisé à accéder à une telle page, Cette paeg est dédiée aux parents elligibles de la plateforme!");
            
        }
    }
}
