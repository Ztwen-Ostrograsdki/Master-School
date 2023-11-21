<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Gestionnaire de modalités'" :width="6" :icon="'fa bi-calculator'" :modalName="'manageClasseModalitiesModal'" :modalBodyTitle="'Gestionnaire de calcule de moyennes des interrogations'">
    @if($classe && $subject && $semestre_id && $school_year_model)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div x-show="$wire.modality" class="col-12 mx-auto justify-content-center m-0 p-0 my-1">
                            <span wire:click="deleteThisModality" title="Supprimer définitivement cette note" class="cursor-pointer btn btn-danger w-100 border py-2 text-center">
                                <span class="bi-trash mx-2"></span>
                                <span >Juste supprimer cette modalité</span>
                            </span>
                        </div>
                        <div class="col-6 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">L'année scolaire </label>
                            <select disabled class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                                <option disabled class="" value="{{null}}">Choisissez une année</option>
                                @foreach ($school_years as $school_year)
                                    <option  value="{{$school_year->id}}">{{$school_year->school_year}}</option>
                                @endforeach
                            </select>
                            @error('school_year')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le semestre </label>
                            <select disabled class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model.defer="semestre_id" name="semestre_id">
                                <option disabled value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                  @foreach ($semestres as $semestre)
                                      <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                  @endforeach
                            </select>
                            @error('semestre_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-between row m-0 p-0 mt-2">
                            <div class="col-7 m-0 p-0 ">
                                <x-z-input :type="'text'" :error="$errors->first('value')" :modelName="'value'" :labelTitle="'Le nombre de meilleurs notes à prendre en compte...'" ></x-z-input>
                            </div>
                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">La classe </label>
                                <select disabled class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model.defer="classe_id" name="classe_id">
                                    <option disabled class="" value="{{null}}">Choisissez la classe</option>
                                    @foreach ($classes as $classe)
                                        <option  value="{{$classe->id}}">{{$classe->name}}</option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>