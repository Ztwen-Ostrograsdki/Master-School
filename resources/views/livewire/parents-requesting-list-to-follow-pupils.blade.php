<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-orange p-0">
                <hr class=" w-100 m-0 p-0 bg-primary">
                <h6 style="letter-spacing: 1.2px" class="w-100 py-2 text-orange m-0 fx-17 px-2 text-right font-italic"> Les demandes de suivis d'élève par des parents en cours </h6>
                <hr class=" w-100 m-0 p-0 bg-primary">
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            <div class="w-100 my-1 mt-2 d-flex justify-content-between mb-2">
                <div class="d-flex justify-content-start m-0 p-0">
                    <span class="nav-item mx-2">
                        <select wire:model="classe_group_id_selected" class="form-select z-bg-secondary custom-select">
                            <option value=""> Lister les demandes par profession </option>
                            @foreach($professions as $profession)
                                <option value="{{$profession}}">  {{ $profession }} </option>
                            @endforeach
                        </select>
                    </span>
                </div>

            </div>

            <div class="card-body z-bg-secondary">
                <div class="w-100 m-0 p-0 mt-1">
                    @if(count($parentsRequests) > 0)
                        <div>
                            <div>

                                @foreach($parentsRequests as $req)
                                    <div class="col-12 my-2">
                                        <div class="card card-outline-secondary text-orange bg-secondary-light-0 m-0 p-0 border border-primary">
                                            <div class="card-header m-0">
                                                <h3 class="card-title">
                                                    Les demandes de Mr/Mme {{ $req->parentable->name }}
                                                    <small class="ml-4 mt-2 float-right text-warning letter-spacing-12">{{ $req->parentable->pupils->count() }} enfants déjà suivi</small>
                                                </h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool">
                                                    <i class="fas fa-trash " title="Supprimer toutes les demandes"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-tool">
                                                    <i class="fas fa-check" title="Valider toutes les demandes"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-tool" data-card-widget="card-refresh" data-source="#" data-source-selector="#card-refresh-content" data-load-on-init="false">
                                                    <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                    <i class="fas fa-expand"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                    <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @php
                                                $user = $req->parentable->user;

                                                $parentable = $req->parentable;

                                                $pupil = $req->pupil;


                                            @endphp
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <x-ztitle-liner :title="'Adresse mail : '" :value="$user->email"></x-ztitle-liner>
                                                    <x-ztitle-liner :title="'Contacts : '" :value="$parentable->contacts"></x-ztitle-liner>
                                                    <x-ztitle-liner :title="'Résidence : '" :value="$parentable->residence"></x-ztitle-liner>
                                                    <x-ztitle-liner :title="'Profession : '" :value="$parentable->job"></x-ztitle-liner>
                                                </div>
                                                <hr class="bg-secondary m-0 p-0">

                                                <div class="my-2 border rounded p-2">

                                                    <div class="border p-2 rounded">

                                                            <p class="text-sm text-muted text-right"><i class="far fa-clock mr-1"></i> Demande envoyée {{ $req->getDateAgoFormated($req->created_at) }} </p>

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
                                                                    <span class="btn btn-primary p-2">Valider la demande</span>
                                                                    <span class="btn btn-success p-2">Marquer déjà analysée</span>
                                                                    <span class="btn btn-warning p-2">Réfuser la demande</span>
                                                                    <span class="btn btn-danger p-2">Annuler la demande</span>
                                                                </span>


                                                            </div>
                                                            <div class="border border-secondary p-2">
                                                                <h6 class="text-orange text-center p-1">Photo de profil de {{ $pupil->getName() }}</h6>

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
