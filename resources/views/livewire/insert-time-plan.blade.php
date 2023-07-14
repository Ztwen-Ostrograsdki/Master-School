<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Gestionnaire emploi du temps'" :width="8" :icon="'fa bi-clock'" :modalName="'insertTimePlanModal'" :modalBodyTitle="'Emploi du temps: Définition-Edition-Suppresion'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitTimePlan">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="d-flex col-12 m-0 p-0 mx-auto justify-content-between">
                        <blockquote class="text-info w-50 m-0 my-2">
                            <span class="fa fa-desktop"></span>
                            Classe : 
                            <span class="text-warning">
                                {{ $classe ? $classe->name : 'La classe...' }}
                            </span>
                            <span class="text-white-50 ml-4"> ({{ $subject ? $subject->name : 'La matière...'}}) </span>
                        </blockquote>
                        @if(!$time_plan)
                            <span class="d-flex justify-content-between">
                                @if(count($times_plans) <= 10)
                                    <span wire:click="pushIntoTimesPlans" class="btn btn-primary border rounded cursor-pointer px-4">
                                        <span class="fa fa-plus"></span>
                                        <span>Ajouter</span>
                                    </span>
                                @endif
                                @if(count($times_plans) > 0)
                                    <span wire:click="retrieveFromTimesPlans" class="ml-1 btn btn-info border rounded cursor-pointer px-4">
                                        <span class="fa fa-trash"></span>
                                        <span>Retirer</span>
                                    </span>
                                @endif
                            </span>
                        @endif
                    </div>
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        @if($time_plan)
                            <div class="col-12 mx-auto justify-content-center m-0 p-0 my-1">
                                <span wire:click="delete" title="Supprimer définitivement cet emploi du temps" class="cursor-pointer btn btn-danger w-100 border py-2 text-center">
                                    <span class="bi-trash mx-2"></span>
                                    <span class="text-uppercase">supprimer cet emploi du temps</span>
                                </span>
                            </div>
                        @endif
                        <div class="col-12 d-flex justify-content-between row m-0 p-0 mt-2">
                            <div class="col-4 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année scolaire </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('school_year_id') text-danger border border-danger @enderror" wire:model.defer="school_year_id" name="school_year_id">
                                    <option value="{{null}}">Veuillez sélectionner l'année scolaire</option>
                                        @foreach ($school_years as $school_year)
                                            <option value="{{$school_year->id}}">{{ $school_year->school_year }}</option>
                                        @endforeach
                                </select>
                                @error('school_year_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le cycle </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model="level_id" name="level_id">
                                    <option class=" {{$teacher ? 'd-none' : ''}} " value="{{null}}">Choisissez le cycle</option>
                                    @foreach ($levels as $level)
                                        <option  value="{{$level->id}}">{{$level->getName()}}</option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">La classe </label>
                                <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model="classe_id" name="classe_id">
                                    <option class="{{$teacher_with_classe ? 'd-none' : ''}}" value="{{null}}">Choisissez la classe</option>
                                    @foreach ($classes as $classe)
                                        <option  value="{{$classe->id}}">{{$classe->name}}</option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-6 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La Matière </label>
                                <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id">
                                    <option class="{{$teacher ? 'd-none' : ''}}" value="{{null}}">Choisissez la matière/Spécialité</option>
                                    @foreach ($subjects as $sub)
                                        <option  value="{{$sub->id}}">{{$sub->name}}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                        </div>

                        <hr class="bg-secondary text-secondary m-0 p-0 w-100 mt-2">
                        <h5 class="text-center text-white-50 my-0 py-0 py-1 mx-auto w-100 text-uppercase">Le cours</h5>
                        <hr class="bg-secondary text-secondary m-0 p-0 w-100">

                        <div class="col-12 d-flex justify-content-between row m-0 p-0">
                            <div class="col-3">
                                <label class="z-text-cyan span m-0 p-0 w-100 cursor-pointer" for="">Le jour </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('day') text-danger border border-danger @enderror" wire:model="day" name="day">
                                    <option class="" value="{{null}}">Choisissez le jour</option>
                                    @foreach ($days as $dy)
                                        <option  value="{{$dy}}">{{$dy}}</option>
                                    @endforeach
                                </select>
                                @error('day')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Début </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start">
                                    <option disabled class="" value="{{null}}">Choisissez l'heure de début</option>
                                    @for ($s = 7; $s <= 17; $s++)
                                        <option  value="{{$s}}">{{$s}}H</option>
                                    @endfor
                                </select>
                                @error('start')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                            <div class="col-3">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Fin </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('end') text-danger border border-danger @enderror" wire:model="end" name="end">
                                    <option disabled class="" value="{{null}}">Choisissez l'heure de fin</option>
                                    @for ($e = 8; $e <= 19; $e++)
                                        <option  value="{{$e}}">{{$e}}H</option>
                                    @endfor
                                </select>
                                @error('end')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-2">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Durée </label>
                                <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('duration') text-danger border border-danger @enderror" wire:model="duration" name="duration">
                                    <option disabled class="" value="{{null}}">Choisissez la durée</option>
                                    @for ($t = 1; $t <= 5; $t++)
                                        <option  value="{{$t}}">0{{$t}}H</option>
                                    @endfor
                                </select>
                                @error('duration')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center flex-column pb-1 pt-1">

            @if($time_plan)
                <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
            @endif

            @if(!$time_plan)
                @if(count($times_plans) > 0)
                    <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>

                    <h6 class="text-center text-warning"> 0{{count($times_plans)}} programmes ont été ajoutés </h6>
                @else
                    <span class="text-dark bg-secondary border text-dark px-4 col-7 text-center mx-auto btn rounded">Veuillez ajouter des programmes</span>
                @endif
            @endif
        </div>
    </form>
</x-z-modal-generator>