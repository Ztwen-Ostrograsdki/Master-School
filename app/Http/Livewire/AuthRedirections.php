<?php

namespace App\Http\Livewire;

use App\Events\NewUserConnectedEvent;
use App\Events\NewUserCreatedEvent;
use App\Events\NewUserRegistredEvent;
use App\Events\UserConnectedEvent;
use App\Events\UserJoiningChannelEvent;
use App\Events\UsersOnlineEvent;
use App\Helpers\AdminTraits\AdminTrait;
use App\Helpers\Redirectors\RedirectorsDriver;
use App\Models\LockedUsersRequest;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\PasswordChecked;
use App\Rules\StrongPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class AuthRedirections extends Component
{
    public $unlock_token_expires = false;

    public $showPassword = false;

    public $showNewPassword = false;

    public $email_auth;

    public $counter = 0;

    public $email_for_reset;

    public $password_auth;

    public $email;

    public $unlock_token;

    public $pseudo;

    public $password;

    public $new_password;

    public $password_confirmation;

    public $new_password_confirmation;

    public $target;

    public $unverifiedUser = false;

    public $user;

    public $userNoConfirm = false;

    public $blockedUser = false;

    public $reset_password_final_step = false;

    protected $rules = [
        'pseudo' => 'required|string|between:2,255',
        'email' => 'required|email',
        'email_auth' => 'required|email',
        'password' => 'required|string|min:4',
        'unlock_token' => 'required|string|min:4',
        'password_confirmation' => 'required|string|min:4',
        'new_password' => 'required|string|min:4',
        'new_password_confirmation' => 'required|string|min:5',

    ];



    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function toogleShowPassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function mount()
    {
        $target = Route::currentRouteName();

        if($target == 'login' || $target == 'connexion'){

            $this->target = 'login';
        }
        elseif($target == 'registration'){

            $this->target = 'registration';
        }
        elseif($target == 'password-forgot'){

            $this->target = 'reset_password';
        }
    }

    public function render()
    {
        return view('livewire.auth-redirections');
    }


    public function updatedEmailAuth($email)
    {
        $this->resetErrorBag();

        $this->reset('userNoConfirm', 'showPassword', 'showNewPassword', 'unverifiedUser', 'userNoConfirm', 'blockedUser', 'reset_password_final_step');
    }

    public function login()
    {

        $this->reset('userNoConfirm');

        $this->validateOnly('email_auth');

        $this->validate([
            'email_auth' => 'required|email',
            'password_auth' => 'required|string|min:4'
        ]);

        $credentials = ['email' => $this->email_auth, 'password' => $this->password_auth];

        $u = User::where('email', $this->email_auth)->first();

        if($u && !$u->hasVerifiedEmail()){

            $this->user = $u;

            $this->email = $u->email;

            $this->emit('newEmailToShouldBeConfirmed', $this->email);

            session()->put('email-to-confirm', $this->email);

            $this->addError('email_auth', "Ce compte n'a pas été confirmé!");

            $this->userNoConfirm = true;
        }
        elseif($u && ($u->blocked || $u->locked)){

            $this->user = $u;

            $this->email_auth = $u->email;

            $this->addError('email_auth', "Ce compte a été bloqué temporairement!");

            $this->blockedUser = true;
        }
        else{
            if(Auth::attempt($credentials)){

                $this->user = User::find(auth()->user()->id);

                // UserJoiningChannelEvent::dispatch($this->user);

                UserConnectedEvent::dispatch($this->user);

                /**
                 * Generate (if user is an admin) admin session key to access to admin composantes: ONLY ADMIN 
                 */
                if($this->user->isAdmin()){

                    $this->user->__generateAdminKey();
                }

                $this->dispatchBrowserEvent('Toast', ['title' => 'Connexion réussie!!!', 'message' => "Vous serez redirigé dans quelques secondes!", 'type' => 'success']);
                
                // $event = new NewUserConnectedEvent($this->user);
                // broadcast($event);

                RedirectorsDriver::userRedirectorDriver($this->user);
            }
            else{

                $this->addError('email_auth', "Vos renseignements ne sont pas correctes!");

                $this->addError('password_auth', "Vos renseignements ne sont pas correctes!");
            }
        }

       
    }


    public function register()
    {
        $this->auth = Auth::user();

        if($this->auth){

            $this->password = '00000';

            $this->password_confirmation = '00000';
        }

        $v = $this->validate([
            'pseudo' => 'required|string|unique:users|between:5, 50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:4',
            'password_confirmation' => 'required|string|min:4'
        ]);

        if ($v) {

            $v = $this->validate(['password' => new StrongPassword(false, false, false, 4)]);

            if($v){

                $this->user = User::create([
                    'pseudo' => $this->pseudo,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'token' => Str::random(6),
                    'role_id' => Role::first()->id,
                    'email_verified_token' => Hash::make(Str::random(16)),
                ]);

                if($this->user){

                    if($this->user->id == 1){

                        $this->user->markEmailAsVerified();
                    }
                    else{

                        $masterAdmin = User::find(1);

                        if($masterAdmin){

                            $masterAdmin->__followThisUser($this->user->id, true);
                        }
                    }
                    if(!$this->auth && $this->user->id == 1){

                        $this->dispatchBrowserEvent('RegistredSelf');

                        Auth::login($this->user);
                    }
                    else{

                        $this->resetErrorBag();

                        $this->dispatchBrowserEvent('hide-form');

                        // $this->user->sendEmailVerificationNotification();

                        session()->put('user_email_to_verify', $this->user->id);

                        return redirect()->route('email-verification-notify', ['id' => $this->user->id]);
                    }
                    $this->resetErrorBag();
                    
                    $this->dispatchBrowserEvent('RegistredNewUser', ['username' => $this->name]);

                    if($this->user->id == 1){

                        return redirect(RouteServiceProvider::ADMIN);
                    }
                    else{
                        return redirect()->back();
                    }


                }
                else{

                    $this->dispatchBrowserEvent('FireAlertDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure est survenue lors de la creation de votre compte, veuillez réessayer",  'type' => 'error']);

                }
            }
        }

    }

    public function sendCode()
    {
        $this->validate(['email_for_reset' => 'required|email']);

        $user = User::where('email', $this->email_for_reset)->first();

        if($user){

            $this->user = $user;

            if($user->hasVerifiedEmail()){

                $user->forceFill([
                    'reset_password_token' => Str::random(6),
                ])->save();

                $this->user->sendEmailForForgotPasswordNotification();

                return redirect($user->__urlForPasswordReset());
            }
            else{

                $this->dispatchBrowserEvent('FireAlertDoNotClose', ['type' => 'warning', 'message' => "Cette adresse n'a pas encore été confirmé",  'title' => 'Compte non activé']);

                $this->emit('newEmailToShouldBeConfirmed', $this->email_for_reset);

                session()->put('email-to-confirm', $this->email_for_reset);

                $this->addError('email_for_reset', "Ce compte n'a pas été confirmé!");

                $this->userNoConfirm = true;
            }
        }
        else{

            $this->addError('email_for_reset', "L'adresse mail est introuvable");

            $this->dispatchBrowserEvent('FireAlertDoNotClose', ['type' => 'error', 'message' => "L'adresse mail renseillée est introuvable",  'title' => 'Erreur']);
        }
    }


    public function forcedEmailVerification()
    {
        return redirect($this->user->__urlForEmailConfirmation(true));
    }

    public function refreshData()
    {
        $this->counter = $this->counter++;
    }





    public function sendLockedRequest()
    {
        $u = $this->user;

        $email = $this->email_auth;

        if($u && $email){

            $old = $u->lockedRequests;

            if(!$old){

                $request = LockedUsersRequest::create(['user_id' => $u->id, "message" => "Demande de déblocage du compte $email"]);

                if($request){

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'message' => "Votre demande a été soumis avec succès!",  'title' => 'DEMANDE ENVOYEE']);
                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Votre demande n'a pas pu être soumise!",  'title' => 'Erreur']);
                }

            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'info', 'message' => "Vous avez déjà envoyé une demande et elle est en cours de traitement!",  'title' => 'REQUETE DEJA SOUMISE']);

            }

        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Veuillez renseigner des données valides!",  'title' => 'Erreur']);
        }

    }



    public function validateToken()
    {
        $this->validate(['unlock_token' => 'required|string|min:4']);

        $this->validate(['unlock_token' => new PasswordChecked($this->user->unlock_token)]);

        if($this->keyIsExpires()){

            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'title' => 'Authentification échouée', 'message' => "Cette clé a déjà expiré. Veuillez renseigner la nouvelle clé."]);

            $this->addError('unlock_token', "Cette clé n'est plus valable. Taper la nouvelle clé!");

            $this->unlock_token_expires = true;
        }
        else{

            $r = $this->user->__unlockOrLockThisUser();

            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'title' => "VERIFICATION REUSSIE", "message" => "Vous pouvez à présent vous connecter à votre compte et accéder à vos données!"]);

            $this->reset('unlock_token_expires', 'blockedUser', 'unlock_token');
        }
        
    }


    public function updatedUnlockToken($token)
    {
        $this->resetErrorBag('unlock_token');
    }


    public function keyIsExpires()
    {
        $now = Carbon::now();

        $e = $this->user->updated_at;

        $times = $now->diffInMinutes($e);

        if($times > 15){

            return true;
        }
        return false;
    }

    public function regenerateAndSendUnlockTokenToUser()
    {
        $user = $this->user;

        if($user){

            $request = $user->__generateUnlockedToken();

            if($request){

                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'message' => "Clé générée avec succès!",  'title' => 'CLE ENVOYEE']);
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "La clé n'a pu être générée!",  'title' => 'Erreur']);
            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Veuillez renseigner des données valides!",  'title' => 'Erreur']);
        }

        $this->refreshData();

    }


    public function retryLogin()
    {
        $this->resetErrorBag();

        $this->reset('userNoConfirm', 'blockedUser', 'unverifiedUser', 'unlock_token_expires');
    }



}
