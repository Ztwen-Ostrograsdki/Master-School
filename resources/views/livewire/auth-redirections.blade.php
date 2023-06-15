<div>
@if($target == 'login')
<div class="zw-90 row mx-auto" style="position: relative; top:200px;">
    <div class="col-12 col-lg-6 col-xl-6 col-md-6 mx-auto z-bg-secondary-light-opac border rounded z-border-orange" style="opacity: 0.8;">
        <div class="w-100 mx-auto p-3">
            <div class="w-100 z-color-orange">
                <h5 class="text-center w-100">
                    <span class="fa fa-user-secret fa-3x "></span>
                    <h5 class="w-100 text-uppercase text-center">Authentification - Connexion</h5>
                </h5>
                <hr class="w-100 z-border-orange mx-auto my-2">
            </div>
            <div class="w-100">
                <form autocomplete="false" method="post" class="mt-3 mx-auto authentication-form" wire:submit.prevent="login" >
                    @csrf
                    <div class="w-100">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-person zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            <input name="email_auth" wire:model="email_auth"  type="email" class="form-control  @error('email_auth') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre adresse mail...">
                        </div>
                        @error('email_auth')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>  
                    @if((!$userNoConfirm && !$blockedUser) || ($user && !$blockedUser && !$user->unlock_token))
                    <div class="w-100 mt-2">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-unlock zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            <input name="password_auth" wire:model.defer="password_auth"  type="password" class="form-control  @error('password_auth') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre mot de passe...">
                        </div>
                        @error('password_auth')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>
                    @endif

                    @if(!$userNoConfirm && !$blockedUser)
                    <div class="w-100 mt-3 d-flex justify-center">
                        <button type="submit" class="z-bg-orange border rounded px-3 py-2 w-75">Se connecter</button>
                    </div>
                    <div class="w-100 mt-3 d-flex justify-center">
                        <a class="text-warning text-center px-3 py-2 w-75" href="{{route('password-forgot')}}">
                            <strong class="">Mot de passe oublié ?</strong>
                        </a>
                    </div>
                    @elseif($blockedUser)
                    <div class="my-2 p-2 text-center border rounded text-white-50">
                        <h6 class="mx-auto p-3 text-white-50">
                            <span class="bi-exclamation-triangle h2 text-warning text-center p-0"> </span>
                            <h6 class="m-0 p-0 my-2">
                                Le compte  <span class="text-warning"> {{ $email }} </span> a été bloqué ou verrouillé <span class="fa bi-lock text-warning mx-2"></span> !
                            </h6>

                            @if($user && $user->lockedRequests)
                                <blockquote class="text-info text-left">
                                    Mr/Mme <b class="text-orange"> {{$user->pseudo}} </b>, vous avez déjà envoyé une demande de déblocage de votre compte  <span class="text-warning"> {{ $email }} </span> <br>
                                    @if($user->unlock_token)
                                        <span class="text-center text-warning">Veuillez renseigner la clé qui vous a été envoyez par mail!</span>
                                    @else
                                        <span class="text-center text-warning">Les administrateurs sont à pied d'oeuvre pour régler le problème!</span>
                                    @endif
                                </blockquote>
                                @if($user->unlock_token)
                                    <div class="col-10 mx-auto my-2">
                                        <div class="w-100 d-flex justify-content-between border rounded">
                                            <strong class="bi-unlock zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                                            <input name="unlock_token" wire:model="unlock_token"  type="password" class="form-control  @error('unlock_token') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner la clé...">
                                        </div>
                                        @error('unlock_token')
                                            <span class="py-1 mb-2 z-color-orange text-left float-left">{{$message}}</span>
                                        @enderror
                                        <div class="d-flex justify-between my-0 py-0 mx-auto row col-11">
                                            <span wire:click="validateToken" class="bg-primary border text-white btn rounded px-3 py-2 my-2 col-6"> <span class="fa bi-unlock mr-1"></span>Débloquer maintenant!</span>
                                            <span wire:click="regenerateAndSendUnlockTokenToUser" class="bg-secondary border text-white btn rounded px-3 py-2 my-2 col-5"> <span class="fa bi-recycle mr-1"></span>Renvoyer la clé!</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif($user && !$user->lockedRequests)
                                <blockquote class="text-info text-left">
                                    Mr/Mme <b class="text-orange"> {{$user->pseudo}} </b>, il se pourait que votre compte <span class="text-warning"> {{ $email }} </span> ait été temporairement bloqué ou verrouillé, veuillez cliquer sur le bouton <span class="text-warning">signaler</span> ci-dessous afin que les administrateurs puissent régler le problème!
                                </blockquote>
                                <span wire:click="sendLockedRequest" class="bg-primary border text-white btn rounded px-3 py-2 w-75">Signaler pour récupérer mon compte!</span>
                            @endif
                        </h6>
                    </div>
                    @else
                    <div class="w-100 mt-3 d-flex justify-center">
                        <span wire:click="forcedEmailVerification" class="text-white cursor-pointer text-center bg-success border rounded px-3 py-2 w-75" >
                            <span class="bi-key mx-2"></span>
                            <span>Confirmer mon compte</span>
                        </span>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@elseif($target == 'registration')

