<x-z-modal-generator :topPosition="0" :hasHeader="true" :modalHeaderTitle="'Insertion de notes relatives'" :width="7" :icon="'fa fa-pen'" :modalName="'insertPupilRelatedMarks'" :modalBodyTitle="'Sanction - Bonus'">
    @if($target && $subjects && count($subjects) > 0)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMarks">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la matière </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model.defer="subject_id" name="subject_id">
                                <option disabled class="" value="{{null}}">Choisissez la matière</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le semestre </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model.defer="semestre_id" name="semestre_id">
                                <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                  @foreach ($semestres as $semestre)
                                      <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                  @endforeach
                            </select>
                            @error('semestre_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-3 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                                <option disabled class="" value="{{null}}">Choisissez l'année</option>
                                @foreach ($school_years as $s_y)
                                    <option value="{{$s_y->id}}">{{$s_y->school_year}}</option>
                                @endforeach
                            </select>
                            @error('school_year')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-12 mt-1 d-flex justify-content-between row m-0 p-0">
                            <div class="col-7 m-0 p-0 ">
                                <x-z-input :type="'text'" :error="$errors->first('marks')" :modelName="'marks'" :labelTitle="'Notes au format 17-11-08-...'" ></x-z-input>
                            </div>
                            <div class="col-4 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le type de note </label>
                                <select class="px-2 form-select text-white z-bg-secondary w-100 @error('type') text-danger border border-danger @enderror" wire:model.defer="type" name="type">
                                    <option disabled class="" value="{{null}}">Choisissez l'année</option>
                                    @foreach ($types_of_marks as $key => $type)
                                        <option value="{{$key}}">{{$type}}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 m-0 p-0">
                            <x-z-input :width="'col-12'" :type="'text'" :error="$errors->first('motif')" :modelName="'motif'" :labelTitle="'Le motif de la note'" ></x-z-input>
                        </div>
                        <div class="col-12 mt-1 d-flex justify-content-between row m-0 p-0">
                            <div class="p-0 m-0 mt-0 mb-2 row col-6">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer"> Date de la séance de cours </label>
                                <input placeholder="La date" class="text-white form-control bg-transparent border border-white px-2 @error('date') text-danger border-danger @enderror" wire:model="date" type="date" name="date">
                            </div>
                            <div class="col-6">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer"> Horaire de cours : Début - Fin </label>
                                <div class="col-12 m-0 p-0 d-flex justify-content-between">
                                    <div class="col-6 p-0 m-0">
                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start">
                                            <option disabled class="" value="{{null}}">Choisissez l'heure de début</option>
                                            @for ($s = 7; $s < 19; $s++)
                                                <option  value="{{$s}}">{{$s . 'H'}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6 p-0 m-0 ml-1">
                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('end') text-danger border border-danger @enderror" wire:model.defer="end" name="end">
                                            <option disabled class="" value="{{null}}">Choisissez l'heure de fin</option>
                                            @for ($e = ($start + 1); $e < 20; $e++)
                                                <option  value="{{$e}}">{{$e . 'H'}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Insérer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>