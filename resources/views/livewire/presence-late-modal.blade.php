<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Faisons la présence de la classe'" :width="6" :icon="'fa fa-bookmark'" :modalName="'classePresenceLateModal'" :modalBodyTitle="'Gestionnaire de présence'">
    @if($classe && $pupils && $subject)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent">
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
                            <span class="text-white-50 float-right text-right"> ( En {{ $subject->name }} )</span>
                        </blockquote>
                    </div>
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">
                             <div class="d-flex row">
                                <div class="col-4">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Date du cours</label>
                                    <div class="p-0 m-0 mt-0 mb-2 row col-12 px-2">
                                        <input placeholder="La date" class="form-control bg-transparent border border-white px-2 @error('date') text-danger border-danger @enderror" wire:model="date" type="date" name="date" id="{{rand(158785, 859745525)}}">
                                    </div>
                                    @error('date')
                                        <small class="text-orange">{{$message}}</small>
                                    @enderror
                                </div>

                                <div class="col-3">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Heure de début</label>
                                    <select class="px-2 form-select custom-select text-dark z-bg-secondary w-100 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start" id="start">
                                        <option disabled class="" value="{{null}}">Choisissez l'heure de début</option>
                                        @for ($s = 7; $s < 19; $s++)
                                            <option  value="{{$s}}">{{$s . 'H'}}</option>
                                        @endfor
                                    </select>
                                    @error('start')
                                        <small class="text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="col-3">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Heure de début</label>
                                    <select class="px-2 form-select custom-select text-dark z-bg-secondary w-100 @error('end') text-danger border border-danger @enderror" wire:model="end" name="end" id="end">
                                        <option disabled class="" value="{{null}}">Choisissez l'heure de fin</option>
                                        @for ($e = ($start + 1); $e < 20; $e++)
                                            <option  value="{{$e}}">{{$e . 'H'}}</option>
                                        @endfor
                                    </select>
                                    @error('end')
                                        <small class="text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                                
                                <div class="col-2 d-flex justify-content-between">
                                    <span title="Annuler dernière action" wire:click="cancelLast" class="btn btn-warning cursor-pointer z-scale">
                                        <span class="fa fa-reply mt-3"></span>
                                    </span>

                                    <span title="Tout reprendre" wire:click="remake" class="btn btn-success cursor-pointer z-scale">
                                        <span class="fa fa-recycle mt-3"></span>
                                    </span>
                                </div>
                                
                            </div>
                        </div>
                        <div class="d-flex justify-content-end my-1 w-100 p-1">
                            <small class="text-warning letter-spacing-12 float-right text-right px-2">
                                {{ $date_as_string }}
                            </small>
                        </div>
                        <div style="max-height: 300px; overflow: auto;" class="py-2">
                            <div>
                                @if(count($pupils) > 0)
                                   <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                                        <col>
                                        <col>
                                        <col>
                                        <col>
                                        <colgroup span="2"></colgroup>
                                        <col>
                                        <tr class="text-center bg-secondary-light-0">
                                            <td class="py-2">No</td>
                                            <td>Nom et Prénoms</td>
                                            <th>Motif</th>
                                            <th>Heure d'arrivée</th>
                                            <th>R</th>
                                            <th>A</th>
                                            <th>C</th>
                                        </tr>
                                        
                                        @foreach($pupils as $k => $pupil)
                                            @if(!in_array($pupil->id, $hiddens) && !array_key_exists($pupil->id, $abs_data) && !array_key_exists($pupil->id, $lates_data))
                                                <tr class="">
                                                    <td class="text-center border-right">{{ $loop->iteration }}</td>
                                                    <td class="text-left px-2"> 
                                                        <div class="w-100">
                                                            {{ $pupil->getName() }}
                                                        </div>
                                                    </td>
                                                    
                                                    <td class="text-center"> 
                                                        <div class="w-100">
                                                            <input placeholder="le motif..." type="text" class="form-control" wire:model.defer="motif">
                                                        </div>
                                                    </td> 

                                                    <td>
                                                        <div class="d-flex justify-between m-0 p-0">
                                                            <div class=" col-6 p-0 m-0">
                                                                <select class="px-2 form-select custom-select z-bg-secondary w-100 border-none @error('coming_hour_H') text-danger border border-danger @enderror" wire:model.defer="coming_hour_H" name="coming_hour_H" id="coming_hour_H">
                                                                    <option disabled class="" value="{{null}}">Heure d'arrivée</option>
                                                                    @for ($ch = 7; $ch < 20; $ch++)
                                                                        <option  value="{{$ch}}">{{$ch . ' H'}}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            <div class="col-6 m-0 p-0">
                                                                <select class="px-2 form-select custom-select z-bg-secondary w-100 border-none @error('coming_hour_M') text-danger border border-danger @enderror" wire:model="coming_hour_M" name="coming_hour_M" id="coming_hour_M">
                                                                    <option disabled class="" value="{{null}}">minutes d'arrivée</option>
                                                                    @for ($cm = 0; $cm < 60; $cm++)
                                                                        <option  value="{{$cm}}">{{$cm . ' min'}}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </td>   
                                                    <td>
                                                        <span title="Marquer comme retardataire" wire:click="pushIntoLates({{$pupil->id}})" class="btn btn-warning cursor-pointer">
                                                            <span class="fa fa-clock"></span>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span title="Marquer comme absent" wire:click="pushIntoAbsents({{$pupil->id}})" class="btn bg-orange cursor-pointer">
                                                            <span class="fa bi-person-x "></span>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span title="Masquer cet apprenant" wire:click="hidePupil({{$pupil->id}})"  class="btn btn-info cursor-pointer">
                                                            <span class="fa bi-eye"></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table> 
                                @else
                                    <div>
                                        <div class="d-flex justify-content-center mx-auto w-100">
                                            <span class="fa fa-trash text-muted fa-8x"></span>
                                        </div>
                                        <blockquote class="text-warning">
                                            <span class="float-right border-top border-white w-100 d-inline-block text-right">
                                                <i class="text-warning small">La liste est vide!!!!!</i>
                                            </span>
                                        </blockquote>
                                    </div>
                                @endif 
                            </div>

                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <span wire:click="resetor" class="text-dark z-scale btn btn-secondary py-1 border col-3 mr-2">Annuler</span>
            <span wire:click="submit" class="text-dark z-scale btn btn-primary border py-1 col-6 ml-1">Valider la présence du jour</span>
        </div>
    </form>
    @endif
</x-z-modal-generator>