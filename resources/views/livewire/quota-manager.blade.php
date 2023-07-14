<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Gestionnaire emploi des quotas horaires'" :width="8" :icon="'fa bi-tools'" :modalName="'quotaManager'" :modalBodyTitle="'Quota horaire: Définition-Edition-Suppresion'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitQuota">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="d-flex col-12 m-0 p-0 mx-auto justify-content-between">
                        <blockquote class="text-info w-100 m-0 my-2">
                            <span class="fa fa-desktop"></span>
                            {{ $section ? ($section == 'classe' ? 'Classe ' : 'La Promotion' ): '...' }}
                            <span class="text-warning">:
                                @if($section)
                                    @if($section == 'classe')
                                        {{ $classe_selected ? $classe_selected->name : '...' }}
                                    @elseif($section == 'classe_group')
                                        {{ $classe_group_selected ? $classe_group_selected->name : '...' }}
                                    @endif
                                @endif
                                
                            </span>
                            <span class="text-white-50 ml-4"> ({{ $subject_selected ? $subject_selected->name : 'La matière...'}}) </span>
                        </blockquote>
                    </div>

                    <div class="col-12 d-flex justify-content-between row m-0 p-0">

                        <div class="col-12 d-flex justify-content-between row m-0 p-0">

                            <div class="col-4 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la section </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('section') text-danger border border-danger @enderror" wire:model="section" name="section">
                                    <option value="{{null}}">Veuillez sélectionner</option>
                                    <option value="classe">Les classes</option>
                                    <option value="classe_group">Les promotions</option>
                                        
                                </select>
                                @error('section')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="col-7 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année scolaire </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('school_year_id') text-danger border border-danger @enderror" wire:model="school_year_id" name="school_year_id">
                                    <option value="{{null}}">Veuillez sélectionner l'année scolaire</option>
                                        @foreach ($school_years as $school_year)
                                            <option value="{{$school_year->id}}">{{ $school_year->school_year }}</option>
                                        @endforeach
                                </select>
                                @error('school_year_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>


                        </div>
                        
                        <div class="col-12 d-flex justify-content-between row m-0 p-0 my-2">

                            <div class="p-0 m-0 mt-0 col-7">
                                @if($section == 'classe')
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">La classe </label>
                                    <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model="classe_id" name="classe_id">
                                        <option class="" value="{{null}}">Choisissez la classe</option>
                                        @foreach ($classes as $classe)
                                            <option  value="{{$classe->id}}">{{$classe->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('classe_id')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                @elseif($section == 'classe_group')
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">La Promotion </label>
                                    <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('classe_group_id') text-danger border border-danger @enderror" wire:model="classe_group_id" name="classe_group_id">
                                        <option value="{{null}}"> Les Promotions </option>
                                        @foreach($classe_groups as $cg)
                                            <option value="{{$cg->id}}"> Promotion {{ $cg->name }} </option>
                                        @endforeach
                                    </select>
                                    @error('classe_group_id')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="p-0 m-0 col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Quota Horaire Hebdomadaire</label>
                                <input autofocus="autofocus" placeholder="Veuillez renseigner la valeur du quotas" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('quota') text-danger border-danger @enderror" wire:model.defer="quota" type="text" name="quota">
                                @error('quota')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between my-2 m-0 p-0 col-12">

                                <div class="col-7 m-0 p-0">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La Matière </label>
                                    <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id">
                                        <option class="" value="{{null}}">Choisissez la matière/Spécialité</option>
                                        @foreach ($subjects as $sub)
                                            <option  value="{{$sub->id}}">{{$sub->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>


                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center flex-column pb-1 pt-1">
            @if($classe_id || $classe_group_id && $subject_id)
                <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
            @else
                <span disabled class="text-dark bg-secondary border text-dark px-4 col-7 text-center mx-auto btn rounded disabled">Terminer</span>
            @endif
        </div>
    </form>
</x-z-modal-generator>