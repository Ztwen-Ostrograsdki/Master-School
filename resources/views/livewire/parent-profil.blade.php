<div class="m-0 p-0 w-100" style="min-height: 700px;">
    @if($parentable && $parentable->authorized)
    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >
       <div class="w-100 border m-0 p-0">
          <div class="m-0 p-0 w-100"> 
             <div class="row w-100 m-0">
                <div class=" col-12 border-left border-white bg-dark pb-3" >
                   <div class="w-100 p-0 m-0 @isMySelf($user) @else d-flex justify-content-between @endisMySelf mt-2 border">
                    <div class="mx-auto d-flex @isMySelf($user) w-100 @else col-8 @endisMySelf justify-between">
                        <div class="mx-auto w-100 m-0 p-0 row">
                            <div class="mx-auto w-100">
                                <h4 class="text-white-50 text-center pt-3 pb-1">
                                    <span class="bi-person-badge mx-2"></span>
                                    Profil
                                </h4>
                                <hr class="m-0 p-0 text-white w-100">
                                <div class="d-flex flex-column w-100">
                                    <h6 class="text-white-50 px-3 pt-2 pb-1">
                                        <span class="bi-pen-fill mx-2"></span>
                                        {{ $user->pseudo }}
                                    </h6>
                                    <h6 class="text-white-50 px-3 pb-1">
                                        <span class="bi-shield-check mx-2"></span>
                                        {{ $user->getRole()->name }}
                                    </h6>
                                    <h6 class="text-white-50 px-3 pb-1">
                                        <span class="bi-people mx-2"></span>
                                        {{ 00 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @auth
                           <div class="m-0" id="OpenEditPhotoProfilModal" title="Doucle cliquer pour changer la photo de profil">
                               <div class="d-flex w-100 justify-content-between cursor-pointer m-0 p-0">
                                @if($user)
                                    <img src="{{$user->__profil('250')}}" alt="mon profil" class="w-100">
                                @endif
                               </div>
                        </div>
                        @endauth
                     </div>
                     </div>
                     <div class="border mt-3 p-3">
                        <div class="mx-auto justify-center d-flex w-100">
                           <h5 class=" text-white w-100">
                               <span class="float-left text-uppercase"> 

                               </span>
                               <span wire:click="followMyPupil" class="float-left text-white z-scale btn bg-primary border-white border" title="Lancer une demande pour suivre l'un de mes apprenants">
                                   <span class="">Identifier un enfant à moi à suivre</span>
                                   <strong class="bi-person-plus text-white "></strong>
                               </span>
                           </h5>
                        </div>
                        <hr class="w-100 bg-white text-white mt-2">
                        <div class="px-2" >
                            @if($user->teacher && $user->teacher->teaching)
                                <div class="row d-flex justify-content-between text-white w-100 ">
                                    <div class="col-lg-3 col-xlg-3 col-md-12 col-12 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-user"></span>
                                            <span>Mes infos personnelles</span>
                                            @if(auth()->user()->isAdminAs('master') || auth()->user->id == $user->id)
                                                <span wire:click="updateTeacherPersoData({{$user->teacher->id}})" class="float-right fa fa-edit text-secondary cursor-pointer fx-20" title="Editer mes infos..."></span>
                                            @endif
                                        </h6>
                                        <hr class="m-0 p-0 bg-white text-white">

                                        <div class="">
                                            <h6 class="">
                                                <span class="text-warning">Nom et Prenoms:</span>
                                                <span>{{ $user->teacher->name . ' ' . $user->teacher->surname }}</span>
                                            </h6>

                                            <h6 class="">
                                                <span class="text-warning">Nationnalité:</span>
                                                <span>{{ $user->teacher->nationality }}</span>
                                            </h6>

                                            <h6 class="">
                                                <span class="text-warning">Contacts:</span>
                                                <span>{{ $user->teacher->contacts }}</span>
                                            </h6>

                                            <h6 class="">
                                                <span class="text-warning">Email:</span>
                                                <span>{{ $user->email }}</span>
                                            </h6>
                                            <h6 class="">
                                                <span class="text-warning">Profession:</span>
                                                <span>{{ 'A renseigner' }}</span>
                                            </h6>

                                            <h6 class="">
                                                <span class="text-warning">Ses apprenants:</span>
                                                <span>12</span>
                                            </h6>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-xlg-4 col-md-12 col-12 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-clock"></span>
                                            <span>Emploi du temps ({{ $user->teacher->speciality()->name }}) </span>
                                        </h6>
                                        <hr class="m-0 p-0 bg-white text-white">
                                        <div class="d-flex justify-content-between flex-column">
                                            @if($user->teacher->hasClasses())
                                                @foreach($user->teacher->getTeachersCurrentClasses() as $cll)
                                                    @php
                                                        $clll = $cll->getNumericName();
                                                        $time_plans = $user->teacher->getCurrentTimePlans($cll->id);
                                                    @endphp
                                                    <div class="col-11 border rounded p-1 m-1">
                                                        <div class="m-0 p-0">
                                                            <h6 class="d-flex justify-content-between">
                                                                <span class="">
                                                                    <span class="text-orange">Pierre Marc</span>
                                                                    <span class="text-white-50 ml-2">{{ $clll['root'] }}<sup>{{ $clll['sup'] }} </sup> {{ $clll['idc'] }}</span>
                                                                </span>
                                                            </h6>
                                                            <hr class="m-0 p-0 bg-secondary text-secondary">
                                                            <div class="m-0 p-0">
                                                                @if(count($time_plans) > 0)
                                                                    @foreach($time_plans as $tm)
                                                                        <h6 class="">
                                                                            <span class="text-warning"> {{$tm->day}} :</span>
                                                                            <span>{{ $tm->start . 'H à ' . $tm->end . 'H' }}</span>
                                                                        </h6>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-white-50 font-italic">Pas encore défini!</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                Aucune classe assignée!
                                            @endif
                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-xlg-4 col-md-12 col-12 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-user"></span>
                                            <span>Mes enfants</span>
                                            @if(auth()->user()->isAdminAs('master') || auth()->user->id == $user->id)
                                                <span wire:click="updateTeacherPersoData({{$user->teacher->id}})" class="float-right fa fa-edit text-secondary cursor-pointer fx-20" title="Editer mes infos..."></span>
                                            @endif
                                        </h6>
                                        <hr class="m-0 p-0 bg-white text-white">

                                        <div class="row d-flex justify-content-center w-95 mx-auto">
                                            
                                            <div class="col-12 shadow border-orange border rounded float-right p-2 m-2">
                                                <h6 class="">
                                                    <span>
                                                        <span class="text-warning">Nom et prénoms:</span>
                                                        <span>Pierre Marc</span>
                                                    </span>
                                                    
                                                </h6>
                                                <h6 class="">
                                                    <span>
                                                        <span class="text-warning">Classe:</span>
                                                        <span>2nde F1</span>
                                                    </span>
                                                </h6>
                                                <h6 class="">
                                                    <span>
                                                        <span class="text-warning">Relation familiale:</span>
                                                        <span>Fils</span>
                                                    </span>
                                                </h6>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            @endif
                        </div>
                     </div>
                </div>
             </div>
          </div>   
       </div>
    </div>
    @elseif(!$parentable)

    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >

        <div class="mt-5">
            <div class="zw-90 mx-auto">
                <h6 class="col-12 col-lg-6 col-xl-6 col-md-6 text-center mx-auto z-bg-secondary-light-opac border py-2 z-border-orange rounded" style="opacity: 0.8;">
                    <span style="letter-spacing: 1.2px;" class="text-warning font-italic fx-15">Veuillez lancer votre demande de contrôle parentable en vous inscrivant</span>
                </h6>
                <div class="col-12 col-lg-6 col-xl-6 col-md-6 mx-auto z-bg-secondary-light-opac border rounded z-border-orange" style="opacity: 0.8;">
                    <div class="w-100 mx-auto p-3">
                        <div class="w-100 z-color-orange">
                            <h5 class="text-center w-100">
                                <span class="fa fa-user-plus fa-3x "></span>
                                <h5 class="w-100 text-uppercase text-center">Inscription en tant que parent</h5>
                            </h5>
                            <hr class="w-100 z-border-orange mx-auto my-2">
                        </div>
                        <div class="w-100">
                            <form autocomplete="false" class="mt-3 mx-auto authentication-form" wire:submit.prevent="register" >
                                @csrf

                                @php

                                    $professions = config('app.professions');

                                @endphp
                                <div class="w-100">
                                    <div class="w-100 d-flex justify-content-between border rounded">
                                        <strong class="bi-person zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                                        <input name="name" wire:model.defer="name"  type="text" class="form-control  @error('name') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre nom complet...">
                                    </div>
                                    @error('name')
                                        <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                                    @enderror
                                </div>  
                                <div class="w-100 mt-2">
                                    <div class="w-100 d-flex justify-content-between border rounded">
                                        <strong class="bi-phone zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                                        <input name="contacts" wire:model.defer="contacts"  type="contacts" class="form-control  @error('contacts') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner vos contacts séparés par des /...">
                                    </div>
                                    @error('contacts')
                                        <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                                    @enderror
                                </div>  
                                <div class="w-100 mt-2">
                                    <div class="w-100 d-flex justify-content-between border rounded">
                                        <strong class="bi-person-workspace zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                                        <select  wire:model.defer="job" class="text-white @error('job') text-danger @enderror text-white border-left zw-85 z-bg-secondary-dark">
                                            <option value=""> Veuillez renseigner votre profession </option>
                                            @foreach($professions as $prof)
                                            <option class="" value="{{$prof}}"> {{$prof}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('job')
                                        <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                                    @enderror
                                </div> 
                                <div class="w-100 mt-2">
                                    <div class="w-100 d-flex justify-content-between border rounded">
                                        <strong class="bi-house zw-15 text-center z-color-orange" style="font-size: 1.5rem"></strong>
                                        <input name="residence" wire:model.defer="residence"  type="residence" class="form-control  @error('residence') text-danger border border-danger @enderror text-white zw-85 p-3 z-bg-secondary-dark border-left" placeholder="Veuillez renseigner votre address...">
                                    </div>
                                    @error('residence')
                                        <span class="py-1 mb-3 z-color-orange">{{$message}}</span>
                                    @enderror
                                </div> 

                                <div class="w-100 my-3 d-flex justify-center">
                                    <button type="submit" class="z-bg-orange border rounded px-5 z-scale py-2">S'inscrire</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @elseif($parentable && !$parentable->authorized)

            <div class="col-11 col-lg-8 my-3 col-xl-8 col-md-8 mx-auto z-bg-secondary-light-opac border rounded border-orange" style="opacity: 0.8; position: relative; top: 100px;">
                <div class="w-100 mx-auto p-3">
                    <div class="w-100 text-success">
                        <h5 class="text-center text-success w-100">
                            <span class="fa fa-user fa-3x "></span>
                            <h5 class="w-100 text-uppercase text-center">Inscription en tant que parent déja envoyée</h5>
                        </h5>
                        <hr class="w-100 z-border-orange mx-auto my-2">
                    </div>
                    <div class="w-100">
                        <span class="text-center text-warning text-italic" style="letter-spacing: 1.2px;">Votre demande en cours de traitement ...</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
 </div>
