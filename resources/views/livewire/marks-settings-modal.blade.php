<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Reglages'" :width="6" :icon="'fa fa-bookmark'" :modalName="'marksSettingsModal'" :modalBodyTitle="'Actions sur les notes'">
    @if($classe)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMarks">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Classe : 
                        <span class="text-warning">
                             {{ $classe->name }}
                        </span>
                        en <span class="text-white">matière</span>
                    </blockquote>
                </div>
               <div class="d-flex row w-100">
                    <div class="d-flex row w-100">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_selected') text-danger border border-danger @enderror" wire:model="subject_selected" name="subject_selected">
                                <option value="{{null}}">Toutes</option>
                                  @foreach ($subjects as $sub)
                                      <option value="{{$sub->id}}">{{$sub->name}}</option>
                                  @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-3 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le semestre </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model="semestre_id" name="semestre_id">
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
                                <option class="" value="{{null}}">Toutes</option>
                                @foreach ($marks_indexes as $ind)
                                    <option value="{{$ind}}">{{$ind}}</option>
                                @endforeach
                            </select>
                            @error('mark_index')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-2 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type de note </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('mark_type') text-danger border border-danger @enderror" wire:model="mark_type" name="mark_type">
                                <option disabled class="" value="{{null}}">Choisissez le type de notes</option>
                                @foreach ($types_of_marks as $key => $mark_type)
                                    <option value="{{$key}}">{{$mark_type}}</option>
                                @endforeach
                            </select>
                            @error('mark_type')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
               </div>
                <div class="col-12 d-flex justify-content-between row m-0 p-0 py-2">
                    <span class="cursor-pointer btn btn-primary border py-2 text-center">
                        <span class="bi-lock mx-2"></span>
                        <span >Obligatoires</span>
                    </span>
                    <span class="cursor-pointer btn btn-info border py-2 text-center">
                        <span class="bi-eye-slash mx-2"></span>
                        <span >Oublier</span>
                    </span>
                    <span class="cursor-pointer btn btn-danger border py-2 text-center">
                        <span class="bi-trash mx-2"></span>
                        <span >Supprimer</span>
                    </span>
                    <span class="cursor-pointer btn btn-success border py-2 text-center">
                        <span class="bi-check-all text-dark mx-2"></span>
                        <span >Standardiser</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto w-75 d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>