<?php 

namespace App\Helpers\Redirectors;

use App\Models\User;

/**
 * Trait to manage the redirections after currently login
 */
trait RedirectorsDriver{


	/**
	 * To the specific url
	 */
	public static function toUrl($url)
	{


	}


	/**
	 * To the registred url <into session> url after connexion
	 */
	public static function toUrlAfterRedirection()
	{
		if(session()->has('afterRedirectionUlr') && session('afterRedirectionUlr') && session('afterRedirectionUlr') !== '' && session('afterRedirectionUlr') !== null){

            $toTheAfterRedirectionUrl = session('afterRedirectionUlr');

            redirect()->to($toTheAfterRedirectionUrl);

        }
	}

	/**
	 * To the normal url after user logged
	 */
	public static function userRedirectorDriver(User $user)
	{
		if(session()->has('afterRedirectionUlr') && session('afterRedirectionUlr') && session('afterRedirectionUlr') !== '' && session('afterRedirectionUlr') !== null){

            $toTheAfterRedirectionUrl = session('afterRedirectionUlr');

            redirect()->to($toTheAfterRedirectionUrl);

        }
        elseif($user->isAdminAs('master')){

            $user->___backToAdminRoute();
        }
        else{
            $user->__backToUserProfilRoute();
        }

	}




}