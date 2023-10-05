<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Inscription de nouveaux apprenant(e)s'" :width="9" :icon="'fa fa-user-plus'" :modalName="'insertMultiplePupilsModal'" :modalBodyTitle="'Inscription de nouveaux élèves à la classe'">
    @if($classes)
    <form class="form-group pb-3 px-2 bg-transparent">
        <div class="d-flex justify-between w-100 mx-auto p-2">
            <div class="row justify-between col-7">
                <div class="row justify-between">
                    <div class="mt-0 mb-2 col-11 mx-auto">
                        
                        <div class="d-flex row">

                            <div class="col-12 m-0 p-0 mx-auto">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Nom de l'apprenant </label>
                                <input placeholder="Nom de l'apprenant..." class="form-control bg-transparent py-2" type="text" name="firstName" wire:model="firstName">
                                @error('firstName')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="col-12 m-0 p-0 mx-auto">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Prénoms de l'apprenant </label>
                                <input placeholder="Prénoms de l'apprenant..." class="form-control bg-transparent py-2" type="text" name="lastName" wire:model="lastName">
                                @error('lastName')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex row justify-between">
                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le Sexe </label>
                                <select class="px-2 form-select text-white z-bg-secondary w-100 @error('sexe') text-danger border border-danger @enderror" wire:model.defer="sexe" name="sexe">
                                    <option disabled class="" value="{{null}}">Choisissez le Sexe</option>
                                    <option  value="male">Masculin</option>
                                    <option  value="female">Féminin</option>
                                </select>
                                @error('sexe')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">La classe </label>
                                <select class="px-2 form-select text-white z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model.defer="classe_id" name="classe_id">
                                    <option disabled class="" value="{{null}}">Choisissez la classe</option>
                                    @foreach ($classes as $classe)
                                        <option  value="{{$classe->id}}">{{$classe->name}}</option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le cycle </label>
                                <select class="px-2 form-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model.defer="level_id" name="level_id">
                                    <option disabled class="" value="{{null}}">Choisissez le cycle</option>
                                    @foreach ($levels as $level)
                                        <option  value="{{$level->id}}">{{$level->getName()}}</option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                    @if($firstName && $lastName)
                        <span title="Ajouter {{trim(strtoupper($firstName)) . ' ' .trim(ucwords($lastName))}} à la liste des apprenants à insérer dans la base de données" wire:click="pushInto" class="text-dark btn btn-primary z-scale border">Ajouter {{trim(strtoupper($firstName)) }} </span>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-start col-5 border rounded py-2">
                <div style="overflow-y: auto; max-height: 300px;" class="w-100">
                    <div class="w-100">
                    <h6 class="text-center py-1 mt-1 text-orange">Liste en cours <small>({{count($pupils)}} apprenants)</small></h6>
                    <hr class="bg-warning w-100 m-0 p-0 mb-2">
                    @if($pupils && count($pupils) > 0)
                        <div class="mx-auto my-2">
                            @foreach($pupils as $index => $p)
                                <ol class="m-0 p-0 w-100">
                                    <li class="w-100" >
                                        <span class="@if($p['sexe'] == 'female') text-orange @endif">
                                            <span>
                                                {{ $p['firstName'] }}
                                            </span>

                                            <span>
                                                {{ $p['lastName'] }}
                                            </span>
                                        </span>

                                        <span wire:click="retrieveFrom({{$index}})" title="Retirer cet apprenant float-right text-right de la liste" class="fa fa-trash cursor-pointer z-scale text-danger"></span>
                                    </li>
                                    <hr class="bg-secondary w-100 m-0 p-0">
                                </ol>

                            @endforeach

                        </div>
                    @else
                        <h6 class="alert alert-warning mx-auto">Aucun apprenant encore ajouté!</h6>
                    @endif

                    </div>
                </div>
            </div>
        </div>

        @if(!$firstName || !$lastName &&  ($pupils && count($pupils) > 0))
        <div class="mx-auto d-flex justify-content-between w-100">
            <span wire:click="submit" title="Soumettre la liste des {{count($pupils)}} apprenants" class="cursor-pointer mt-2 z-scale col-5 btn btn-primary border border-orange mx-auto" >
                <span class="fa fa-upload"></span>
                <span class="ml-2">Soumettre la liste</span>
            </span>
        </div>
        @endif
    </form>
    @endif
</x-z-modal-generator>
    