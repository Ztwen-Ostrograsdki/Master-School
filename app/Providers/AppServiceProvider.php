<?php

namespace App\Providers;

use App\Models\FollowingSystem;
use App\Models\UserOnlineSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('isAdmin', function($user = null){
            if(Auth::user()){
                if($user == null){
                    $user = Auth::user();
                }
                if($user->isAdmin() || $user->id == 1){
                    return true;
                }
                return false;
            }
            return false;

        });

        Blade::if('isNotAdmin', function($user = null){
            if(Auth::user()){
                if($user == null){
                    $user = Auth::user();
                }
                if(!$user->isAdmin()){
                    return false;
                }
                return true;
            }
            return true;
        });

        Blade::if('isNotMaster', function($user = null){
            if(Auth::user()){
                if($user == null){
                    $user = Auth::user();
                }
                if($user->isAdmin() && !$user->isAdminAs('master') && $user->id !== 1){
                    return true;
                }
                return false;
            }
            return true;

        });
        Blade::if('isMaster', function($user = null){
            if(Auth::user()){
                if($user == null){
                    $user = Auth::user();
                }
                if($user->isAdmin() && $user->isAdminAs('master')){
                    return true;
                }
                return false;
            }
            return false;

        });

        Blade::if('isMySelf', function($user){
            if($user->id == Auth::user()->id){
                return true;
            }
            return false;

        });
        Blade::if('isNotMySelf', function($user){
            if($user->id !== Auth::user()->id){
                return true;
            }
            return false;

        });

        Blade::if('fixedHeaderForRoute', function(){
            $route = Route::currentRouteName();
            $routesNames = config('app.routes');
            if(in_array($route, $routesNames)){
                return true;
            }
            return false;

        });
        Blade::if('isRoute', function($routeName){
            if(Route::currentRouteName() == $routeName){
                return true;
            }
            return false;

        });
        
        Blade::if('routeHas', function($routeName){
            if(Route::has($routeName)){
                return true; 
            }
            return false;

        });

        Blade::if('routeHasNot', function($routeName){
            if(Route::has($routeName)){
                return false;
            }
            return true;

        });

        Blade::if('isNotRoute', function($routeName){
            if(Route::currentRouteName() == $routeName){
                return false;
            }
            return true;

        });

        Blade::if('isRoutes', function($routesName){
            foreach($routesName as $routeName){
                if(Route::has($routeName)){
                    if(in_array(Route::currentRouteName(), $routesName)){
                        return true;
                    }
                    return false;
                }
                return false;
            }

        });

        Blade::if('isNotRoutes', function($routesName){
            foreach($routesName as $routeName){
                if(Route::has($routeName)){
                    if(in_array(Route::currentRouteName(), $routesName)){
                        return false;
                    }
                    return true;
                }
                return true;
            }

        });



    }
}