<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Edition des donneés personnelles'" :width="6" :icon="'fa fa-user-plus'" :modalName="'pupilPersoData'" :modalBodyTitle="'Edition des donneés personnelles'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                
                <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('firstName')" :modelName="'firstName'" :labelTitle="'Le Nom apprenant'" ></x-z-input>
                    <x-z-input :type="'text'" :error="$errors->first('lastName')" :modelName="'lastName'" :labelTitle="'Les prénoms apprenant'" ></x-z-input>
                </div>

               <div class="d-flex row">
                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('contacts')" :modelName="'contacts'" :labelTitle="'Les Contacts'" ></x-z-input>
                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('nationality')" :modelName="'nationality'" :labelTitle="'La nationalité'" ></x-z-input>
               </div>

               <div class="d-flex row">
                    <x-z-input :width="'col-6'" :type="'date'" :error="$errors->first('birth_day')" :modelName="'birth_day'" :labelTitle="'La date de naissance'" ></x-z-input>
                    <x-z-input :width="'col-6'" :type="'text'" :error="$errors->first('birth_city')" :modelName="'birth_city'" :labelTitle="'Lieu de naissance'" ></x-z-input>
               </div>

               <div class="d-flex row">
                    <div class="col-8">
                        <x-z-input :type="'text'" :error="$errors->first('last_school_from')" :modelName="'last_school_from'" :labelTitle="'Ecole de provénance'" ></x-z-input>
                    </div>
                    <div class="col-4">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le Sexe </label>
                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('sexe') text-danger border border-danger @enderror" wire:model.defer="sexe" name="sexe" id="">
                            <option disabled class="" value="{{null}}">Choisissez le Sexe</option>
                            <option  value="male">Masculin</option>
                            <option  value="female">Féminin</option>
                        </select>
                        @error('sexe')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Mettre à jour</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    