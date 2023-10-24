<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-bookmark'" :modalName="'insertClassePupilsMarksTogether'" :modalBodyTitle="$title">
    @if($classe && $pupils)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMarks">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Classe (e) : 
                        <span class="text-warning">
                            {{$classe->name}} 
                        </span>
                        en <span class="text-white">{{$subject ? $subject->name : 'Sélectionner la matière ...'}}</span>
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
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière</label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id" name="subject_id">
                                <option class="" value="{{null}}">Sélectionnez une matière</option>
                                @foreach ($subjects as $sub)
                                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-3 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année </label>
                            <select disabled class="px-2 form-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                                <option disabled class="" value="{{null}}">Choisissez l'année</option>
                                @foreach ($school_years as $s_y)
                                    <option value="{{$s_y->id}}">{{$s_y->school_year}}</option>
                                @endforeach
                            </select>
                            @error('school_year')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="mx-auto w-100 my-2">
                            <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">
                                <col>
                                <col>
                                <col>
                                <col>

                                <tr class="bg-secondary-light-2 py-2">
                                    <th class="py-2">N°</th>
                                    <th>Nom apprenant</th>
                                    <th>Notes Interro.</th>
                                    <th>Notes Devoir</th>
                                    <th>Action</th>

                                </tr>
                            </table>
                        </div>

                        <div class="w-100 py-2" style="max-height: 400px; overflow: auto;">
                            <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">
                                <col>
                                <col>
                                <col>
                                <col>
                                <col>
                                @foreach($pupils as $pupil)
                                    <tr class="@isset($marks[$pupil->id]) bg-secondary-light-2 @endisset @if($targeted_pupil && $targeted_pupil !== $pupil->id) opacity-50  @else opacity-100 @endif">
                                        <th class="px-2">{{$loop->iteration}}</th>
                                        <th>{{$pupil->getName()}}</th>
                                        <th>
                                            @if(!isset($marks[$pupil->id]))
                                                @if(!$targeted_pupil)
                                                    <input autofocus="autofocus" placeholder="Notes interros au format 17-11-08-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('epe_marks') text-danger border-primary @enderror" wire:model.defer="epe_marks" type="text" name="epe_marks">
                                                    @error('epe_marks')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                @endif
                                            @else
                                                @if($targeted_pupil && $targeted_pupil == $pupil->id)
                                                    <input autofocus="autofocus" placeholder="Notes interros au format 17-11-08-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('epe_marks') text-danger border-primary @enderror" wire:model.defer="epe_marks" type="text" name="epe_marks">
                                                    @error('epe_marks')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                @endif
                                            @endif
                                        </th>

                                        <th>
                                            @if(!isset($marks[$pupil->id]))
                                                @if(!$targeted_pupil)
                                                    <input autofocus="autofocus" placeholder="Notes devoir au format 14-18-07-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('dev_marks') text-danger border-primary @enderror" wire:model.defer="dev_marks" type="text" name="dev_marks">
                                                    @error('dev_marks')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                @endif
                                            @else
                                                @if($targeted_pupil && $targeted_pupil == $pupil->id)
                                                    <input autofocus="autofocus" placeholder="Notes devoir au format 14-18-07-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('dev_marks') text-danger border-primary @enderror" wire:model.defer="dev_marks" type="text" name="dev_marks">
                                                    @error('dev_marks')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                @endif
                                            @endif

                                        </th>
                                        <th>
                                            @if(!isset($marks[$pupil->id]))

                                                @if(!$targeted_pupil)
                                                    <span wire:click="pushIntoMarks('{{$pupil->id}}')" class="btn btn-primary w-100 m-0" title="Ajouter les note de {{$pupil->getName()}}">
                                                        <span class="fa fa-upload mx-2"></span>
                                                        <span></span>
                                                    </span>
                                                @else
                                                    <span class="btn btn-secondary w-100 m-0" title="Ajouter les note de {{$pupil->getName()}}">
                                                        <span class="fa fa-recycle mx-2"></span>
                                                        <span></span>
                                                    </span>

                                                @endif
                                            @else

                                                @if(!$targeted_pupil)
                                                    <span wire:click="editMarks({{$pupil->id}})" class="btn btn-success w-100 m-0" title="Notes de {{$pupil->getName()}} déjà ajoutées. Vous pouvez les éditer en cliquant sur le bouton">
                                                        <span class="fa fa-check mx-2"></span>
                                                        <span></span>
                                                    </span>
                                                @elseif($targeted_pupil == $pupil->id)
                                                    <span wire:click="pushIntoMarks('{{$pupil->id}}')" class="btn btn-primary w-100 m-0" title="Mettre à jour les note de {{$pupil->getName()}}">
                                                        <span class="fa fa-upload mx-2"></span>
                                                        <span></span>
                                                    </span>

                                                @elseif($targeted_pupil !== $pupil->id)
                                                    <span class="btn btn-secondary w-100 m-0" >
                                                        <span class="fa fa-recycle mx-2"></span>
                                                        <span></span>
                                                    </span>
                                                @endif
                                            @endif
                                        </th>

                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider la soumission des notes de classe</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>