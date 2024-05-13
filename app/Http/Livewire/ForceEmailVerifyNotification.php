<?php

namespace App\Http\Livewire;

use App\Events\UserConfirmedEmailEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ForceEmailVerifyNotification extends Component
{
    protected $listeners = ['sendNewEmailVerificationNotification', 'newEmailToShouldBeConfirmed'];

    public $error = null;

    public $resentToken = false;

    public $code;

    public $key;

    public $email;

    public $email_for_resent;

    public $confirmed = false;

    public $user;

    protected $rules = [
        'code' => 'required|string|min:6|max:16',
        'email' => 'required|email',
    ];

    public function mount()
    {
        if(session()->has('email-to-confirm')){

            $this->email = session('email-to-confirm');
        }
    }

    public function newEmailToShouldBeConfirmed($email)
    {
        $this->email = $email;
    }

    public function render()
    {
        return view('livewire.force-email-verify-notification');
    }


    public function sendNewForceEmailVerificationNotification($request)
    {
        $this->user = $request;

        $this->mount();
    }

    public function verify()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        $auth = auth()->user();

        if($user){

            if(!$user->hasVerifiedEmail()){

                $this->user = $user;

                if($user->token == $this->code){

                    $this->user->markEmailAsVerified();

                    $this->confirmed = true;

                    UserConfirmedEmailEvent::dispatch($auth, $this->user);
                }
                else{

                    $this->addError('code', "La clé ne correspond pas!");

                    $this->addError('email', "La clé ne correspond pas!");
                }
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'ADRESSE EMAIL DEJA CONFIRMEE', 'message' => "Cette adresse mail est déjà confirmé!", 'type' => 'info']);

                $this->addError('email', "Cette adresse mail est déjà confirmée");
            }
        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'ADRESSE MAIL INCONNUE', 'message' => "Cette adresse mail est inconnue!", 'type' => 'error']);

            $this->addError('email', "Cette adresse mail est inconnue");
        }
    }

    public function forceLogin()
    {
        Auth::login($this->user);
    }

    public function resentVerificationEmailToken()
    {
        $this->validate(['email_for_resent' => 'required|email']);

        $data = User::withTrashed('deleted_at')->where('email', $this->email_for_resent)->first();

        if($data){

            if(!$data->hasVerifiedEmail()){

                if(!$data->deleted_at){

                    $data->resendEmailVerificationNotification();

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "CODE RENVOYE", 'type' => 'success', 'message' => "Votre requête a été avec succès!"]);
                }
                else{

                    $this->addError('email_for_resent', "Adresse mail bloquée");

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "ADRESSE BLOQUEE", 'type' => 'error', 'message' => "Cette adresse mail a été bloqué. Veuillez contacter un administrateur"]);
                }
            }
            else{

                $this->addError('email_for_resent', "Adresse mail non compatible");

                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Vous ne pouvez pas effectuer cette action avec cette adresse mail"]);
            }
        }
        else{

            $this->addError('email_for_resent', "Adresse mail introuvable");

            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Vous ne pouvez pas effectuer cette action, l'adresse mail est introuvable ou incorrecte..."]);
        }
    }

    public function prepareResentVerificationEmailToken()
    {
        $this->resentToken = true;
    }
}
