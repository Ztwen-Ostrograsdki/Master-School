<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Définition de la période des semestres ou trimestres'" :width="6" :icon="'fa fa-calendar'" :modalName="'definedSemestrePeriodModal'" :modalBodyTitle="'Période des semestres ou trimestres'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 row shadow justify-between m-0 p-0">
                        <div class="border border-secondary my-1 shadow w-100">
                            <div class="col-12 m-0 p-0">
                                <h6 class="py-2 m-0 p-0 w-100 px-2"> 
                                    <span class="text-left"> {{$semestre_type}} 1 </span>
                                    <span class="text-warning text-right float-right">
                                        <span>{{ $period1_string }}</span>
                                        <span class="mx-2 text-info"> ( {{ $period1_weeks ? $period1_weeks : ' 0 Semaine 0 Jour ' }} )</span>
                                    </span>
                                </h6>
                                <hr class="bg-secondary m-0 p-0 w-100">
                                <div class="mt-2 w-100 p-2">
                                    <div class="d-flex justify-content-between ">
                                        Du :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period11') text-danger border border-danger @enderror" wire:model="period11" name="period11"/>
                                        Au :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period12') text-danger border border-danger @enderror" wire:model="period12" name="period12"/>
                                    </div>
                                    @error('period11')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                    @error('period12')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                    </div>


                    <div class="col-12 row shadow justify-between m-0 p-0 mt-2">
                        <div class="border border-secondary my-1 shadow w-100">
                            <div class="col-12 m-0 p-0">
                                <h6 class="py-2 m-0 p-0 w-100 px-2"> 
                                    <span class="text-left"> {{$semestre_type}} 2 </span>
                                    <span class="text-warning text-right float-right">
                                        <span>{{ $period2_string }}</span>
                                        <span class="mx-2 text-info"> ( {{ $period2_weeks ? $period2_weeks : ' 0 Semaine 0 Jour ' }} )</span>
                                    </span>
                                </h6>
                                <hr class="bg-secondary m-0 p-0 w-100">
                                <div class="mt-2 w-100 p-2">
                                    <div class="d-flex justify-content-between ">
                                        Du :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period21') text-danger border border-danger @enderror" wire:model="period21" name="period21"/>
                                        Au :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period22') text-danger border border-danger @enderror" wire:model="period22" name="period22"/>
                                    </div>
                                    @error('period21')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                    @error('period22')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    @if($semestre_type == 'Trimestre')
                    <div class="col-12 row justify-between shadow m-0 p-0 mt-2">
                        <div class="border border-secondary my-1 shadow w-100">
                            <div class="col-12 m-0 p-0">
                                <h6 class="py-2 m-0 p-0 w-100 px-2"> 
                                    <span class="text-left"> {{$semestre_type}} 3 </span>
                                    <span class="text-warning text-right float-right">
                                        <span>{{ $period3_string }}</span>
                                        <span class="mx-2 text-info"> ( {{ $period3_weeks ? $period3_weeks : ' 0 Semaine 0 Jour ' }} )</span>
                                    </span>
                                </h6>
                                <hr class="bg-secondary m-0 p-0 w-100">
                                <div class="mt-2 w-100 p-2">
                                    <div class="d-flex justify-content-between ">
                                        Du :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period31') text-danger border border-danger @enderror" wire:model="period31" name="period31"/>
                                        Au :
                                        <input type="date" class="px-2 text-white bg-transparent col-5 @error('period32') text-danger border border-danger @enderror" wire:model="period32" name="period32"/>
                                    </div>
                                    @error('period31')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                    @error('period32')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>