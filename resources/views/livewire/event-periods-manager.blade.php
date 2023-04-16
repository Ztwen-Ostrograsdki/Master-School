<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Mise en place des évènement/calendrier (' . $looked . ')'" :width="6" :icon="'fa fa-calendar'" :modalName="'eventPeriodManagerModal'" :modalBodyTitle="'Des évènements...'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 row shadow justify-between m-0 p-0">
                        <div class="border border-secondary my-1 shadow w-100">
                            <div class="col-12 m-0 p-0">
                                <h6 class="py-2 m-0 p-0 w-100 px-2"> 
                                    <span class="text-warning w-100 d-flex justify-between">
                                        <span>{{ $period_string }}</span>
                                        <span class="mx-2 text-info"> ( {{ $period_weeks ? $period_weeks : ' 0 Semaine 0 Jour ' }} )</span>
                                    </span>
                                </h6>
                                <hr class="bg-secondary m-0 p-0 w-100">
                                
                                <div class="mt-2 w-100 p-2">
                                    <div class="d-flex justify-content-between ">
                                        <div class="col-2 m-0 p-0 mx-auto">
                                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le semestre </label>
                                            <select class="px-2 form-select text-white z-bg-secondary-light-opac w-100 @error('semestre_id') text-danger border border-danger @enderror" wire:model="semestre_id" name="semestre_id">
                                                <option value="{{null}}">Sélectionner le {{$semestre_type}}</option>
                                                  @foreach ($semestres as $semestre)
                                                      <option class="text-dark" value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                                  @endforeach
                                            </select>
                                            @error('semestre_id')
                                                <small class="py-1 z-text-orange">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <div class="col-3 m-0 p-0 mx-auto">
                                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'évènement </label>
                                            <select class="px-2 form-select text-white z-bg-secondary-light-opac w-100 @error('target') text-danger border border-danger @enderror" wire:model.defer="target" name="target">
                                                <option class="text-dark" value="{{null}}">Veuillez sélectionner</option>
                                                  @foreach ($events as $value => $ev)
                                                      <option class="text-dark" value="{{$value}}"> {{ $ev }} </option>
                                                  @endforeach
                                            </select>
                                            @error('target')
                                                <small class="py-1 z-text-orange">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <div class="col-6 m-0 p-0 mx-auto">
                                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">L'évènement </label>
                                            <input placeholder="L'évènement..." class="form-control bg-transparent py-2" type="text" name="object" wire:model.defer="object">
                                            @error('object')
                                                <small class="py-1 z-text-orange">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2 w-100 p-2">
                                    <div class="d-flex justify-content-between ">
                                        Du :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start"/>
                                        Au :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('end') text-danger border border-danger @enderror" wire:model="end" name="end"/>
                                    </div>
                                    @error('start')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                    @error('end')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>