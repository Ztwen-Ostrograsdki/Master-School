<?php

use App\Http\Controllers\BlockTemporaryMyAccount;
use App\Http\Controllers\ClasseListDownload;
use App\Http\Livewire\Admin;
use App\Http\Livewire\AdminAuthorization;
use App\Http\Livewire\AdminTeacherSecurityActions;
use App\Http\Livewire\AuthRedirections;
use App\Http\Livewire\ClasseGroupProfil;
use App\Http\Livewire\ClasseProfil;
use App\Http\Livewire\ClasseTableList;
use App\Http\Livewire\EpreuvesDeCompositionEnvoyees;
use App\Http\Livewire\ForceEmailVerifyNotification;
use App\Http\Livewire\Home;
use App\Http\Livewire\ManageEpreuvesTransfers;
use App\Http\Livewire\MultiplePupilInsertion;
use App\Http\Livewire\PDFPrinter;
use App\Http\Livewire\ParentProfil;
use App\Http\Livewire\ParentsListerComponent;
use App\Http\Livewire\PolyvalenteClasseManager;
use App\Http\Livewire\PupilProfil;
use App\Http\Livewire\PupilsListerComponent;
use App\Http\Livewire\PupilsListingByClasse;
use App\Http\Livewire\RegisteringNewUser;
use App\Http\Livewire\ResetPassword;
use App\Http\Livewire\SchoolCalendar;
use App\Http\Livewire\SchoolYearableComponent;
use App\Http\Livewire\Statistics;
use App\Http\Livewire\SubjectTableList;
use App\Http\Livewire\TeacherListing;
use App\Http\Livewire\TeacherListingByClasse;
use App\Http\Livewire\TeacherProfilAsUser;
use App\Http\Livewire\TimePlansComponent;
use App\Http\Livewire\UserListing;
use App\Http\Livewire\UserProfil;
use App\Http\Livewire\UsersListingByTarget;
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

Route::group(['prefix' => '/administration', 'middleware' => ['auth', 'admin', 'admin.authenticate', 'user.authorized2route']], function(){
    Route::get('/', Admin::class)->middleware('auth')->name('admin');
    Route::get('/utilisateurs', UserListing::class)->name('user_listing');
    Route::get('/utilisateurs/{target}', UsersListingByTarget::class)->name('user_listing_by_target');
    Route::get('/tous-les-enseignants', TeacherListing::class)->name('teacher_listing');
    Route::get('/tous-les-enseignants-de-la/{slug}', TeacherListingByClasse::class)->name('classe_teachers');
    Route::get('/securisation/enseignants', AdminTeacherSecurityActions::class)->name('admin_teacher_security_actions');
    Route::get('/gestionnaire/liste/epreuves-de-composition', EpreuvesDeCompositionEnvoyees::class)->name('epreuves_de_composition_envoyes');
    Route::get('/gestionnaire/base-de-données/Secondaire', SchoolYearableComponent::class)->name('data_manager_secondary');
    Route::get('/gestionnaire/base-de-données/Primaire', SchoolYearableComponent::class)->name('data_manager_primary');
    Route::get('/calendrier-scolaire/{school_year}', SchoolCalendar::class)->name('school_calendar');
    Route::get('/emploi-du-temps/{school_year}', TimePlansComponent::class)->name('time_plans');
    
    Route::get('/tous-les-apprenant/cycle/{slug}', PupilsListerComponent::class)->name('pupil_listing');
    Route::get('/tous-les-parents/', ParentsListerComponent::class)->name('parents_listing');
    Route::get('/tous-les-apprenant-de-la/{slug}', PupilsListingByClasse::class)->name('classe_pupils');
    Route::get('/toutes-les-classes', ClasseTableList::class)->name('classe_listing');
    Route::get('/classe-polyvalente/cycle/{slug}', PolyvalenteClasseManager::class)->name('polyvalente_classe');
    Route::get('/toutes-les-specialites', SubjectTableList::class)->name('subject_listing');
    Route::get('/classe/{slug}', ClasseProfil::class)->name('classe_profil');
    Route::get('/promotion/{slug}', ClasseGroupProfil::class)->name('classe_group_profil');
    Route::get('/élève/{id}', PupilProfil::class)->name('pupil_profil');
    Route::get('/inscription-élèves/inscription-multiple', MultiplePupilInsertion::class)->name('multiple_pupil_insertion');
    Route::get('/statistiques', Statistics::class)->name('statistics');

});

Route::get('/Enseignant/envoi-des-epreuves-de-composition', ManageEpreuvesTransfers::class)->name('upload_epreuves', 'user.authorized2route')->middleware(['auth']);
Route::get('/compte/mon-compte/{id}', UserProfil::class)->name('user_profil')->middleware(['user.self', 'notBlockedUser']);
Route::get('/espace-parent/mon-compte-parent/{id}', ParentProfil::class)->name('parent_profil')->middleware(['auth', 'user.authorized2route']);
Route::get('/mon-compte/enseignant/{id}/{classe_id}/{slug}', TeacherProfilAsUser::class)->name('teacher_profil_as_user')->middleware(['user.teacher', 'classeNotClosedForTeacher', 'user.authorized2route']);


Route::get('/email-verification-notify', ForceEmailVerifyNotification::class)->name('email-verification-notify');
Route::post('/inscription', RegisteringNewUser::class)->middleware('guest')->name('inscription');

Route::get('/classe/{classe_id}', [ClasseListDownload::class, 'index'])->name('classe_pdf');
Route::get('/classe/{classe_id}/pdf', [ClasseListDownload::class, 'createPDF'])->name('classe_pdf_print');

Route::get('/pdf', [PDFPrinter::class, 'render'])->name('pdf_print');

Route::get('/connexion', AuthRedirections::class)->name('connexion')->middleware('guest');
Route::get('/inscription', AuthRedirections::class)->name('registration')->middleware('guest');
Route::get('/authentification', AdminAuthorization::class)->name('get-admin-authorization')->middleware(['auth', 'admin', 'verifiedUser', 'admin.not.authenticate', 'user.authorized2route']);
Route::get('/mot-de-passe-oublie', AuthRedirections::class)->name('password-forgot')->middleware('guest');
Route::get('/changer-mot-de-passe/get-protection/id={id}/token={token}/key={key}/hash={hash}/s={s}/from-email={email}/reset-password=1/password=new', ResetPassword::class)->name('reset.password')->middleware(['guest', 'signed']);
Route::get('/verrouillage-de-mon-compte/protection=1/id={id}/token={token}/hash={hash}/security=1/blocked=true', [BlockTemporaryMyAccount::class, '__locked'])->name('block-temporary-account')->middleware(['signed']);


Route::get('/deconnection', function () {
    Auth::guard('web')->logout();
    session()->flush();
    return redirect()->route('connexion');
})->name('deconexion');

Route::get('/login', function () {
    return redirect()->route('connexion');
});

Route::get('/about', function () {
    return view('layouts/app');
   
})->name('about');
