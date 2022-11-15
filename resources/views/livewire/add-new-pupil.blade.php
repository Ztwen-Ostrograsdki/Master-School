<x-z-modal-generator :topPosition="-200" :hasHeader="true" :modalHeaderTitle="'Inscription de nouvel apprenant(e)'" :width="6" :icon="'fa fa-user-plus'" :modalName="'addNewPupil'" :modalBodyTitle="'Inscription de nouvel apprenant à la classe'">
    @if($classes)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                
                <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('firstName')" :modelName="'firstName'" :labelTitle="'Le Nom apprenant'" ></x-z-input>
                    <x-z-input :type="'text'" :error="$errors->first('lastName')" :modelName="'lastName'" :placeholder="'Veuillez renseigner les prénoms de l\' apprenant '" :labelTitle="'Les prénoms apprenant'" ></x-z-input>
                </div>

               <div class="d-flex row justify-between">
                    <x-z-input :width="'col-5'" :type="'text'" :error="$errors->first('contacts')" :modelName="'contacts'" :labelTitle="'Les Contacts'" ></x-z-input>
                    <x-z-input :width="'col-5'" :type="'text'" :error="$errors->first('nationality')" :modelName="'nationality'" :labelTitle="'La nationalité'" ></x-z-input>
               </div>

               <div class="d-flex row justify-between">
                    <x-z-input :width="'col-5'" :type="'date'" :error="$errors->first('birth_day')" :modelName="'birth_day'" :labelTitle="'La date de naissance'" ></x-z-input>
                    <x-z-input :width="'col-5'" :type="'text'" :error="$errors->first('birth_city')" :modelName="'birth_city'" :labelTitle="'Lieu de naissance'" ></x-z-input>
               </div>

               <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('last_school_from')" :modelName="'last_school_from'" :labelTitle="'Ecole de provénance'" ></x-z-input>
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
    