<?php

namespace App\Http\Middleware;

use App\Helpers\Redirectors\RedirectorsDriver;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        RedirectorsDriver::setUlrFromToSessionBeforeRedirection($request);

        if (! $request->expectsJson()) {
            return route('connexion');
        }
    }
}
