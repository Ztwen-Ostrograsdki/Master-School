<div class="m-0 p-0 w-100">
    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >
       <div class="w-100 border m-0 p-0">
          <div class="m-0 p-0 w-100"> 
             <div class="row w-100 m-0">
                @isMySelf($user)
                <div class="col-2 m-0 text-capitalize border border-dark bg-dark p-0 text-white" style="min-height: 650px;">
                   <div class="d-fex flex-column w-100 mb-3">
                        <div class="m-0" id="OpenEditPhotoProfilModal" title="Doucle cliquer pour changer la photo de profil">
                           <div class="d-flex w-100 justify-content-between cursor-pointer m-0 p-0">
                            @if($user)
                                <img src="{{$user->__profil('250')}}" alt="mon profil" class="w-100">
                            @endif
                           </div>
                        </div>
                        <hr class="m-0 p-0 bg-white w-100">
                        <div class="m-0 py-2 px-2 z-bg-secondary" wire:click="setActiveTag('editing', 'Edition de profil')">
                           <div class="d-flex w-100 justify-content-between cursor-pointer m-0 p-0">
                                <span class="bi-tools "></span>
                                <h6 class="w-100 ml-3 d-none d-lg-inline d-xl-inline">Editer profil</h6>
                                <span class="bi-pen-fill"></span>
                           </div>
                        </div>
                        <hr class="m-0 p-0 bg-white w-100">
                        <div class="m-0 py-2 px-2">

                            <div class="d-flex flex-column w-100 cursor-pointer m-0 p-0 justify-content-around">
                                @if($user && $user->isAdmin())
                                <span title="Détruire la clé de session d'administration" wire:click="destroyAdminSessionKey" class="cursor-pointer py-1 border rounded px-2">
                                    <span class="bi-trash"></span>
                                    <span class="d-none d-xxl-inline d-xl-inline d-md-inline d-lg-inline ml-1">Détruire la clé</span>
                                </span>
                                <span title="Regénérer une clé de session d'administration" wire:click="regenerateAdminKey" class="cursor-pointer py-1 my-1 border rounded px-2">
                                    <span class="bi-key"></span>
                                    <span class="d-none d-xxl-inline d-xl-inline d-md-inline d-lg-inline ml-1">Générer une clé</span>
                                </span>
                                <span title="Afficher la clé de session d'administration" wire:click="displayAdminSessionKey" class="cursor-pointer py-1 border rounded px-2">
                                    <span class="bi-eye"></span>
                                    <span class="d-none d-xxl-inline d-xl-inline d-md-inline d-lg-inline ml-1">Afficher la clé</span>
                                </span>
                                @endif
                                <span title="Se déconnecter" class="cursor-pointer py-1 border rounded px-2 my-1">
                                    <span class="bi-lock text-danger"></span>
                                    <span class="d-none d-xxl-inline d-xl-inline d-md-inline d-lg-inline ml-1">
                                        <a class="text-danger" data-toggle="modal" data-dismiss="modal" data-target="#logoutModal" href="#">Logout</a>
                                    </span>
                                </span>
                            </div>
                         </div>
                        <hr class="m-0 p-0 bg-white w-100">
                   </div>
                </div>
                @endisMySelf
                <div class=" @isMySelf($user) col-10 @else col-12 @endisMySelf border-left border-white bg-dark pb-3" >
                   <div class="w-100 p-0 m-0 @isMySelf($user) @else d-flex justify-content-between @endisMySelf mt-2 border">
                        @isMySelf($user)
                            
                        @else
                            <div class="p-0 m-0 @isMySelf($user) d-none @else col-4 @endisMySelf">
                                <div class="cursor-pointer m-0 p-0 float-left">
                                    @if($user)
                                        <img src="{{$user->__profil('250')}}" alt="mon profil" class="w-100">
                                    @endif
                               </div>
                            </div>
                        @endisMySelf
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
                           <div class="text-white-50 cursor-pointer text-center border-left p-3" data-toggle="modal" data-target="#addFriendsModal" data-dismiss="modal">
                              <span class="text-center">
                                 <span class="fa fa-user-plus fa-2x mt-3"></span>
                                 <span class="">Suivre des amis</span>
                              </span>
                           </div>
                        @endauth
                     </div>
                     </div>
                     <div class="border mt-3 p-3">
                        <div class="mx-auto justify-center d-flex w-100">
                           <h5 class=" text-white w-100">
                               <a href="{{route('upload_epreuves')}}" class="float-left text-white border h6 p-2 bg-primary z-scale border-white border">
                                   <span class="">Envoyez mes épreuves</span>
                                   <strong class="fa fa-upload text-white "></strong>
                               </a>                              
                               <a class="float-right text-white btn bg-orange border-white border" href="#">
                                   <span class="">Optimiser le profil</span>
                                   <strong class="bi-person-badge text-white "></strong>
                               </a>
                           </h5>
                        </div>
                        <hr class="w-100 bg-white text-white mt-2">
                        <div class="px-2" style="height: 500px; overflow: auto">
                            @if($user->teacher && $user->teacher->teaching)
                                <div class="row d-flex justify-content-end text-white w-100 ">
                                    <div class="col-3 shadow border-orange border rounded float-right p-2 m-2">
                                        <h6 class="text-white-50">
                                            <span class="fa fa-clock"></span>
                                            <span>Emploi du temps ({{ $user->teacher->speciality()->name }}) </span>
                                            @if(auth()->user()->isAdminAs('master'))
                                                <span wire:click="insertTeachersTimePlan({{$user->teacher->id}})" class="float-right fa fa-edit text-primary cursor-pointer fx-20 mx-1" title="Insérer l'emploi du temps..."></span>
                                                @if(count($user->teacher->getCurrentTimePlans(null, null))>0)
                                                    <span wire:click="deleteTeacherTimePlans" class="fa fa-trash text-danger cursor-pointer fx-20 mx-1 float-right" title="Supprimer les Emplois du temps de cet enseignant..."></span>
                                                @endif
                                            @endif
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
                                                                <span class="col-4">
                                                                    {{ $clll['root'] }}<sup>{{ $clll['sup'] }} </sup> {{ $clll['idc'] }}
                                                                </span>
                                                                @if(auth()->user()->isAdminAs('master'))
                                                                    <span class="col-3 d-flex justify-content-around">
                                                                        <span wire:click="insertTeachersTimePlan({{$user->teacher->id}}, {{$cll->id}})" class="float-right fa fa-edit text-primary cursor-pointer fx-20" title="Définir l'emploi du temps de cette classe..."></span>
                                                                        @if(count($time_plans) > 0)
                                                                            <span wire:click="deleteTeacherTimePlans({{$cll->id}})" class="fa fa-trash text-danger cursor-pointer fx-20" title="Supprimer l'emploi du temps de cette classe..."></span>
                                                                        @endif
                                                                    </span>
                                                                @endif
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
                                    <div class="col-4 shadow border-orange border rounded float-right p-2 m-2">
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
                                                <span>
                                                    <span class="text-warning">Spécialité:</span>
                                                    <span>{{ $user->teacher->speciality()->name }}</span>
                                                </span>
                                                @if(auth()->user()->isAdminAs('master'))
                                                    <span class="fa fa-edit ml-4 cursor-pointer fx-15 text-primary" title="Changer la matière ou la Spécialité de {{$user->teacher->getFormatedName()}}" wire:click="updateTeacherSubject({{$user->teacher->id}})"></span>
                                                @endif
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
                                                <span class="text-warning">Diplôme:</span>
                                                <span>{{ 'A renseigner' }}</span>
                                            </h6>

                                            <h6 class="">
                                                @php

                                                    $teacher_classes_paginated = $user->teacher->getTeachersCurrentClassesWithPagination();
                                                @endphp
                                                <span class="text-warning">Classes assignées: 
                                                    <i class="text-white-50">
                                                        ( {{ $teacher_classes_paginated['total'] }} classe(s) )
                                                    </i>
                                                </span>
                                                <div class="w-100">
                                                    @if($user->teacher->hasClasses())
                                                        @foreach($teacher_classes_paginated['classes'] as $teacher_classes)

                                                            <div class="w-100 row p-1 m-1 mb-1">

                                                                @foreach($teacher_classes as $c)

                                                                    @php
                                                                        $cl = $c->getNumericName();

                                                                    @endphp
                                                                    <a class="text-white small col-3 border rounded border-white btn-secondary py-2 text-center px-2 mr-2 z-scale" href="{{route('teacher_profil_as_user', ['id' => auth()->user()->teacher->id, 'classe_id' => $c->id, 'slug' => $c->slug])}}">
                                                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                                                    </a>

                                                                @endforeach

                                                            </div>
                                                            

                                                        @endforeach
                                                    @else
                                                        Aucune classe assignée!
                                                    @endif
                                                </div>
                                            </h6>

                                        </div>

                                    </div>

                                </div>
                            @endif
                        
                            @if($activeTagName == 'editing')
                               @include('livewire.components.user.user-profil-editor')
                            @endif
                        </div>
                     </div>
                </div>
             </div>
          </div>   
       </div>
    </div>
    
 </div>
