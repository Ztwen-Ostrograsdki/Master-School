<?php

namespace App\Http;

use App\Http\Middleware\AuthorizedToRoute;
use App\Http\Middleware\ClasseNotClosedForTeacher;
use App\Http\Middleware\NotYetAuthenticateAdmin;
use App\Http\Middleware\ParentableAuthorizedToPupilProfilMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \GeneaLabs\LaravelCaffeine\Http\Middleware\LaravelCaffeineDripMiddleware::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Laravel\Jetstream\Http\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];


    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'classeNotClosedForTeacher' => \App\Http\Middleware\ClasseNotSecureForTeacher::class,
        'parentable.authorized2pupilprofil' => \App\Http\Middleware\ParentableAuthorizedToPupilProfilMiddleware::class,
        'user.authorized2route' => \App\Http\Middleware\AuthorizedToRoute::class,
        'authorized.admin' => \App\Http\Middleware\AuthorizedToAdminRoutes::class,
        'admin.authenticate' => \App\Http\Middleware\AuthorizedToAdminRoutes::class,
        'admin.not.authenticate' => \App\Http\Middleware\NotYetAuthenticateAdmin::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'user.self' => \App\Http\Middleware\UserMiddleware::class,
        'user.teacher' => \App\Http\Middleware\AuthTeacherMiddleware::class,
        'master' => \App\Http\Middleware\Master::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'verifiedUser' => \App\Http\Middleware\ThisUserHasVerifiedEmail::class,
        'notBlockedUser' => \App\Http\Middleware\NotBlockedUser::class,
        'notFullReportedUser' => \App\Http\Middleware\NotFullReportedUser::class,
    ];
}
