<div>
    <div class="m-0 p-0 w-100">
        <blockquote class="text-warning py-2">
            {{ count($teachers) }} Enseignants
        </blockquote>
        <hr class="text-warning w-100 m-0 p-0 bg-warning">
    </div>
    <div class="card-header d-flex justify-content-between p-0">
        <span class="ml-3 mt-2">
            <span title="Ajouter un enseignant" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addNewTeacher">
                <span class="bi-person-plus"></span>
                <span>Ajouter</span>
            </span>
        </span>
        <ul class="nav nav-pills ml-auto p-2">

            <span wire:click="disjoinAll" title="Supprimer les données de tous les enseignants relatives à cette année scolaire {{$school_year_model->school_year}} " class="fa bi-trash fx-25 mr-4 text-orange mt-2 cursor-pointer"></span>

            <span wire:click="joinAll" title="Recharger tous les enseignants pour cette année scolaire {{$school_year_model->school_year}} " class="fa fa-recycle fx-25 mr-4 text-info mt-2 cursor-pointer"></span>

            <span wire:click="resetSelectedData" title="Recharger la page" class="fa fa-recycle fx-25 mr-4 text-secondary mt-2 cursor-pointer"></span>

            <li class="nav-item mx-2">
                <select wire:change="changeSection('classe')" wire:model="classe_id_selected" class="form-select z-bg-secondary custom-select">
                    <option value=""> Enseignants selon la classe </option>
                    @foreach($classes as $cl)
                        <option value="{{$cl->id}}"> {{ $cl->name }} </option>
                    @endforeach
                </select>
            </li>

            <li class="nav-item">
                <select wire:change="changeSection('subject')" wire:model="subject_id_selected" class="form-select z-bg-secondary custom-select">
                    <option value=""> Enseignants selon la matière </option>
                    @foreach($subjects as $sub)
                        <option value="{{$sub->id}}"> {{ $sub->name }} </option>
                    @endforeach
                </select>
            </li>
        </ul>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($teachers))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="py-1">Nom et Prénoms</th>
                <th>Classes tenues en {{$school_year_model->school_year}}
                    @if($school_year_model->id !== $lastYear->id)
                        <br class="m-0 p-0">
                        @if($school_year_model->id !== $lastYear->id)
                            <small class="text-orange d-block">
                                (tenues en l'année {{$lastYear->school_year}} )
                            </small>
                        @endif
                    @endif
                </th>
                <th>Contacts</th>
                <th>Spécialité</th>
                <th>Inscrit depuis</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($teachers as $k => $t)
                    <tr class="py-3">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-left px-2"> 
                            @if($baseRoute == 'teacher_listing')
                            <a style="color: inherit !important;" class="m-0 p-0 py-1" href="{{route('user_profil', ['id' => $t->user->id])}}">
                                <span class="d-flex justify-content-between">
                                    <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                        {{ $t->name . ' ' . $t->surname }}
                                            @if($t->user->isAdmin())
                                                @if($t->user->isAdminAs('admin'))
                                                    <span class="fa fa-star text-danger"></span>
                                                    <span class="fa fa-star text-danger"></span>
                                                @elseif($t->user->isAdminAs('master'))
                                                    <span class="fa fa-star text-danger"></span>
                                                    <span class="fa fa-star text-danger"></span>
                                                    <span class="fa fa-star text-danger"></span>
                                                @elseif($t->user->isAdminAs('default'))
                                                    <span class="fa fa-star text-danger"></span>
                                                @else
                                                    <span class="fa fa-warning text-warning"></span>
                                                @endif
                                            @endif
                                    </span>
                                </span>
                            </a>
                            @else
                            <span wire:click="toList({{$t->id}})" class="m-0 p-0 py-1">
                                <span class="d-flex justify-content-between">
                                    <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                        <span class="{{$t->teaching ? '' : "text-warning"}}">{{ $t->name . ' ' . $t->surname }}</span>
                                        @if(!$t->teaching)
                                            <br>
                                            <small class="text-orange text-right font-italic">Retiré de la fonction enseignante depuis le {{$t->getLastTeachingDate()}}</small>
                                        @endif
                                        @if(in_array($t->id, $selecteds))
                                            <span class="text-success fa fa-check"></span>
                                        @endif
                                    </span>
                                </span>
                            </span>
                            @endif
                        </td>
                        <td class="text-left pl-1"> 
                            @if($t->hasClasses())
                                <span class="d-flex justify-content-between">
                                    <span class="d-flex justify-content-start">
                                        @foreach($t->getTeachersCurrentClasses() as $c)
                                            @php
                                                $cl = $c->getNumericName();
                                            @endphp
                                            <a style="color: inherit !important;" class=" @if($baseRoute == 'teacher_listing') border rounded border-white btn-secondary @endif py-1 px-2 mr-1 my-1" href=" @if($baseRoute == 'teacher_listing'){{route('classe_profil', $c->slug)}} @else # @endif">
                                                {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                            </a>
                                        @endforeach
                                    </span>
                                </span>
                            @else
                                <span class="text-white-50 font-italic small">Aucune classe assignée en {{$school_year_model->school_year}}!</span>
                            @endif

                            @if($school_year_model->id !== $lastYear->id)
                                <small class="text-orange d-block m-0 p-0">
                                    @if($t->hasClasses($lastYear->id))
                                        <span class="d-flex justify-content-between m-0 p-0">
                                            <span class="d-flex justify-content-start m-0 p-0">
                                                @foreach($t->getTeachersCurrentClasses(false, $lastYear->id) as $c_l)
                                                    @php
                                                        $cl_l = $c_l->getNumericName();
                                                    @endphp
                                                    <a style="color: inherit !important;" class=" @if($baseRoute == 'teacher_listing') border rounded border-white btn-secondary @endif py-1 px-2 mr-1" href=" @if($baseRoute == 'teacher_listing'){{route('classe_profil', $c_l->slug)}} @else # @endif">
                                                        {{ $cl_l['root'] }}<sup>{{ $cl_l['sup'] }} </sup> {{ $cl_l['idc'] }}
                                                    </a>
                                                @endforeach
                                            </span>
                                        </span>
                                    @else
                                        Aucune classe assignée en {{$lastYear->school_year}}!
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td class="text-center"> {{ $t->contacts }}</td>
                        <td class="text-center"> {{ $t->speciality() ? $t->speciality()->name : 'Non définie' }}</td>
                        <td class="text-center"> {{ $t->user->getDateAgoFormated($t->user->created_at) }}</td>
                        <td class="text-center"> 
                            <span class="w-100 m-0 p-0">
                                @if($t->isTeacherOfThisYear())
                                    <span title="Rafraichir les données relatives à l'année {{$school_year_model->school_year}} du professeur {{$t->getFormatedName() }} " wire:click="disjoin({{$t->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                        <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                    </span>
                                @else
                                    <span title="Générer une base de données pour le professeur {{$t->getFormatedName() }} pour l'année {{$school_year_model->school_year}} " wire:click="join({{$t->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                        <span class="text-success cursor-pointer fa fa-recycle py-2 px-2"></span>
                                    </span>
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>  

        <blockquote class="text-warning text-right float-right font-italic small border-top border-warning">Les enseignants colorés en jaune sont ceux qui n'enseignent plus selon la plateforme de l'école depuis un certain temps</blockquote>          
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