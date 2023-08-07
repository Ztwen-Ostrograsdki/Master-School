<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="9" :icon="'fa bi-edit'" :modalName="'movePupilToNewClasseModal'" :modalBodyTitle="$title">
    @if($pupil)
    <form autocomplete="off" class="form-group pb-3 px-3 bg-transparent" wire:submit.prevent="submit">
        <div class="d-flex flex-column col-12 m-0 p-0 mx-auto justify-center">
            <blockquote class="text-info w-100 m-0 my-2">
                <span class="">
                    <span class="fa bi-edit mt-3"></span>
                    Apprenant : 
                    <span class="text-warning mt-3">
                        {{$pupil->getName() }} 
                    </span>
                </span>
                <span class="float-right">
                @if($current_classe)
                    Classe actuelle: 
                    <span class="text-orange font-italic">
                        {{ $current_classe->name }}
                    </span>
                @else
                    <span class="text-orange font-italic">Aucune classe en cours</span>
                @endif
                </span>
            </blockquote>
        </div>
        <div class="row justify-between m-0 p-0">
            <div class="p-0 m-0 mt-0 mb-2 col-12 mx-auto">
               <div class="d-flex row m-0 p-0">

                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-8 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez comment le déplacement doit être fait</label>
                            <select wire:model="move_type" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                <option disabled class="disabled" value="{{null}}">Choisissez</option>
                                <option value="same_promotion"> Juste changer de classe vers la même promotion et conserver les données vers la nouvelle classe </option>
                                <option value="just_move"> Juste changer de classe et conserver les données vers la nouvelle classe </option>
                                <option value="migrate"> Faire passer en classe supérieure </option>
                                <option value="reset_data"> Pas de classe définie - les données seront perdues </option>
                                <option value="to_polyvalence"> Transferer vers la classe polyvalente - les données des classes antérieures seront conservées </option>
                            </select>
                        </div>

                    </div>

                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-4 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la promotion</label>
                            <select wire:model="classe_group_id" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                <option class="" value="{{null}}">Choisissez la promotion</option>
                                @foreach($classe_groups as $cg)
                                    <option value="{{$cg->id}}"> {{$cg->name}} </option>
                                @endforeach
                            </select>
                        </div>

                        @if($classe_group_id || in_array($move_type, ['migrate', 'reset_data', 'to_polyvalence']))
                            <div class="col-7 m-0 p-0 mb-2">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la classe </label>
                                <select wire:model="classe_id" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                    <option value="{{null}}">Choisissez la classe</option>
                                    @foreach($classes as $cl)
                                        <option value="{{$cl->id}}"> {{$cl->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <span class="text-white-50 mt-4"> Veuillez sélectionner une promotion et la classe ensuite... </span>
                        @endif
                    </div>
               </div>
            </div>
        </div>

        <div class="p-0 m-0 mx-auto w-100 pb-1 pt-1 d-flex justify-content-center mt-2">
            <span @if(!$classe_id) disabled @endif title="Annuler le processus et conserver les anciennes données" wire:click="submit" class="btn btn-primary border border-white px-3 w-50">
                <span class="fa fa-upload"></span>
                <span>Valider</span>
            </span>
        </div>
    </form>
    @endif
</x-z-modal-generator>