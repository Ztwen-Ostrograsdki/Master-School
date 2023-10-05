<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Faisons la présence de la classe'" :width="6" :icon="'fa fa-bookmark'" :modalName="'classePresenceLateModal'" :modalBodyTitle="'Gestionnaire de présence'">
    @if($classe && $pupils)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMark">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                        <blockquote class="text-info w-100 m-0 my-2">
                            <span class="fa bi-person-check"></span>
                            classe (e) : 
                            <span class="text-warning">
                                {{$classe->name}} 
                            </span>
                            <span class="text-white-50"> ( La présence )</span>
                        </blockquote>
                    </div>
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
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

                        </div>
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">
                             <div class="d-flex row">
                                <div class="col-2">
                                    <div class="p-0 m-0 mt-0 mb-2 row col-12 px-2">
                                        <input placeholder="La date" class="form-control bg-transparent border border-white px-2 @error('date') text-danger border-danger @enderror" wire:model="date" type="date" name="date" id="{{rand(158785, 859745525)}}">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('coming_hour_H') text-danger border border-danger @enderror" wire:model.defer="coming_hour_H" name="coming_hour_H" id="coming_hour_H">
                                        <option disabled class="" value="{{null}}">Heure d'arrivée</option>
                                        @for ($ch = 7; $ch < 20; $ch++)
                                            <option  value="{{$ch}}">{{$ch . 'H'}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-1 d-none">
                                    <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('coming_hour_M') text-danger border border-danger @enderror" wire:model="coming_hour_M" name="coming_hour_M" id="coming_hour_M">
                                        <option disabled class="" value="{{null}}">minutes d'arrivée</option>
                                        @for ($cm = 0; $cm < 60; $cm++)
                                            <option  value="{{$cm}}">{{$cm . 'min'}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-1">
                                    <select class="px-2 form-select custom-select bg-success text-dark custom-select bg-transparent w-100 @error('duration') text-danger border border-danger @enderror" wire:model.defer="duration" name="duration" id="duration">
                                        <option disabled class="" value="{{null}}">Minutes manquées</option>
                                        @for ($m = 5; $m < 3600; $m++)
                                            <option  value="{{$m}}">{{$m . 'min'}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-3 d-none">
                                    <x-z-input :width="'col-12'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('motif')" :modelName="'motif'" :labelTitle="'Le motif'" ></x-z-input>
                                </div>
                                <div class="col-1">
                                <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start" id="start">
                                        <option disabled class="" value="{{null}}">Choisissez l'heure de début</option>
                                        @for ($s = 7; $s < 19; $s++)
                                            <option  value="{{$s}}">{{$s . 'H'}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-1">
                                    <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('end') text-danger border border-danger @enderror" wire:model.defer="end" name="end" id="end">
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
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>