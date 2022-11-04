<?php

use App\Http\Controllers\BlockTemporaryMyAccount;
use App\Http\Livewire\Admin;
use App\Http\Livewire\AdminAuthorization;
use App\Http\Livewire\AuthRedirections;
use App\Http\Livewire\ClasseProfil;
use App\Http\Livewire\Home;
use App\Http\Livewire\MultiplePupilInsertion;
use App\Http\Livewire\PupilProfil;
use App\Http\Livewire\RegisteringNewUser;
use App\Http\Livewire\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', Home::class)->name('home');

Route::group(['prefix' => '/administration'], function(){
    Route::get('/', Admin::class)->name('admin');
    Route::get('/classe/{slug}', ClasseProfil::class)->name('classe_profil');
    Route::get('/élève/{id}', PupilProfil::class)->name('pupil_profil');
    Route::get('/inscription-élèves/inscription-multiple', MultiplePupilInsertion::class)->name('multiple_pupil_insertion');
});
Route::post('/inscription', RegisteringNewUser::class)->middleware('guest')->name('inscription');


Route::get('/connexion', AuthRedirections::class)->name('login')->middleware('guest');
Route::get('/inscription', AuthRedirections::class)->name('registration')->middleware('guest');
Route::get('/authentification', AdminAuthorization::class)->name('get-admin-authorization')->middleware(['auth', 'admin', 'verifiedUser']);
Route::get('/mot-de-passe-oublie', AuthRedirections::class)->name('password-forgot')->middleware('guest');
Route::get('/changer-mot-de-passe/get-protection/id={id}/token={token}/key={key}/hash={hash}/s={s}/from-email={email}/reset-password=1/password=new', ResetPassword::class)->name('reset.password')->middleware(['guest', 'signed']);
Route::get('/verrouillage-de-mon-compte/protection=1/id={id}/token={token}/hash={hash}/security=1/blocked=true', [BlockTemporaryMyAccount::class, '__locked'])->name('block-temporary-account')->middleware(['signed']);


Route::get('/deconnection', function () {
    Auth::guard('web')->logout();
    session()->flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/about', function () {
    return view('layouts/app');
   
})->name('about');
