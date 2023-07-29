<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="9" :icon="'fa bi-edit'" :modalName="'migratePupilsIntoClasseModal'" :modalBodyTitle="$title">
    @if($classe)
    <form autocomplete="off" class="form-group pb-3 px-3 bg-transparent" wire:submit.prevent="confirmed">
        <div class="d-flex flex-column col-12 m-0 p-0 mx-auto justify-center">
            <small class="text-warning">Cliquez sur un apprenat pour l'ajouter ou le retirer de la sélection!</small>
            <blockquote class="text-info w-100 m-0 my-2">
                <span class="">
                    <span class="fa bi-edit mt-3"></span>
                    Classe : 
                    <span class="text-warning mt-3">
                        {{$classe->name }} 
                    </span>
                </span>
                @if(count($data) > 0 && !$confirmation)
                    <span wire:click="submit" class="btn btn-primary float-right border border-white px-3">
                        <span class="fa fa-upload"></span>
                        <span>Terminer</span>
                    </span>
                @else
                    <span title="Confirmer les sélections" wire:click="confirmed" class="btn btn-success float-right border border-white px-3">
                        <span class="fa fa-check"></span>
                        <span>Confirmer</span>
                    </span>

                    <span title="Revenir sur les sélections" wire:click="edit" class="btn btn-info float-right border border-white px-3 mx-2">
                        <span class="fa fa-arrow-left"></span>
                        <span>Revenir en arrière</span>
                    </span>

                    
                @endif
                
            </blockquote>
        </div>
        @if(!$confirmation)
            <div class="row justify-between m-0 p-0">
                <div class="p-0 m-0 mt-0 mb-2 col-12 mx-auto">
                   <div class="d-flex row m-0 p-0">
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">

                            <div class="col-4 m-0 p-0 mb-2">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la promotion</label>
                                <select wire:model="classe_group_id_selected" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                    <option class="" value="{{null}}">Choisissez la promotion</option>
                                    @foreach($classe_groups as $cg)
                                        <option value="{{$cg->id}}"> {{$cg->name}} </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($classe_group_id_selected)
                                <div class="col-7 m-0 p-0 mb-2">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la classe </label>
                                    <select wire:model="classe_id_selected" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                        <option value="{{null}}">Choisissez la classe</option>
                                        @foreach($classes as $cl)
                                            <option value="{{$cl->id}}"> {{$cl->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <span class="text-white-50 mt-4"> Veuillez sélectionner une promotion et une classe... </span>
                            @endif
                        </div>
                   </div>
                </div>
            </div>
            @if(count($data) > 0)
                <div class="p-0 m-0 mx-auto  pb-1 pt-1 mt-3">
                    <h6 class="alert alert-info">Vous avez sélectionnés déjà {{count($data)}} apprenants!</h6>
                </div>
            @endif

            @if(count($pupils) > 0)
            <div style="height: 300px; overflow: auto;" class="mx-auto d-flex border border-white justify-content-between p-2">
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <tr class="text-center z-bg-secondary-dark py-2 bg-secondary-light-2 ">
                        <th class="py-2">No</th>
                        <th >Apprenant </th>
                        <th >Classe</th>
                        <th >Moyenne</th>
                        <th >rang</th>
                        <th >Actions</th>
                    </tr>

                    @foreach($pupils as $p)
                        @php 
                            $p_data = [];

                            $current_classe = $p->getCurrentClasse($school_year_befor_model->id, false);

                            if($current_classe){

                                $p_data = $p->annual_average($current_classe->id, $school_year_befor_model->id);

                                $cl = $current_classe->getNumericName();
                            }

                        @endphp
                        <tr @if(!in_array($p->id, $data)) title="Ajouter cet apprenant à la sélection" wire:click="pushIntoData({{$p->id}})" @else title="Retirer cet apprenant de la sélection" wire:click="retrieveFromData({{$p->id}})" @endif class="text-left text-center">
                            <th scope="row" class="text-center py-2">{{ $loop->iteration }}</th>
                            <th scope="row" class="text-center">
                                <span class="text-warning">{{$p->getName()}}</span>
                            </th>
                            <th class="text-capitalize text-center pl-2 p-0 m-0">
                                @if($current_classe)
                                    <span class="">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_befor_model->school_year }})</small>
                                @else
                                    Aucune <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_befor_model->school_year }})</small>
                                @endif
                            </th>
                            <th>
                                @if($p_data && $p_data->moy)

                                    <span class="{{$p_data->moy >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ $p_data->moy > 9 ? $p_data->moy : '0' . $p_data->moy }}
                                    </span>

                                @else
                                    <small class="text-white-50 font-italic"> - </small>
                                @endif
                            </th>
                            <th>
                                @if($p_data && $p_data->rank)

                                    <span>{{$p_data->rank}}</span><sup>{{$p_data->exp}}</sup><small>{{$p_data->base }} </small>

                                @else
                                    <small class="text-white-50 font-italic">Non classé</small>
                                @endif
                            </th>
                            <th>
                                <span class="d-flex justify-content-between w-100">
                                    @if(!in_array($p->id, $data))
                                        <span wire:click="pushIntoData({{$p->id}})" title="Ajouter cet apprenant à la sélection" class="btn btn-primary z-scale px-2 w-100">
                                            <span class="fa fa-download mt-1 fx-20 cursor-pointer"></span>
                                            <span>Ajouter</span>
                                        </span>
                                    @else  
                                        <span wire:click="retrieveFromData({{$p->id}})" title="Retirer cet apprenant de la sélection" class="btn bg-orange z-scale px-2 w-100">
                                            <span  class="fa fa-trash text-white mt-1 fx-20 cursor-pointer"></span>
                                            <span>Retirer cet apprenant</span>
                                        </span>
                                    @endif
                                </span>
                            </th>
                        </tr>
                    @endforeach
                </table>

            </div>
            @endif
        @else
{{-- SHOW AFTER SLECTEDS --}}
        <h5 class="alert bg-transparent w-100 p-2">
            Liste des apprenants sélectionnés
            <small class="float-right text-warning">{{count($data)}} apprenant(s) sélectionné(s)</small>
        </h5>
        <small class="text-warning fx-15 p-1">Cliquez sur le bouton <i class="text-orange">confirmer</i> à droite supérieur pour lancer le processus!</small>


        <div style="height: 300px; overflow: auto;" class="mx-auto border border-white justify-content-between p-2">
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <tr class="text-center z-bg-secondary-dark bg-secondary-light-2 py-2">
                        <th class="py-2">No</th>
                        <th >Apprenant </th>
                        <th >Classe</th>
                        <th >Moyenne</th>
                        <th >rang</th>
                        <th >Actions</th>
                    </tr>

                    @foreach($selecteds_pupils as $ps)
                        @php 
                            $ps_data = [];

                            $current_classe = $ps->getCurrentClasse($school_year_befor_model->id, false);

                            if($current_classe){

                                $ps_data = $ps->annual_average($current_classe->id, $school_year_befor_model->id);

                                $cl = $current_classe->getNumericName();
                            }

                        @endphp
                        <tr @if(in_array($ps->id, $data)) title="Retirer cet apprenant de la sélection" wire:click="retrieveFromData({{$ps->id}})" @endif class=" @if(!in_array($ps->id, $data))d-none @endif text-left text-center">
                            <th scope="row" class="text-center py-2">{{ $loop->iteration }}</th>
                            <th scope="row" class="text-center">
                                <span class="text-warning">{{$ps->getName()}}</span>
                            </th>
                            <th class="text-capitalize text-center pl-2 p-0 m-0">
                                @if($current_classe)
                                    <span class="">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_befor_model->school_year }})</small>
                                @else
                                    Aucune <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_befor_model->school_year }})</small>
                                @endif
                            </th>
                            <th>
                                @if($ps_data && $ps_data->moy)

                                    <span class="{{$ps_data->moy >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ $ps_data->moy > 9 ? $ps_data->moy : '0' . $ps_data->moy }}
                                    </span>

                                @else
                                    <small class="text-white-50 font-italic"> - </small>
                                @endif
                            </th>
                            <th>
                                @if($ps_data && $ps_data->rank)

                                    <span>{{$ps_data->rank}}</span><sup>{{$ps_data->exp}}</sup><small>{{$ps_data->base }} </small>

                                @else
                                    <small class="text-white-50 font-italic">Non classé</small>
                                @endif
                            </th>
                            <th>
                                <span class="d-flex justify-content-between w-100">
                                    @if(in_array($ps->id, $data))
                                        <span wire:click="retrieveFromData({{$ps->id}})" title="Retirer cet apprenant de la sélection" class="btn bg-orange z-scale px-2 w-100">
                                            <span  class="fa fa-trash text-white mt-1 fx-20 cursor-pointer"></span>
                                            <span>Retirer cet apprenant</span>
                                        </span>
                                    @endif
                                </span>
                            </th>
                        </tr>
                    @endforeach
                </table>

            </div>
            
        <table>

        </table>
        @endif

        <div class="p-0 m-0 mx-auto w-100 pb-1 pt-1 d-flex justify-content-center mt-2">
            <span title="Annuler le processus et conserver les anciennes données" wire:click="cancel" class="btn btn-secondary border border-white px-3 w-50">
                <span class="fa fa-recycle"></span>
                <span>Annuler</span>
            </span>
        </div>
    </form>
    @endif
</x-z-modal-generator>