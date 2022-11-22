<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Inscription de nouveaux apprenant(e)s'" :width="6" :icon="'fa fa-user-plus'" :modalName="'insertMultiplePupilsModal'" :modalBodyTitle="'Inscription de nouveaux élèves à la classe'">
    @if($classes)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                
                <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('firstNames')" :modelName="'firstNames'" :labelTitle="'Les noms séparés par des points virgules'" ></x-z-input>
                    <x-z-input :type="'text'" :error="$errors->first('lastNames')" :modelName="'lastNames'" :placeholder="'Veuillez renseigner les prénoms  '" :labelTitle="'Les prénoms apprenant'" ></x-z-input>
                </div>

                <div class="d-flex row justify-between m-0 p-0">
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
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>
    