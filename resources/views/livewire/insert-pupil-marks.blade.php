<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Insertion de notes'" :width="6" :icon="'fa fa-bookmark'" :modalName="'insertPupilMarks'" :modalBodyTitle="'Insertion de nouvelles notes'">
    @if($pupil && $subject)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMarks">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Apprenant (e) : 
                        <span class="text-warning">
                            {{$pupil->getName()}} 
                        </span>
                        en <span class="text-white">{{$subject->name}}</span>
                    </blockquote>
                </div>
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
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
                        <div class="col-2 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Index de la note</label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('mark_index') text-danger border border-danger @enderror" wire:model.defer="mark_index" name="mark_index">
                                <option class="" value="{{null}}">Retirer l'index temporairement</option>
                                @for ($mi = 1; $mi <= 15; $mi++)
                                    <option value="{{$mi}}">{{$mi}}</option>
                                @endfor
                            </select>
                            @error('mark_index')
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

                        <div class="col-12 d-flex justify-content-between row m-0 p-0 mt-3">
                            <div class="col-7 m-0 p-0">
                                <div class="p-0 m-0 mt-0 mb-2 row">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Notes au format 17-11-08-...</label>
                                    <input autofocus="autofocus" placeholder="Notes au format 17-11-08-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('marks') text-danger border-danger @enderror" wire:model.defer="marks" type="text" name="marks">
                                    @error('marks')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
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