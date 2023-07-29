<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse"></h5>
                <div class="card-tools">
                    
                </div>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link text-white dropdown-toggle border border-success" data-toggle="dropdown" href="#">
                            Détruire les clées de sécurité expirées <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu">
                                <span wire:click="destroyClasseSecuritiesKeys('{{"teachers"}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Des enseignants </span>
                                <span wire:click="destroyClasseSecuritiesKeys('{{'marks'}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Des notes </span>
                                <span wire:click="destroyClasseSecuritiesKeys" class="dropdown-item cursor-pointer" tabindex="-1"> Toutes les clés </span>
                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link text-white dropdown-toggle border border-orange" data-toggle="dropdown" href="#">
                            Opérations de verrouillage / blocage <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu">
                                <span wire:click="generateClasseSecurity('{{null}}', '{{'locked'}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Verrouiller la classe </span>
                                <span wire:click="generateClasseSecurity('{{null}}', '{{'closed'}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Fermer la classe </span>
                                <span wire:click="generateClasseSecurity('{{null}}', '{{'locked_marks'}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Bloquer insertion/édition des notes </span>
                                <span wire:click="generateClasseSecurity('{{null}}', '{{'locked_marks_updating'}}')" class="dropdown-item cursor-pointer" tabindex="-1"> Bloquer édition des notes </span>
                                
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(isset($classe) && $classe)
    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-0">
                    <span class="ml-3 mt-2">
                        <span title="Ajouter un enseignant" class="float-right text-white-50 mb-1 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="manageClasseTeachers">
                            <span class="bi-person-plus"></span>
                            <span>Définir les profs de la classe</span>
                        </span>
                    </span>
                    <ul class="nav nav-pills ml-auto p-2">
                        <span class="text-orange mx-1">
                            @if($classe)
                                @php
                                    $cl = $classe->getNumericName();
                                @endphp
                                <span class="fa fa-3x">
                                    {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                </span>
                            @else
                                <span>Classe inconnue</span>
                            @endif
                        </span>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div>
                            <blockquote class="text-warning">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 p-0 h6 text-white-50 py-2">
                                        Liste des enseignants de la <a class="text-warning" href="{{route('classe_profil', ['slug' => $classe->slug])}}">{{$classe->name}}</a> la plateforme <span class="text-warning"></span>
                                    </h6>
                                
                                </div>
                            </blockquote>
                        </div>
                        <div class="w-100 m-0 p-0 mt-3">
                        @if(count($teachers))
                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                                <col>
                                <col>
                                <col>
                                <col>
                                <col>
                                <colgroup span="{{count($semestres)}}"></colgroup>
                                <colgroup span="3"></colgroup>
                                <col>
                                <tr class="text-center z-bg-secondary-dark ">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Nom et Prénoms <span class="text-orange">(Classes)</span></th>
                                    <th rowspan="2">Emploi du temps</th>
                                    <th rowspan="2">Tient la classe depuis</th>
                                    <th rowspan="2">Contacts</th>
                                    <th colspan="{{count($semestres)}}" scope="colgroup">Nombres de notes déjà faites</th>
                                    <th rowspan="2">Actions</th>
                                </tr>
                                <tr class="text-center">
                                    @foreach($semestres as $s)
                                        <th scope="col">{{$semestre_type . ' ' . $s }}</th>
                                    @endforeach
                                </tr>
                                
                                @foreach($teachers as $t)
                                    <tr class="text-left text-center">
                                        <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                        <th class="text-capitalize text-center pl-2 p-0 m-0">
                                            <span class="d-flex justify-content-between">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                                    {{ $t->getFormatedName() }}
                                                </span>
                                                <span class="pr-1">
                                                    @if($t->hasClasses())
                                                        <span class="d-flex justify-content-between">
                                                            (<span class="d-flex justify-content-start small text-orange">
                                                                @foreach($t->getTeachersCurrentClasses() as $c)
                                                                    @php
                                                                        $cl = $c->getNumericName();
                                                                    @endphp
                                                                    <small style="color: inherit !important;" class=" py-1 px-2 mr-1 my-1">
                                                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                                                    </small>
                                                                @endforeach
                                                            </span>)
                                                        </span>
                                                    @else
                                                        <small class="text-orange">Aucune classe assignée!</small>
                                                    @endif
                                                </span>
                                            </span>
                                        </th>
                                        <th class="text-capitalize text-left p-0 m-0 px-2">
                                            @php
                                                $times = $t->getCurrentTimePlans($classe->id, $school_year_model->id);
                                            @endphp
                                            @if(count($times) > 0)

                                                @foreach($times as $tp)
                                                    <span class="d-block">
                                                        <small class="text-orange">{{$tp->day}} :</small> 
                                                        <span>
                                                            <small>{{$tp->start .'H - ' . $tp->end . 'H' }}</small>
                                                        </span>
                                                    </span>
                                                @endforeach

                                            @else
                                                <small>Pas encore définis</small>
                                            @endif
                                        </th>
                                        <th class="text-capitalize text-center pl-2 p-0 m-0">
                                            {{ $t->getInsertToClasseSince($classe->id) }}
                                        </th>
                                        <th style="letter-spacing: 1.2px;" class="text-capitalize text-center pl-2 p-0 m-0">
                                            {{ $t->contacts }}
                                        </th>
                                        @foreach($semestres as $sm)
                                            <th class="text-capitalize text-center pl-2 p-0 m-0">
                                                @php
                                                    $epe = $classe->getMarksTypeLenght($t->speciality()->id, $sm, $school_year_model->id, 'epe');

                                                    $dev = $classe->getMarksTypeLenght($t->speciality()->id, $sm, $school_year_model->id, 'devoir');


                                                @endphp
                                                <span style="letter-spacing: 1px;" class="d-flex justify-content-between flex-column">
                                                    <span>Int : 
                                                        {{ $epe > 9 ? $epe : '0' . $epe }}

                                                        @if($epe >= 2)
                                                            <small class="fa fa-check text-success"></small>
                                                        @else
                                                            <small class="fa fa-warning text-danger"></small>
                                                        @endif

                                                    </span>
                                                    <span>Dev : 
                                                        {{ $dev > 9 ? $dev : '0' . $dev }}

                                                        @if($dev >= 2)
                                                            <small class="fa fa-check text-success"></small>
                                                        @else
                                                            <small class="fa fa-warning text-danger"></small>
                                                        @endif
                                                    </span>
                                                </span>
                                            
                                            </th>
                                        @endforeach
                                        <th>

                                            <span class="d-flex justify-content-between">
                                                @if($t->hasSecurities($school_year_model->id, 'locked', $classe->id))
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked'}}', '{{'false'}}')"  title="Permettre des édtions dans  à la classe" class="fa bi-tools z-scale mx-1 text-success"></span>

                                                @else
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked'}}', '{{'true'}}')" title="Bloquer des éditions à la classe" class="fa bi-tools z-scale mx-1 text-danger"></span>

                                                @endif

                                                @if($t->hasSecurities($school_year_model->id, 'closed', $classe->id))
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'closed'}}', '{{'false'}}')" title="Permettre l'accès à la classe" class="fa fa-unlock z-scale mx-1 text-primary"></span>

                                                @else
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'closed'}}', '{{'true'}}')" title="Bloquer l'accès à la classe" class="fa fa-unlock z-scale mx-1 text-warning"></span>

                                                @endif

                                                @if($t->hasSecurities($school_year_model->id, 'locked_marks', $classe->id))
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked_marks'}}', '{{'false'}}')" title="Permettre l'insertion des notes dans la classe" class="fa fa-upload z-scale mx-1 text-success"></span>

                                                @else
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked_marks'}}', '{{'true'}}')" title="Bloquer l'insertion des notes dans la classe" class="fa fa-upload z-scale mx-1 text-danger"></span>

                                                @endif

                                                @if($t->hasSecurities($school_year_model->id, 'locked_marks_updating', $classe->id))
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked_marks_updating'}}', '{{'false'}}')" title="Permettre l'édition des notes dans la classe" class="fa fa-edit z-scale mx-1 text-success"></span>

                                                @else
                                                    <span wire:click="generateClasseSecurity('{{$t->id}}', '{{'locked_marks_updating'}}', '{{'true'}}')" title="Bloquer l'édition des notes dans la classe" class="fa fa-edit z-scale mx-1 text-warning"></span>

                                                @endif


                                            </span>

                                        </th>
                                    </tr>
                                @endforeach

                            </table>

                        @else
                            <div>
                                <blockquote class="">
                                    <span class="float-right border-top border-white w-100 d-inline-block text-right">
                                        <i class="text-warning small">La liste est viège pour le moment!!!</i>
                                    </span>
                                </blockquote>
                            </div>
                        @endif                                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else

        <div class="mx-auto w-100 justify-content-center">
            <blockquote class="text-warning">
                <div class="d-flex justify-content-between">
                    <h6 class="m-0 p-0 h6 text-white-50 py-2">
                        La classe recherchée n'existe certainement pas <span class="text-warning"></span>
                    </h6>
                
                </div>
            </blockquote>
        </div>
    @endif
</div>
