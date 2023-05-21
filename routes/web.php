<?php

use App\Http\Controllers\BlockTemporaryMyAccount;
use App\Http\Controllers\ClasseListDownload;
use App\Http\Livewire\Admin;
use App\Http\Livewire\AdminAuthorization;
use App\Http\Livewire\AdminTeacherSecurityActions;
use App\Http\Livewire\AuthRedirections;
use App\Http\Livewire\ClasseGroupProfil;
use App\Http\Livewire\ClasseProfil;
use App\Http\Livewire\Home;
use App\Http\Livewire\MultiplePupilInsertion;
use App\Http\Livewire\PupilProfil;
use App\Http\Livewire\RegisteringNewUser;
use App\Http\Livewire\ResetPassword;
use App\Http\Livewire\SchoolCalendar;
use App\Http\Livewire\TeacherListing;
use App\Http\Livewire\TeacherProfilAsUser;
use App\Http\Livewire\UserListing;
use App\Http\Livewire\UserProfil;
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


// Route::any('test', 'TestController@test')->middleware('caffeinated');

// Route::group(['middleware' => ['caffeinated']], function () {
//     Route::any('test', 'TestController@test');
// });

Route::get('/', Home::class)->name('home');

Route::group(['prefix' => '/administration', 'middleware' => ['auth']], function(){
    Route::get('/', Admin::class)->middleware('auth')->name('admin');
    Route::get('/utilisateurs', UserListing::class)->name('user_listing');
    Route::get('/enseignants', TeacherListing::class)->name('teacher_listing');
    Route::get('/securisation/enseignants', AdminTeacherSecurityActions::class)->name('admin_teacher_security_actions');
    Route::get('/calendrier-scolaire/{school_year}', SchoolCalendar::class)->name('school_calendar');
    Route::get('/classe/{slug}', ClasseProfil::class)->name('classe_profil');
    Route::get('/promotion/{slug}', ClasseGroupProfil::class)->name('classe_group_profil');
    Route::get('/élève/{id}', PupilProfil::class)->name('pupil_profil');
    Route::get('/inscription-élèves/inscription-multiple', MultiplePupilInsertion::class)->name('multiple_pupil_insertion');
});

Route::get('/compte/mon-compte/{id}', UserProfil::class)->name('user_profil')->middleware('user.self');
Route::get('/mon-compte/enseignant/{id}/{slug}', TeacherProfilAsUser::class)->name('teacher_profil_as_user')->middleware(['user.self', 'user.teacher']);


Route::post('/inscription', RegisteringNewUser::class)->middleware('guest')->name('inscription');

Route::get('/classe/{classe_id}', [ClasseListDownload::class, 'index'])->name('classe_pdf');
Route::get('/classe/{classe_id}/pdf', [ClasseListDownload::class, 'createPDF'])->name('classe_pdf_print');

Route::get('/connexion', AuthRedirections::class)->name('connexion')->middleware('guest');
Route::get('/inscription', AuthRedirections::class)->name('registration')->middleware('guest');
Route::get('/authentification', AdminAuthorization::class)->name('get-admin-authorization')->middleware(['auth', 'admin', 'verifiedUser']);
Route::get('/mot-de-passe-oublie', AuthRedirections::class)->name('password-forgot')->middleware('guest');
Route::get('/changer-mot-de-passe/get-protection/id={id}/token={token}/key={key}/hash={hash}/s={s}/from-email={email}/reset-password=1/password=new', ResetPassword::class)->name('reset.password')->middleware(['guest', 'signed']);
Route::get('/verrouillage-de-mon-compte/protection=1/id={id}/token={token}/hash={hash}/security=1/blocked=true', [BlockTemporaryMyAccount::class, '__locked'])->name('block-temporary-account')->middleware(['signed']);


Route::get('/deconnection', function () {
    Auth::guard('web')->logout();
    session()->flush();
    return redirect()->route('connexion');
})->name('deconexion');

Route::get('/about', function () {
    return view('layouts/app');
   
})->name('about');
