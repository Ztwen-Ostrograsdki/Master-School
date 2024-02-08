<?php

namespace App\Http\Middleware;

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
        session()->forget('afterRedirectionUlr');

        $theRouteName = Route::currentRouteName();

        $theUrl = $request->url();

        if($theRouteName !== 'connexion' && $theRouteName !== 'registration'){

            session()->put('afterRedirectionUlr', $theUrl);

        }

        if (! $request->expectsJson()) {
            return route('connexion');
        }
    }
}
