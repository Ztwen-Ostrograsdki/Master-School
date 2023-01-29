<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Gestionnaire de notes'" :width="6" :icon="'fa fa-bookmark'" :modalName="'markManagerModal'" :modalBodyTitle="'Gestionnaire de note: Edition-Suppresion'">
    @if($pupil && $markModel)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMark">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                        <blockquote class="text-info w-100 m-0 my-2">
                            <span class="fa bi-person-check"></span>
                            Apprenant (e) : 
                            <span class="text-warning">
                                {{$pupil->getName()}} 
                            </span>
                            <span class="text-white-50"> ( Edition de note de {{$subject_selected}} )</span>
                        </blockquote>
                    </div>
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-12 mx-auto justify-content-center m-0 p-0 my-1">
                            <span wire:click="delete" title="Supprimer définitivement cette note" class="cursor-pointer btn btn-danger w-100 border py-2 text-center">
                                <span class="bi-trash mx-2"></span>
                                <span class="text-uppercase">supprimer la note</span>
                            </span>
                        </div>
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">
                            <div class="col-5 m-0 p-0">
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
                            <div class="col-3 m-0 p-0 mt-4">
                                @if($markModel->forced_mark)
                                    <span wire:click="toUnforcedMark" title="Convertir en note standard" class="cursor-pointer btn btn-success w-100 border py-2 text-center">
                                        <span class="bi-unlock mx-2"></span>
                                        <span >Standardiser</span>
                                    </span>
                                @else
                                    <span wire:click="toForcedMark" title="Rendre cette note obligatoire pour le calcule des moyennes" class="cursor-pointer btn btn-warning w-100 border py-2 text-center">
                                        <span class="bi-lock mx-2"></span>
                                        <span >Note obligatoire</span>
                                    </span>
                                @endif

                            </div>

                            <div class="col-3 m-0 p-0 mt-4">
                                @if($markModel->forget)
                                    <span wire:click="toUnforgetMark" title="Ne plus oublier cette note" class="cursor-pointer btn btn-primary w-100 border py-2 text-center">
                                        <span class="bi-eye mx-2"></span>
                                        <span >Ne plus oublier</span>
                                    </span>
                                @else
                                    <span wire:click="toForgetMark" title="Oublier cette note pour le calcule des moyennes" class="cursor-pointer btn btn-info w-100 border py-2 text-center">
                                        <span class="bi-eye-slash mx-2"></span>
                                        <span >Oublier cette note</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">
                            <div class="col-5 m-0 p-0 ">
                                <x-z-input :type="'text'" :error="$errors->first('mark')" :modelName="'mark'" :labelTitle="'La note...'" ></x-z-input>
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
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>