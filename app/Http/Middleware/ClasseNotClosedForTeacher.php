<?php

namespace App\Http\Middleware;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Closure;
use Illuminate\Http\Request;

class ClasseNotClosedForTeacher
{
    use ModelQueryTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
    }
}
