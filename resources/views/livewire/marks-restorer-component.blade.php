<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-trash'" :modalName="'marksRestorerModal'" :modalBodyTitle="$title">
    @if($classe || $classe_id)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Classe (e) : 
                        <span class="text-warning">
                            {{ $classe ? $classe->name : 'La classe'}} 
                        </span>
                        en <span class="text-white">{{$subject ? (is_object($subject) || $subject !== 'all' ? $subject->name : 'Toutes les matières') : 'Sélectionner la matière ...'}}</span>
                    </blockquote>
                </div>
               <div class="d-flex row mt-2">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le semestre </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model.defer="semestre_id" name="semestre_id">
                                <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                <option value="all">Tous les {{$semestre_type}}s</option>
                                  @foreach ($semestres as $semestre)
                                      <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                  @endforeach
                            </select>
                            @error('semestre_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière</label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id" name="subject_id">
                                <option class="" value="{{null}}">Sélectionnez une matière</option>
                                <option value="all">Tous les notes</option>
                                @foreach ($subjects as $sub)
                                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
               </div>

               <div class="d-flex row mt-4">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-3 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type de notes </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('type') text-danger border border-danger @enderror" wire:model.defer="type" name="type">
                                <option value="{{null}}">Veuillez sélectionner le type</option>
                                <option value="all">Tous les types de notes</option>
                                    @foreach ($types_of_marks as $value => $tp)
                                        <option value="{{$value}}">{{ $tp }}</option>
                                    @endforeach
                            </select>
                            @error('type')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La période initiale</label>
                            <input placeholder="La date initiale-..." class="text-white form-control bg-transparent border border-white px-2 @error('start') text-danger border-primary @enderror" wire:model="start" type="date" name="start">
                            @error('start')
                                <small class="py-1 z-text-orange letter-spacing-12 font-italic">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La période de fin</label>
                            <input placeholder="La date de fin-..." class="text-white form-control bg-transparent border border-white px-2 @error('end') text-danger border-primary @enderror" wire:model="end" type="date" name="end">
                            @error('end')
                                <small class="py-1 z-text-orange letter-spacing-12 font-italic">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
               </div>
            </div>
        </div>

        <div class="mx-auto p-2">
            <h6 class="text-center text-warning font-italic letter-spacing-12 fx-15"> Restaurer les notes {{ $period_string ? $period_string : ' ...' }} </h6>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Lancer la restauration des notes</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>