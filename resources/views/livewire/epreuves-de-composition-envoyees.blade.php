<div>
    <div class="px-2" x-data="{all: null}">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            
            <div class="card-header bg-dark my-2"> 
                <h5 style="letter-spacing: 1.2px;" class="card-title cursor-pointer text-uppercase text-cursive text-orange" data-card-widget="collapse"> Détails sur les épreuves de composition déjà envoyées</h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                  <div class="card-tools">
                    
                    <ul class="nav nav-pills ml-auto position-relative" style="right: 100px;">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" wire:click="resetLates" tabindex="-1" href="#">Tout télécharger</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Tous réfuser</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Vérouiller les épreuves</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Signaler les retardataires</a>
                            <a class="dropdown-item" wire:click="createNewClasse" tabindex="-1" href="#">Fermer la réception des épreuves</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="m-0 p-0 px-2 d-flex">
                            <form class="d-inline" action="">
                                @csrf()
                                <select id="semestre_selected" wire:model="semestre_selected" class="form-select custom-select border border-warning">
                                  <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                    @foreach ($semestres as $semestre)
                                        <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form class="d-inline mx-2 " action="">
                                @csrf()
                                <select style="letter-spacing: 1.2px;" wire:model="target_selected" class="form-select text-uppercase font-weight-bold border border-orange custom-select ml-3">
                                    <option value="{{null}}">Veuillez sélectionner une section</option>
                                    <option value="{{2000}}">Toutes sortes</option>
                                    @foreach($epreuves_targets as $target => $title)
                                        <option value="{{$target}}" class="">Epreuves: {{$title}}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                    <div class="mx-auto w-100 my-2 p-2">
                        <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">

                            <col>
                            <col>
                            <col>
                            <colgroup span="2">
                            <col>
                            <col>
                            <col>
                            <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-3">
                                <th rowspan="2" class="py-2">No</th>
                                <th rowspan="2">Matières / Atélier</th>
                                <th rowspan="2">Prof. chargé (AE)</th>
                                <th colspan="2" >Epreuves</th>
                                <th rowspan="2">Classe</th>
                                <th rowspan="2">Date d'envoie</th>
                                <th rowspan="2">Statut</th>
                                <th rowspan="2">Action</th>
                            </tr>
                            <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-2">
                                <th scope="colgroup">Attendues</th>
                                <th scope="colgroup">Réçues</th>
                            </tr>
                            @foreach($epreuves as $epreuve)

                                @php
                                    $classe = null;

                                    $cl = $epreuve->classe;

                                    if($cl){

                                        $classe = $cl->getNumericName();
                                    }

                                    
                                @endphp

                                <tr>
                                    <th>{{$loop->iteration}}</th>
                                    <th class="py-2 pl-2 text-left">{{$epreuve->subject->name}}</th>
                                    <th> {{$epreuve->teacher->getFormatedName()}} </th>
                                    <th> 01 </th>
                                    <th> 01 </th>
                                    <th> 
                                    <span class="">
                                        @if($classe)
                                            {{  $classe['root'] }}<sup>{{ $classe['sup'] }} </sup> {{ $classe['idc'] }}
                                        @else
                                            <span> <small>Non renseignée!</small></span>  
                                        @endif
                                    </span>
                                    </th>
                                    <th class=""> {{$epreuve->__to($epreuve->created_at, true)}} </th>
                                    <th wire:click="ztwen('{{$epreuve->name}}')"> 
                                        <span title="Télécharger l'épreuve {{$epreuve->name}}"  class="btn btn-success text-light-0 w-100">
                                            <span class="fa fa-download"></span>
                                            <span class="">Télécharger</span>
                                        </span>
                                    </th>
                                    <th>
                                        <span title="Supprimer l'épreuve {{$epreuve->name}}" wire:click="delete({{$epreuve->name}})" class="btn btn-danger w-100">
                                            <span class="fa fa-trash"></span>
                                            <span class="">Réfusé</span>
                                        </span>
                                    </th>
                                </tr>
                            @endforeach
                            <tr class="bg-secondary-light-0 fx-20 font-italic" style="letter-spacing: 1.2px;">
                                <th colspan="3" class="py-3"> Total </th>
                                <th> <small class="letter-spacing-12">Inconnue</small> </th>
                                <th> {{count($epreuves)}} </th>
                                <td> - </td>
                                <td> - </td>
                                <td> - </td>
                                <td> - </td>

                            </tr>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
