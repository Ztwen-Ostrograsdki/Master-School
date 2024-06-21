<div class="w-100 p-0 m-0">
    @if($classe)
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning p-0">
                <hr class=" w-100 m-0 p-0 bg-primary">
                <h6 style="letter-spacing: 1.2px" class="w-100 py-2 m-0 fx-17 text-orange text-right px-2 mr-2 font-italic">{{ numb_formatted(count($marks_requests)) }} Les requêtes de modification de notes enregistrées pour la classe de {{ $classe->name }} </h6>
                <hr class=" w-100 m-0 p-0 bg-primary">
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            <div class="w-100 my-1 mt-2 d-flex justify-content-between mb-2">
                <div class="d-flex justify-content-start m-0 p-0">
                    <span class="nav-item mx-2">
                        <select wire:model="by_job" class="form-select z-bg-secondary custom-select">
                            <option value="{{null}}"> Lister  par cible </option>
                            <option value="all"> Liste complète </option>
                        </select>
                    </span>
                </div>

            </div>

            <div class="card-body z-bg-secondary">
                <div class="w-100 m-0 p-0 mt-1">
                    @if(count($marks_requests) > 0)
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
                            <tr class="text-center fx-15 text-orange bg-secondary-light-0" style="letter-spacing: 1.2px;">
                                <td class="py-3">No</td>
                                <td>Utilisateur / Prof</td>
                                <td>Spécialité</td>
                                <td>Elève</td>
                                <td>Type</td>
                                <td>Note normale</td>
                                <td>Nouvelle note</td>
                                <td>Action</td>
                            </tr>

                            @foreach($marks_requests as $req)
                            <tr title="Cliquer pour sélectionner et faire une action globale sur les sélections" class="text-center @if($selecteds && isset($selecteds[$req->id])) border text-warning border-success bg-secondary-light-3 @endif ">
                                @php

                                    $updater = $req->getEditor();

                                @endphp 
                                <td class="text-center ">{{ $loop->iteration }}</td>
                                <td wire:click="pushToSelecteds({{$req->id}})"> 
                                    {{ $updater ? ( $updater->teacher ? $updater->teacher->getName() : $updater->pseudo ) : 'Inconnue' }} 
                                        @if($selecteds && isset($selecteds[$req->id]))
                                            <span class="fa fa-check text-success fx-20 float-right mt-1 mr-1"></span>
                                        @endif
                                </td>
                                <td>
                                    {{ $updater ? ( $updater->teacher ? $updater->teacher->speciality()->name : 'Inconnue' ) : 'Inconnue' }}
                                </td>
                                <td>
                                    {{ $req->pupil->getName() }}
                                </td>
                                <td>
                                    {{ get_mark_type($req->type) }}
                                </td>
                                <td class="text-success fx-17 cursive" title="Date d'insertion de la note: {{ $req->__to($req->created_at, true, true) }}">
                                    {{ numb_formatted($req->value) }}
                                </td>
                                <td class="text-orange fx-17 cursive" title="Date d'édition: {{$req->__to($req->updated_at, true, false)}}">
                                    {{ numb_formatted($req->editing_value) }}
                                </td>
                                
                                <td>
                                    <span wire:click="authorized({{$req->id}})" title="Valider le note" class="m-0 p-0 btn btn-success border p-1 cursor-pointer">
                                        <span>Approuver</span>
                                        <span class="fa fa-check z-scale-2 py-2 px-2"></span>
                                    </span>
                                    <span wire:click="refused({{$req->id}})" title="Rejéter la demande et supprimer la note" class="btn btn-warning m-0 p-0 border p-1 cursor-pointer">
                                        <span>Rejéter</span>
                                        <span class=" z-scale fa fa-trash z-scale-2 py-2 px-2"></span>
                                    </span>
                                    <span wire:click="delete({{$req->id}})" title="Supprimer la demande" class="btn btn-danger border p-1 m-0 p-0 cursor-pointer">
                                        <span>Suppr.</span>
                                        <span class="fa fa-trash py-2 px-2 z-scale-2 "></span>
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="w-100 mx-auto p-2">

                            <h6 class="text-center fx-20 letter-spacing-12 text-orange">
                                Aucune requête n'a été enregistré pour la classe de <span class="text-warning letter-spacing-12">{{ $classe->name}}</span>
                            </h6>

                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
