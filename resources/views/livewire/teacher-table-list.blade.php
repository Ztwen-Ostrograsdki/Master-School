<div>
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50 py-2">
                Liste des enseignants enregistrés sur la plateforme <span class="text-warning"></span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($teachers))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="py-1">Nom et Prénoms</th>
                <th>Classes</th>
                <th>Contacts</th>
                <th>Spécialité</th>
                <th>Inscrit depuis</th>
                @isRoutes(['teacher_listing'])
                    <th>Action</th>
                @endisRoutes
            </thead>
            <tbody>
                @foreach($teachers as $k => $t)
                    <tr class="py-3">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-left px-2"> 
                            @isRoutes(['teacher_listing'])
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
                                        {{ $t->name . ' ' . $t->surname }}
                                        @if(in_array($t->id, $selecteds))
                                            <span class="text-success fa fa-check"></span>
                                        @endif
                                    </span>
                                </span>
                            </span>
                            @endisRoutes
                        </td>
                        <td class="text-left"> 
                            @if($t->hasClasses())
                                @foreach($t->getTeachersCurrentClasses() as $c)
                                    @php
                                        $cl = $c->getNumericName();
                                    @endphp
                                    <a style="color: inherit !important;" class="@isRoutes(['teacher_listing']) border rounded border-white btn-secondary @endisRoutes py-1 px-2 mr-1 my-1" href="@isRoutes(['teacher_listing']){{route('classe_profil', $c->slug)}} @else # @endisRoutes">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </a>
                                @endforeach
                            @else
                                Aucune classe assignée!
                            @endif
                        </td>
                        <td class="text-center"> {{ $t->contacts }}</td>
                        <td class="text-center"> {{ $t->speciality() ? $t->speciality()->name : 'Non définie' }}</td>
                        <td class="text-center"> {{ $t->user->getDateAgoFormated($t->user->created_at) }}</td>
                        @isRoutes(['teacher_listing'])
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer cet utilisateur" wire:click="delete({{$t->user->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                @if($t->user->hasVerifiedEmail())
                                    <span title="Marquer comme compte non vérifié" wire:click="markEmailAsUnverified({{$t->user->id}})" class="text-warning col-3 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-person-x-fill py-2 px-2"></span>
                                </span>
                                @else
                                    <span title="Marquer comme compte déjà vérifié" wire:click="markEmailAsVerified({{$t->user->id}})" class="text-success col-3 m-0 p-0 cursor-pointer border-right border-left">
                                        <span class="fa fa-check py-2 px-2"></span>
                                    </span>
                                @endif
                                <span wire:click="manageAdminStatus({{$t->user->id}})" title="Etendre entant que administrateur" class="text-danger border-right border-left col-3 m-0 p-0 cursor-pointer">
                                    <span class="text-secondary cursor-pointer fa fa-user-secret py-2 px-2"></span>
                                </span>
                                <span wire:click="manageTeacherClasses({{$t->id}})" title="Editer les classes de {{ $t->name . ' ' . $t->surname }}" class="text-danger col-3 border-right border-left m-0 p-0 cursor-pointer">
                                    <span class="text-primary cursor-pointer fa fa-edit py-2 px-2"></span>
                                </span>
                            </span>
                        </td>
                        @endisRoutes
                    </tr>
                @endforeach
            </tbody>
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