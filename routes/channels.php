<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('parent.{id}', function ($user, $id) {
    return (int) $user->parentable->id === (int) $id;
});

Broadcast::channel('users', function () {
    return true;
});

Broadcast::channel('master', function ($user) {
    return (int) $user->isAdminAs('master');
});

Broadcast::channel('mark', function ($user) {
    return true;
});

Broadcast::channel('reloader.{id}', function ($auth, $id) {
    if((int) $auth->id === (int) $id || $auth->isAdminAs('master')){

        return ['user' => $auth];

    }
});

Broadcast::channel('reloadMarkChannel.{id}', function ($auth, $id) {
    if((int) $auth->id === (int) $id || $auth->isAdminAs('master')){

        return ['user' => $auth];

    }
});







Broadcast::channel('online', function ($user) {

    if((int) $user->id === (int) auth()->user()->id){

        $roleName = 'Inconnu';

        $role = $user->getRole();

        if($role){

            $roleName = $role->name;

        }
        
        return ['id' => $user->id, 'pseudo' => $user->pseudo, 'email' => $user->email, 'role' => $roleName];
    }
});
