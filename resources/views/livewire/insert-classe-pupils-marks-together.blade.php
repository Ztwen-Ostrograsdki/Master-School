<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-bookmark'" :modalName="'insertClassePupilsMarksTogether'" :modalBodyTitle="$title">
    @if($classe && $pupils)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent">
        <div class="row justify-between w-100">
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
                        <div class="col-3 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le semestre </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model.defer="semestre_id" name="semestre_id">
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
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id" name="subject_id">
                                <option class="" value="{{null}}">Sélectionnez une matière</option>
                                @foreach ($subjects as $sub)
                                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-4 m-0 p-0 d-none">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année </label>
                            <select disabled class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                                <option disabled class="" value="{{null}}">Choisissez l'année</option>
                                @foreach ($school_years as $s_y)
                                    <option value="{{$s_y->id}}">{{$s_y->school_year}}</option>
                                @endforeach
                            </select>
                            @error('school_year')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type de notes </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('type') text-danger border border-danger @enderror" wire:model="type" name="type">
                                <option disabled class="" value="{{null}}">Choisissez le type de notes</option>
                                <option value="epe">Interrogations</option>
                                <option value="devoir">Devoirs</option>
                                <option value="epe-devoir">Interrogations - Devoirs</option>
                                <option value="participation">Partîcipation</option>
                                
                            </select>
                            @error('type')
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
                                    @if($type == 'epe' || $type == 'epe-devoir')
                                        <th>Notes Interro.</th>
                                    @endif
                                    @if($type == 'devoir' || $type == 'epe-devoir')
                                        <th>Notes Devoir</th>
                                    @endif
                                    @if($type == 'participation')
                                        <th>Notes Partcipation</th>
                                    @endif
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
                                    @if(2 == 2)
                                    <tr class="@isset($marks[$pupil->id]) bg-secondary-light-2 @endisset @if($targeted_pupil && $targeted_pupil !== $pupil->id) opacity-50  @else opacity-100 @endif">
                                        <th class="px-2">{{$loop->iteration}}</th>
                                        <th class="text-left pl-2">{{$pupil->getName()}}</th>
                                            @if($type == 'epe' || $type == 'epe-devoir')
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
                                            @endif

                                            @if($type == 'devoir' || $type == 'epe-devoir')
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
                                            @endif
                                            {{-- INSERTION DES NOTES DE PARTICIPATION --}}
                                        @if($type == 'participation')

                                            <th>
                                                @if(!isset($marks[$pupil->id]))
                                                    @if(!$targeted_pupil)
                                                        <input autofocus="autofocus" placeholder="Notes de Participation au format 17-11-08-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('participation_marks') text-danger border-primary @enderror" wire:model.defer="participation_marks" type="text" name="participation_marks">
                                                        @error('participation_marks')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    @else
                                                        <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                    @endif
                                                @else
                                                    @if($targeted_pupil && $targeted_pupil == $pupil->id)
                                                        <input autofocus="autofocus" placeholder="Notes de Participation au format 17-11-08-..." class="text-white form-control bg-transparent border border-white px-2 z-focus @error('participation_marks') text-danger border-primary @enderror" wire:model.defer="participation_marks" type="text" name="participation_marks">
                                                        @error('participation_marks')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    @else
                                                        <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition de notes en cours" type="text">

                                                    @endif
                                                @endif
                                            </th>
                                        @endif

                                        <th>
                                            <span class="d-flex justify-content-center w-100">
                                                @if(!isset($marks[$pupil->id]))

                                                    @if(!$targeted_pupil)
                                                        <span wire:click="pushIntoMarks('{{$pupil->id}}')" class="btn btn-primary m-0  w-100" title="Ajouter les note de {{$pupil->getName()}}">
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
                                                        <span wire:click="editMarks({{$pupil->id}})" class="btn btn-success m-0 @if(isset($marks[$pupil->id])) col-6 @else col-12 @endif " title="Notes de {{$pupil->getName()}} déjà ajoutées. Vous pouvez les éditer en cliquant sur le bouton">
                                                            <span class="fa fa-check mx-2"></span>
                                                            <span></span>
                                                        </span>
                                                    @elseif($targeted_pupil == $pupil->id)
                                                        <span wire:click="pushIntoMarks('{{$pupil->id}}')" class="btn btn-primary m-0" title="Mettre à jour les note de {{$pupil->getName()}}">
                                                            <span class="fa fa-upload mx-2"></span>
                                                            <span></span>
                                                        </span>
                                                    @endif
                                                    <span wire:click="retrievedPupilMarksFromMarksData({{$pupil->id}})" title="Effacer les notes de {{$pupil->getName()}} déjà insérées" class="btn btn-danger m-0" title="Ajouter les note de {{$pupil->getName()}}">
                                                        <span class="fa fa-trash mx-2"></span>
                                                        <span></span>
                                                    </span>
                                                @endif
                                            </span>
                                        </th>

                                    </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <span wire:click="submitMarks" class="text-dark btn btn-info border border-white mx-1 z-scale col-6">Valider la soumission des notes de classe</span>
            <span wire:click="flushMarksTabs" class="text-dark btn btn-secondary border border-white ml-3 z-scale col-3">Tout effacer</span>
        </div>
    </form>
    @endif
</x-z-modal-generator>