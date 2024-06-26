<?php

namespace App\Helpers\UserTraits;

use App\Events\ForcingUserDisconnectionEvent;
use App\Events\UserConfirmedEmailEvent;
use App\Models\Parentable;
use App\Notifications\ResetEmail;
use App\Notifications\SentEmailVerificationToUser;
use App\Notifications\SentParentKeyToUserNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;

trait MustVerifyEmailTrait
{
    /**
     * Determine if the user has verified their email address.
     * @return bool
     */
    public function hasNewEmailToVerified()
    {
        return (! is_null($this->new_email_verified_token)) && (! is_null($this->new_email));
    }
    /**
     * Determine if the user has verified their email address.
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        $auth = auth()->user();

        if($this->id == 1){
            $make = $this->forceFill([
                'email_verified_at' => $this->freshTimestamp(),
                'email_verified_token' => null,
                'role' => 'master',
                'token' => null,
            ])->save();
        }

        $make = $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_verified_token' => null,
            'token' => null,
        ])->save();

        if($make){

            $this->deleteLockedRequest();

            UserConfirmedEmailEvent::dispatch($auth, $this);

            return $make;

        }
       
    }
    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsUnVerified()
    {
        $this->forceFill([
            'token' => Str::random(6),
            'email_verified_at' => null,
            'email_verified_token' => Hash::make(Str::random(16)),
        ])->save();

        $this->notify(new SentEmailVerificationToUser);

        session()->put('user_email_to_verify', $this->id);

        return redirect()->route('email-verification-notify', ['id' => $this->id]);
    } 

       /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsOnlyUnverified()
    {
        DB::transaction(function($e){

            $make = $this->forceFill([
                'token' => Str::random(6),
                'email_verified_at' => null,
                'email_verified_token' => Hash::make(Str::random(16)),
            ])->save();

            if($make){

                ForcingUserDisconnectionEvent::dispatch($this);

            }

        });
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new SentEmailVerificationToUser);
    }


    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendParentableKeyNotification($key)
    {
        $this->notify(new SentParentKeyToUserNotification($key));
    }


    public function __initialisedResetUserEmail($newEmail)
    {
        $token = Str::random(6);

        $make = $this->update([
            'new_email' => $newEmail,
            'new_email_verified_token' => $token,
        ]);

        if($make){

            return $this->__resetEmail();
        }
        else{
            return false;
        }
    }

    public function __finishedResetUserEmail($newEmail)
    {
        return $this->update([
            'email' => $newEmail,
            'new_email' => null,
            'new_email_verified_token' => null,
        ]);
    }

    
    public function __abortResetUserEmail()
    {
        return $this->update([
            'new_email' => null,
            'new_email_verified_token' => null,
        ]);
    }

   
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function __resetEmail()
    {
        if($this->hasNewEmailToVerified()){

            return $this->notify(new ResetEmail());
        }
        return false;
    }

    /**
     * Resend the email verification notification.
     *
     * @return void
     */
    public function resendEmailVerificationNotification()
    {
        $this->forceFill([
            'token' => Str::random(6),
            'email_verified_token' => Hash::make(Str::random(16)),
        ])->save();

        $this->notify(new SentEmailVerificationToUser);

        session()->put('user_email_to_verify', $this->id);

        return redirect()->route('email-verification-notify', ['id' => $this->id]);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get the token to verify the email registred
     *
     * @return void
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * Get the token to verify the email registred
     *
     * @return void
     */
    public function getEmailResetToken()
    {
        return $this->emailConfirmation->token;
    }

    public function __loginThisUser()
    {
        $credentials = ['email' => $this->email, 'password' => $this->password];
        Auth::attempt($credentials);
    }


    public function __urlForEmailConfirmation(bool $forced = false)
    {
        $this->forceFill([
            'token' => Str::random(6),
            'email_verified_token' => Hash::make(Str::random(16)),
        ])->save();
        // $this->notify(new SentEmailVerificationToUser);

        if($forced){
            return URL::temporarySignedRoute(
                'email-verification-notify',
                Carbon::now()->addMinutes(30),
                [
                    'email' => $this->getEmailForVerification(),
                    'token' => urlencode(sha1($this->email_verified_token)),
                    'key' => $this->token,
                    'hash' => sha1($this->getEmailForVerification()),
                ]
            );
        }
        else{
            return URL::temporarySignedRoute(
                'confirmed-email-verification',
                Carbon::now()->addMinutes(7),
                [
                    'id' => $this->getKey(),
                    'token' => urlencode(sha1($this->email_verified_token)),
                    'key' => $this->token,
                    'hash' => sha1($this->getEmailForVerification()),
                ]
            );
        }
    }




}
