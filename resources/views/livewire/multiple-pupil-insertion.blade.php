<div>
    <div class="row w-100 mx-auto my-2">
        <span wire:click="decrementSize" class="mx-1 cursor-pointer p-2 btn btn-secondary">Diminiuer les champs</span>
        <span wire:click="incrementSize" class="cursor-pointer p-2 btn btn-primary">Augmnenter les champs</span>
    </div>
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div>
            @for($insert = 1; $insert <= $size; $insert++)
                <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark my-1">
                    <div class="card-header bg-dark">
                        <h5 class="card-title cursor-pointer" data-card-widget="collapse">Inscription {{ $insert }}</h5>
                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-between">
                            <div class="mt-0 mb-2 col-11 mx-auto">
                                
                                <div class="d-flex row">
                                    <x-z-input :type="'text'" :error="$errors->first('pupils.{{$insert}}.firstName')" :modelName="'pupils.{{$insert}}.firstName'" :labelTitle="'Le Nom apprenant'" ></x-z-input>
                                    <x-z-input :type="'text'" :error="$errors->first('pupils.{{$insert}}.lastName')" :modelName="'pupils.{{$insert}}.lastName'" :labelTitle="'Les prénoms apprenant'" ></x-z-input>
                                </div>

                               <div class="d-flex row">
                                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('pupils.{{$insert}}.contacts')" :modelName="'pupils.{{$insert}}.contacts'" :labelTitle="'Les Contacts'" ></x-z-input>
                                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('pupils.{{$insert}}.nationality')" :modelName="'pupils.{{$insert}}.nationality'" :labelTitle="'La nationalité'" ></x-z-input>
                               </div>

                               <div class="d-flex row">
                                    <x-z-input :width="'col-6'" :type="'date'" :error="$errors->first('pupils.{{$insert}}.birth_day')" :modelName="'birth_day'" :labelTitle="'La date de naissance'" ></x-z-input>
                                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('pupils.{{$insert}}.birth_city')" :modelName="'birth_city'" :labelTitle="'Lieu de naissance'" ></x-z-input>
                               </div>

                               <div class="d-flex row">
                                    <x-z-input :type="'text'" :error="$errors->first('pupils.{{$insert}}.last_school_from')" :modelName="'pupils.{{$insert}}.last_school_from'" :labelTitle="'Ecole de provénance'" ></x-z-input>
                               </div>
                                <div class="d-flex row">
                                    <div class="col-4">
                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le Sexe </label>
                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('pupils.{{$insert}}.sexe') text-danger border border-danger @enderror" wire:model.defer="pupils.{{$insert}}.sexe" name="sexe">
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
                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('pupils.{{$insert}}.classe_id') text-danger border border-danger @enderror" wire:model.defer="pupils.{{$insert}}.classe_id" name="classe_id">
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
                    </div>
                </div>

            @endfor

        </div>

         <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
</div>