<div class="zw-90 row mx-auto" style="position: relative; top:150px;">
    <div class="col-12 col-lg-6 col-xl-6 col-md-6 mx-auto z-bg-secondary-light-opac border rounded z-border-orange" style="opacity: 0.8;">
        <div class="w-100 mx-auto p-3">
            <div class="w-100 z-color-orange">
                <h5 class="text-center w-100">
                    <span class="fa fa-user-plus fa-3x "></span>
                    <h5 class="w-100 text-uppercase text-center">Inscription</h5>
                </h5>
                <hr class="w-100 z-border-orange mx-auto my-2">
            </div>
            <div class="w-100">
                <form autocomplete="false" class="mt-3 mx-auto authentication-form" wire:submit.prevent="register" >
                    @csrf
                    <div class="w-100">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-person zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            <input name="pseudo" wire:model.defer="pseudo"  type="text" class="form-control  @error('pseudo') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre pseudo...">
                        </div>
                        @error('pseudo')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>  
                    <div class="w-100 mt-2">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-envelope-check zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            <input name="email" wire:model.defer="email"  type="email" class="form-control  @error('email') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre adresse mail...">
                        </div>
                        @error('email')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>  

                    <div class="w-100 mt-2">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-unlock zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            @if ($showPassword)
                            <input name="password" wire:model.defer="password"  type="text" class="form-control  @error('password') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre mot de passe...">
                            @else
                            <input name="password" wire:model.defer="password"  type="password" class="form-control  @error('password') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre mot de passe...">
                            @endif
                        </div>
                        @error('password')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="w-100 mt-2">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-unlock-fill zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            @if ($showPassword)
                            <input name="password_confirmation" wire:model.defer="password_confirmation"  type="text" class="form-control  @error('password_confirmation') text-danger border border-danger @enderror text-white zw-80 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez confirmer votre mot de passe...">
                            @else
                            <input name="password_confirmation" wire:model.defer="password_confirmation"  type="password" class="form-control  @error('password_confirmation') text-danger border border-danger @enderror text-white zw-80 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez confirmer votre mot de passe...">
                            @endif
                            @if ($showPassword)
                                <span title="Masquer le mot de passe" wire:click="toogleShowPassword" class="bi-eye-slash z-bg-secondary-dark text-white p-2 cursor-pointer"></span>
                            @else
                                <span title="Afficher le mot de passe" wire:click="toogleShowPassword" class="bi-eye z-bg-secondary-dark text-white p-2 cursor-pointer"></span>
                            @endif
                        </div>
                        @error('password_confirmation')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="w-100 mt-3 d-flex justify-center">
                        <button type="submit" class="z-bg-orange border rounded px-3 py-2 w-75">S'inscrire</button>
                    </div>
                    <div class="w-100 mt-3 d-flex justify-center">
                        <a class="text-white text-center px-3 py-2 w-75" href="{{route('connexion')}}">
                            <span class="bi-user mx-2"></span>
                            <strong class="text-warning text-center w-100">J'ai déjà un compte</strong>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- PASSWORD FORGOT --}}

@elseif($target == 'reset_password')
<div class="zw-90 row mx-auto" style="position: relative; top:200px;">
    <div class="col-12 col-lg-6 col-xl-6 col-md-6 mx-auto z-bg-secondary-light-opac border rounded z-border-orange" style="opacity: 0.8;">
        <div class="w-100 mx-auto p-3">
            <div class="w-100 z-color-orange">
                <h5 class="text-center w-100">
                    <span class="fa fa-user-secret fa-3x "></span>
                    <h5 class="w-100 text-uppercase text-center">Reccupération de compte</h5>
                </h5>
                <hr class="w-100 z-border-orange mx-auto my-2">
            </div>
            <div class="w-100">
                <form autocomplete="false" method="post" class="mt-3 mx-auto authentication-form" wire:submit.prevent="sendCode" >
                    @csrf
                    <div class="w-100">
                        <div class="w-100 d-flex justify-content-between border rounded">
                            <strong class="bi-person zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                            <input name="email_for_reset" wire:model.defer="email_for_reset"  type="email" class="form-control  @error('email_for_reset') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre adresse mail...">
                        </div>
                        @error('email_for_reset')
                            <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                        @enderror
                    </div> 
                    @if(!$userNoConfirm)
                    <div class="w-100 mt-3 d-flex justify-center">
                        <button type="submit" class="z-bg-orange border rounded px-3 py-2 w-75">Lancer</button>
                    </div> 
                    <div class="w-100 mt-3 d-flex justify-center">
                        <a href="{{route('login')}}" class="text-warning text-center px-3 py-2 w-75">
                            <strong class="">Annuler le processus ?</strong>
                        </a>
                    </div>
                    @else
                    <div class="w-100 mt-3 d-flex justify-center">
                        <span wire:click="forcedEmailVerification" class="text-white text-center bg-success border rounded px-3 py-2 w-75" >
                            <span class="bi-key mx-2"></span>
                            <span>Confirmer mon compte</span>
                        </span>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endif

</div>