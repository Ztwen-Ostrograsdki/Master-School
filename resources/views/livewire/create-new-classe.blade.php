<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Création de nouvelle classe'" :width="6" :icon="'fa fa-user-plus'" :modalName="'createNewClasse'" :modalBodyTitle="'Création de nouvelle classe'">
    <form x-data={} autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                
                <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('name')" :modelName="'name'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                </div>

                <div class="d-flex row">
                    <div class="col-8">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le cycle </label>
                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model.defer="level_id">
                            <option disabled class="" value="{{null}}">Choisissez le cycle</option>
                            @foreach ($levels as $l)
                                <option  value="{{$l->id}}">{{$l->getName()}}</option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="col-4">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">L'année scolaire </label>
                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                            <option disabled class="" value="{{null}}">Choisissez une année</option>
                            @foreach ($school_years as $school_year)
                                <option  value="{{$school_year->id}}">{{$school_year->school_year}}</option>
                            @endforeach
                        </select>
                        @error('school_year')
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
</x-z-modal-generator>
    