<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Reglages'" :width="6" :icon="'fa fa-tools'" :modalName="'marksSettingsModal'" :modalBodyTitle="'Opérations sur les notes de la classe'">
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
                    </blockquote>
                </div>
               <div class="d-flex row m-0 p-0 mx-auto w-100">
                    <div class="d-flex row w-100 justify-center">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        
                        <div class="col-6 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière </label>
                            <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id" name="subject_id">
                                <option value="all">Toutes les matières</option>
                                  @foreach ($subjects as $sub)
                                      <option value="{{$sub->id}}">{{$sub->name}}</option>
                                  @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le semestre </label>
                            <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model.defer="semestre_id" name="semestre_id">
                                <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                @foreach ($semestres as $semestre)
                                    <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                @endforeach
                            </select>
                            @error('semestre_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-between my-2 row m-0 p-0">

                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Index de la note</label>
                            <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('mark_index') text-danger border border-danger @enderror" wire:model.defer="mark_index" name="mark_index">
                                <option class="" value="all">Toutes</option>
                                <option class="" value="f">Première note</option>
                                <option class="" value="l">Dernière note</option>
                                @foreach ($marks_indexes as $ind)
                                    <option value="{{$ind}}">{{$ind}}</option>
                                @endforeach
                            </select>
                            @error('mark_index')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-6 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type de note </label>
                            <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('mark_type') text-danger border border-danger @enderror" wire:model="mark_type" name="mark_type">
                                <option disabled class="" value="{{null}}">Choisissez le type de notes</option>
                                <option class="" value="all">Tout type de notes</option>
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
                <div class="col-12 d-flex justify-content-between row m-0 my-2 p-0 py-2">
                    <span wire:click="toMustMarks" class="z-scale cursor-pointer btn btn-primary border py-2 text-center">
                        <span class="bi-lock mx-2"></span>
                        <span >Obligatoires</span>
                    </span>
                    <span wire:click="toForgetMarks" class="z-scale cursor-pointer btn btn-info border py-2 text-center">
                        <span class="bi-eye-slash mx-2"></span>
                        <span >Oublier</span>
                    </span>
                    <span wire:click="deleteMarks" class="z-scale cursor-pointer btn btn-danger border py-2 text-center">
                        <span class="bi-trash mx-2"></span>
                        <span >Supprimer</span>
                    </span>
                    <span wire:click="toNormalMarks" class="z-scale cursor-pointer btn btn-success border py-2 text-center">
                        <span class="bi-check-all text-dark mx-2"></span>
                        <span >Standardiser</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto w-75 d-flex justify-content-center pb-1 pt-1">
            <x-z-button wire:click="close" :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>