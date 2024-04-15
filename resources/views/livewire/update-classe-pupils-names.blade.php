<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-book'" :modalName="'updateClassePupilsNames'" :modalBodyTitle="$title">
    @if($classe)
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
                    </blockquote>
                </div>
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        

                        <div class="mx-auto w-100 my-2">
                            <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">
                                <col>
                                <col>
                                <col>
                                <col>

                                <tr class="bg-secondary-light-2 py-2">
                                    <th class="py-2">N°</th>
                                    <th>Apprenant</th>
                                    <th>Nouveau Nom et Prénoms</th>
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
                                    <tr class="@isset($data[$pupil->id]) bg-secondary-light-2 @endisset @if($targeted_pupil && $targeted_pupil !== $pupil->id) opacity-50  @else opacity-100 @endif">
                                        <th class="px-2">{{$loop->iteration}}</th>
                                        <th class="text-left pl-2">
                                            {{$pupil->getName()}}


                                        </th>
                                        <th>
                                            @if(!isset($names_data[$pupil->id]))
                                                @if(!$targeted_pupil)
                                                    <input autofocus="autofocus" placeholder="Nouveau les noms de {{$pupil->getName()}}" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('upd_lastName') text-danger border-primary @enderror" wire:model.defer="upd_lastName" type="text" name="upd_lastName">
                                                    @error('upd_lastName')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition des noms de {{$pupil->getName()}} en cours ..." type="text">

                                                @endif
                                            @else
                                                @if($targeted_pupil && $targeted_pupil == $pupil->id)
                                                    <input autofocus="autofocus" placeholder="Nouveau les noms de {{$pupil->getName()}}" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('upd_lastName') text-danger border-primary @enderror" wire:model.defer="upd_lastName" type="text" name="upd_lastName">
                                                    @error('upd_lastName')
                                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                                    @enderror
                                                @else
                                                    <input disabled value="{{$names_data[$pupil->id]}}" class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition des noms de {{$pupil->getName()}} en cours..." type="text">

                                                @endif
                                            @endif

                                        </th>
                                        <th>
                                            <span class="d-flex justify-content-center w-100">
                                                @if(!isset($names_data[$pupil->id]))

                                                    @if(!$targeted_pupil)
                                                        <span wire:click="pushIntoNamesData('{{$pupil->id}}')" class="btn btn-primary m-0  w-100" title="Ajouter les noms de {{$pupil->getName()}}">
                                                            <span class="fa fa-upload mx-2"></span>
                                                            <span></span>
                                                        </span>
                                                    @else
                                                        <span class="btn btn-secondary w-100 m-0" title="Ajouter les noms de {{$pupil->getName()}}">
                                                            <span class="fa fa-recycle mx-2"></span>
                                                            <span></span>
                                                        </span>

                                                    @endif
                                                @else

                                                    @if(!$targeted_pupil)
                                                        <span wire:click="editNamesData({{$pupil->id}})" class="btn btn-success m-0 @if(isset($names_data[$pupil->id])) col-6 @else col-12 @endif" title="les noms de {{$pupil->getName()}} . Vous pouvez les éditer en cliquant sur le bouton">
                                                            <span class="fa fa-check mx-2"></span>
                                                            <span></span>
                                                        </span>
                                                    @elseif($targeted_pupil == $pupil->id)
                                                        <span wire:click="pushIntoNamesData('{{$pupil->id}}')" class="btn btn-primary m-0" title="Mettre à jour les noms de {{$pupil->getName()}}">
                                                            <span class="fa fa-upload mx-2"></span>
                                                            <span></span>
                                                        </span>
                                                    @endif
                                                    <span wire:click="retrievedPupilFromNamesData({{$pupil->id}})" title="Réinitialiser les noms de {{$pupil->getName()}}" class="btn btn-danger m-0">
                                                        <span class="fa fa-trash mx-2"></span>
                                                        <span></span>
                                                    </span>
                                                @endif
                                            </span>
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
            <span wire:click="submit" class="text-dark @if($names_data == [] || !$names_data) d-none @endif btn btn-info border border-white mx-1 z-scale col-6">Soummettre lees nomss de classe</span>
            <span wire:click="flushDataTabs" class="text-dark btn btn-secondary border border-white ml-3 z-scale col-3">Tout Réinitialiser</span>
        </div>
    </form>
    @endif
</x-z-modal-generator>