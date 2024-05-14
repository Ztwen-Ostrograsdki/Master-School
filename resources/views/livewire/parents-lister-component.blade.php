<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning p-0">
                <hr class=" w-100 m-0 p-0 bg-primary">
                <h6 style="letter-spacing: 1.2px" class="w-100 py-2 m-0 fx-17 text-orange text-right px-2 mr-2 font-italic">{{ count($parents) >= 10 ? count($parents) : '0' . count($parents) }} Parents d'élève enregistré(s) sur la plateforme </h6>
                <hr class=" w-100 m-0 p-0 bg-primary">
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            <div class="w-100 my-1 mt-2 d-flex justify-content-between mb-2">
                <div class="d-flex justify-content-start m-0 p-0">
                    <span class="nav-item mx-2">
                        <select wire:model="by_job" class="form-select z-bg-secondary custom-select">
                            <option value="{{null}}"> Lister les parents par profession </option>
                            <option value="all"> Liste complète </option>
                            @foreach($professions as $profession)
                                <option value="{{$profession}}">  {{ $profession }} </option>
                            @endforeach
                        </select>
                    </span>
                </div>

            </div>

            <div class="card-body z-bg-secondary">
                <div class="w-100 m-0 p-0 mt-1">
                    @if(count($parents) > 0)
                       <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <tr class="text-center fx-15 text-orange bg-secondary-light-0" style="letter-spacing: 1.2px;">
                                <td class="py-3">No</td>
                                <td>Nom et Prénoms</td>
                                <td>Profession</td>
                                <td>Compte</td>
                                <td>Contacts</td>
                                <td>Résidence</td>
                                <td>Nationalité</td>
                                <td>
                                    Nombres d'enfants
                                    <small class="text-warning d-block">(En cours)</small>
                                </td>
                                <td>Statut</td>
                                <td>Action</td>
                            </tr>

                            @foreach($parents as $p)
                            <tr class="text-center">
                                <td class="text-center ">{{ $loop->iteration }}</td>
                                <td class="text-left px-2 ">
                                    <span class="d-block">{{ $p->name }}</span>
                                    <span class="float-right">
                                        @if(!$p->authorized)
                                            <small class="text-orange font-italic letter-spacing-12">Compte parental pas encore confirmé</small>
                                        @else
                                            <small class="text-success font-italic letter-spacing-12">Compte parental déjà confirmé</small>
                                        @endif
                                    </span>

                                </td>
                                <td class="">{{ $p->job }}</td>
                                <td class="">{{ $p->user->email }}</td>
                                <td class=" ">{{ $p->contacts }}</td>
                                <td class=" ">{{ $p->residence }}</td>
                                <td class=" ">
                                    @if($p->user->nationality)
                                        {{ $p->user->nationality }}
                                    @else
                                        <small style="letter-spacing: 1.2px;" class="text-white-50 font-italic ">Non renseignée</small>
                                    @endif
                                </td>
                                <td class=" ">
                                    {{ count($p->pupils) >= 10 ? count($p->pupils) : '0' . count($p->pupils) }}
                                    <small title="Les demandes en cours de traitement..." class="text-warning mx-2"> ({{ count($p->notConfirmedsParentRequests()) >= 10 ? count($p->notConfirmedsParentRequests()) : '0' . count($p->notConfirmedsParentRequests()) }}) </small>
                                </td>
                                <td class=" ">
                                    @if($p->authorized)
                                        <small class="text-success">
                                            Confirmé!
                                        </small>
                                    @else
                                        <small class="text-warning">
                                            En cours...
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if(!$p->authorized)  
                                        <span wire:click="authorized({{$p->id}})" title="Valider le compte parental" class="text-success m-0 p-0 cursor-pointer">
                                            <span class="fa fa-check z-scale-2 py-2 px-2"></span>
                                        </span>
                                    @else
                                        <span wire:click="lock({{$p->id}})" title="Bloquer temporairement ce compte parental" class="text-warning m-0 p-0 cursor-pointer">
                                            <span class=" z-scale fa fa-lock z-scale-2 py-2 px-2"></span>
                                        </span>
                                    @endif
                                    <span wire:click="delete({{$p->user->id}})" title="Supprimer définivement ce compte parental" class="text-orange m-0 p-0 cursor-pointer">
                                        <span class="fa fa-trash py-2 px-2 z-scale-2 "></span>
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
