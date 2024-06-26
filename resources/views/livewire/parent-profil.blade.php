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

                            <div class="m-0 p-0 px-2 d-inline-block my-2">
                                <form class="d-inline" action="">
                                    @csrf()
                                    <select id="school_year_selected" wire:model="school_year_selected" class="form-select custom-select border bg-secondary-light-0 text-white border-warning">
                                      <option disabled value="{{null}}">Veuillez sélectionner une année-scolaire</option>
                                        @foreach ($school_years as $school_year)
                                            <option value="{{$school_year->school_year}}"> Année-Scolaire {{ $school_year->school_year }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <hr class="w-100 bg-white text-white mt-2">
                        <div class="px-2" >
                            
                                <div class="row d-flex justify-content-between text-white w-100 ">
                                    @if($user->teacher && $user->teacher->teaching)
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
                                                <span>{{ $parentable->job }}</span>
                                            </h6>

                                            <h6 class="">
                                                <span class="text-warning">Apprenants suivis:</span>
                                                <span> {{ numb_formatted($parentable->pupils->count()) }} </span>
                                            </h6>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-xlg-4 col-md-12 col-12 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-clock"></span>
                                            <span>Emploi du temps ({{ $user->teacher->speciality()->name }}) </span>
                                        </h6>
                                        <hr class="m-0 p-0 bg-white text-white">
                                        <div style="max-height: 600px; overflow-y: auto;" class="d-flex justify-content-between flex-column">
                                            @if($user->teacher->hasClasses($school_year_selected))
                                                @foreach($user->teacher->getTeachersCurrentClasses(false, $school_year_selected) as $cll)
                                                    @php
                                                        $clll = $cll->getNumericName();
                                                        $time_plans = $user->teacher->getCurrentTimePlans($cll->id, $school_year_selected);
                                                    @endphp
                                                    <div class="col-11 border rounded p-1 m-1">
                                                        <div class="m-0 p-0">
                                                            <h6 class="d-flex justify-content-between">
                                                                <span class="">
                                                                    <span class="text-orange">Classe: </span>
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
                                    @endif

                                    {{-- AFFICHAGE DES DONNEES DES APPRENANTS SUIVIS PAR LE PARENT --}}
                                    @if($parentable)
                                    <div class="col-lg-4 col-xlg-4 col-md-12 col-12 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-user"></span>
                                            <span>Mes enfants</span>
                                            @if(auth()->user()->isAdminAs('master') || auth()->user()->id == $parentable->user->id)
                                                <span class="float-right fa fa-edit text-secondary cursor-pointer fx-20" title="Editer les données..."></span>
                                            @endif
                                        </h6>
                                        <hr class="m-0 p-0 bg-white text-white">

                                        <div style="max-height: 600px; overflow-y: auto;" class="row d-flex justify-content-center w-100 mx-auto">

                                            @php

                                                $sons = $parentable->pupils;

                                                $has_request = false;

                                            @endphp

                                            @if(count($sons))
                                                
                                                @foreach($sons as $son)

                                                    @php
                                                        
                                                        $has_request = $son->following_request();

                                                    @endphp

                                                    @if($has_request)

                                                        @php

                                                            $pupil = $son->pupil;

                                                        @endphp

                                                        <div class="col-11 shadow border-orange border rounded float-right p-2 m-2">
                                                            <h6 class=" text-right d-flex">
                                                                <span>
                                                                    <a class="nav-link btn btn-primary p-2" href="{{route('pupil_marks_listing_for_parent', ['id' => $pupil->id])}}">Voir les notes</a>
                                                                </span>
                                                                <span class="mx-2 btn btn-warning p-2" wire:click="delete({{$son->id}})" title="Ne plus suivre {{ $pupil->getName() }}">
                                                                    <span>Ne plus suivre</span>
                                                                    <span class=" mr-2 cursor-pointer fa fa-trash"></span>
                                                                </span>
                                                            </h6>
                                                            <hr class="bg-secondary">
                                                            <h6 class="">
                                                                <span>
                                                                    <span class="text-warning">Apprenant(e) :</span>
                                                                    <span>{{ $pupil->getName() }}</span>
                                                                </span>
                                                                
                                                            </h6>
                                                            <h6 class="">
                                                                <span>
                                                                    <span class="text-warning">N° EducMaster :</span>
                                                                    <span>{{ $pupil->educmaster ? $pupil->educmaster : $pupil->ltpk_matricule ? : 'Inconnu' }}</span>
                                                                </span>
                                                                
                                                            </h6>
                                                            <h6 class="">
                                                                <span>
                                                                    <span class="text-warning">Classe:</span>
                                                                    <span> 
                                                                        @if($pupil->getCurrentClasse($school_year_selected))
                                                                            {{ $pupil->getCurrentClasse($school_year_selected)->name }} 
                                                                        @else
                                                                            <span class="text-orange">Auncune classe assignée en {{ $school_year_selected }}</span>
                                                                        @endif
                                                                    </span>
                                                                </span>
                                                            </h6>
                                                            <h6 class="">
                                                                <span>
                                                                    <span class="text-warning">Relation familiale:</span>
                                                                    <span>{{ $parentable->parent_relation($pupil->id) }}</span>
                                                                </span>
                                                            </h6>
                                                            <hr class="bg-orange">

                                                            <h6 class="">
                                                                <div class="w-100 m-0 p-0 mx-auto">
                                                                    <span class="text-orange text-center py-2">Emploi du temps:</span>
                                                                    <div class="d-flex justify-content-between flex-column">

                                                                        <div class="d-flex justify-content-between flex-column">
                                                                            @if($pupil->getCurrentClasse($school_year_selected))

                                                                                @php
                                                                                    $time_plans = $pupil->getPupilTimePlans($school_year_selected)

                                                                                @endphp

                                                                                @if(count($time_plans))

                                                                                    @foreach($time_plans as $time_data)

                                                                                        @php

                                                                                            $subject = $time_data['subject'];

                                                                                            $horars = $time_data['times'];

                                                                                        @endphp
                                                                                        <div class="col-12 bg-secondary-light-0 border rounded p-1 m-1">
                                                                                            <div class="m-0 p-0">
                                                                                                <h6 class="d-flex justify-content-between">
                                                                                                    <span class="">
                                                                                                        <span class="text-orange"> {{ $subject->name }} </span>
                                                                                                    </span>
                                                                                                </h6>
                                                                                                <hr class="m-0 p-0 bg-secondary text-secondary">
                                                                                                <div class="m-0 p-0">
                                                                                                    @if(count($horars) > 0)
                                                                                                        @foreach($horars as $tm)
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

                                                                                @endif
                                                                            @else
                                                                                <span class="text-white-50 font-italic letter-spacing-12">
                                                                                    Aucune classe assignée!
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    
                                                                    </div>
                                                                    
                                                                </div>
                                                            </h6>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else

                                                <span>Pas d'apprenant suivi : La liste est vide! </span>

                                            @endif
                                        </div>

                                    </div>
                                    @endif

                                </div>
                        </div>
                        <div>
                            <h6>Mes demandes</h6>

                            <div class="w-100 m-0 p-0 mt-1">
                                @if(count($parent_requests) > 0)
                                    <div>
                                        <div>
                                            @foreach($parent_requests as $req)
                                                <div @if($req->refused) style="opacity: 0.5;" @endif class="col-12 my-2 @if($req->authorized) d-none @endif ">
                                                    <div class="card card-outline-secondary text-orange bg-secondary-light-0 m-0 p-0 border @if($req->refused) border-danger @else border-primary @endif">
                                                        <div class="card-header m-0">
                                                            <span class="card-title float-left">
                                                               Demande N° {{ $loop->iteration }}
                                                            </span>

                                                            @if($req->refused)
                                                                <span style="font-size: 1.4rem; font-weight: bolder; text-align: center;" class="text-danger text-center bg-warning mx-3 p-2 border border-danger px-3">REJETEE</span>
                                                            @endif

                                                            @if($req->authorized)
                                                                <span style="font-size: 1.4rem; font-weight: bolder; text-align: center;" class="text-white text-center bg-success mx-3 p-2 border border-success px-3">APPROUVEE</span>
                                                            @endif

                                                            @if($req->refused)
                                                                <span class="text-warning float-right">Désolé !!! Votre demande a été réjeté </span>
                                                                
                                                            @elseif($req->analysed && !$req->authorized)
                                                                <span class="fa fa-check text-success mx-2"></span>
                                                                <span class="text-success float-right">Votre demande a  été analysé et est en cours de traitement ...</span>

                                                            @elseif($req->authorized)

                                                                <span class="fa fa-check-all text-success mx-2"></span>
                                                                <span class="text-success float-right">Félicitations !!! Votre demande a  été analysé et approuvé</span>
                                                            @else
                                                                <span class="text-warning float-right">Votre demande a  n'a pas encore été analysé, elle en cours de traitement</span>

                                                            @endif
                                                        </div>
                                                        @php
                                                            $user = $req->parentable->user;

                                                            $parentable = $req->parentable;

                                                            $pupil = $req->pupil;
                                                        @endphp
                                                        <div class="card-body">
                                                            <p class="text-sm text-white-50 text-right"><i class="far fa-clock mr-1"></i> Vous avez envoyé cette demande {{ $req->getDateAgoFormated($req->created_at) }} </p>

                                                            <div class="my-2 border rounded p-2">

                                                                <div class="border p-2 rounded">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div class="col-7">
                                                                            <x-ztitle-liner :title="'Classe : '" :value="$pupil->classe->name"></x-ztitle-liner>
                                                                            <x-ztitle-liner :title="'Apprenant(e) :'" :value="$pupil->getName()" ></x-ztitle-liner>
                                                                            <x-ztitle-liner :title="'Date de naissance :'" :smallTitle=" '(à ' . $pupil->birth_city . ')'" classe="text-capitalize" :value="$pupil->__getDateAsString($pupil->birth_day, null)"></x-ztitle-liner>
                                                                            <x-ztitle-liner :title="'Résidence :'" :value="$pupil->residence"></x-ztitle-liner>
                                                                            <x-ztitle-liner :title="'EducMaster : '" class="text-orange" :smallTitle="'(Numéro EducMaster)'" :classes="'text-warning'" :value="$pupil->educmaster ? $pupil->educmaster : $pupil->ltpk_matricule ? : 'Inconnu'"></x-ztitle-liner>
                                                                            <x-ztitle-liner :title="'Lien de parenté :'" :value="$req->relation"></x-ztitle-liner>
                                                                            <hr class="bg-secondary m-0 p-0 my-1">

                                                                            <span class="">
                                                                                <span wire:click="delete({{$req->id}})" class="btn btn-danger p-2">Je ne souhaite plus suivre {{ mb_substr($pupil->getName(), 0, 20) }} ...</span>
                                                                            </span>

                                                                        </div>

                                                                        <div class="border border-secondary p-2">
                                                                            <h6 class="text-orange text-center p-1">Photo de profil de {{ mb_substr($pupil->getName(), 0, 20) }} ...</h6>

                                                                            <img class="border border-warning m-0 p-0" src="{{$pupil->__profil(250)}}" alt="">
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @endforeach

                                        </div>

                                    </div>
                                @else

                                    <h6>Aucune demande en cours</h6>

                                @endif
                            </div>

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
