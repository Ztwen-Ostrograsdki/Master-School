<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Défintion des enseignants de classe'" :width="6" :icon="'fa bi-edit'" :modalName="'definedClasseTeachersModal'" :modalBodyTitle="'Edition des enseignants de chaque matière'">
    @if($classe)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="confirmed">
        <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
            <blockquote class="text-info w-100 m-0 my-2">
                <span class="">
                    <span class="fa bi-edit mt-3"></span>
                    Classe : 
                    <span class="text-warning mt-3">
                        {{$classe->name }} 
                    </span>
                </span>

                @if($subject_id && $teacher_id)
                    <span wire:click="pushIntoData" class="btn btn-success float-right">
                        <span class="fa fa-plus"></span>
                        <span>Ajouter cette donnée</span>
                    </span>
                @elseif(count($data) > 0 && !$confirmation)
                    <span wire:click="submit" class="btn btn-primary float-right border border-white px-3">
                        <span class="fa fa-upload"></span>
                        <span>Terminer</span>
                    </span>
                @else

                    <span title="Confirmer les sélections" wire:click="confirmed" class="btn btn-success float-right border border-white px-3">
                        <span class="fa fa-check"></span>
                        <span>Confirmer</span>
                    </span>

                    <span title="Revenir sur les sélections" wire:click="edit" class="btn btn-info float-right border border-white px-3 mx-2">
                        <span class="fa fa-arrow-left"></span>
                        <span>Revenir en arrière</span>
                    </span>

                    
                @endif
                
            </blockquote>
        </div>
        @if(!$confirmation)
            <div class="row justify-between m-0 p-0">
                <div class="p-0 m-0 mt-0 mb-2 col-12 mx-auto">
                   <div class="d-flex row m-0 p-0">
                        <div class="col-12 d-flex justify-content-between row m-0 p-0">

                            <div class="col-4 m-0 p-0 mb-2">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la matière</label>
                                <select wire:model="subject_id" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                    <option class="" value="{{null}}">Choisissez la matière</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{$sub->id}}"> {{$sub->name}} </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($subject_id)
                                <div class="col-7 m-0 p-0 mb-2">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le prof de {{$subject->name}} </label>
                                    <select wire:model="teacher_id" class="px-2 form-select custom-select text-white z-bg-secondary w-100" >
                                        <option value="{{null}}">Choisissez le prof</option>
                                        @foreach($teachers as $t)
                                            @if($t->speciality()->id == $subject->id)
                                                <option @if(!$t->teaching) disabled title="Cet enseignant n'est plus en fonction sur la plateforme. Veuillez le réinsérer dans les paramètres avant de pouvoir le choisir" @endif value="{{$t->id}}"> {{$t->getFormatedName()}} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <span class="text-white-50 mt-4"> Veuillez sélectionner une matière puis choisir le prof ensuite </span>
                            @endif
                        </div>
                   </div>
                </div>
            </div>
            @if(count($dataToShow) > 0)
                <div class="p-0 m-0 mx-auto  pb-1 pt-1 mt-3">
                    <hr class="w-100 p-0 m-0 bg-orange">
                    <h6 style="letter-spacing: 1.3px;" class="text-white-50 text-center">Voici les choix que vous avez définis. Une fois terminée, clicker sur Terminer!</h6>
                     <hr class="w-100 p-0 m-0 mb-2 bg-orange">

                    <div class="d-flex justify-content-between flex-column w-100 mx-auto ">
                        @foreach($dataToShow as $sub_id => $datum)
                           <span class="d-flex justify-content-start w-auto shadow my-2">
                                <span class="fa fa-check text-warning mt-t mr-2"></span>
                                <span class="text-white-50 mr-2">
                                    Prof de 
                                    <span class="text-warning">{{$datum['subject']}}</span> : 
                                </span>
                                <span class="text-white-50"> Mr/Mme <span class="text-orange">{{$datum['teacher']}}</span> </span>

                                <span wire:click="retrieveFromData({{$sub_id}})" title="Retirer cette donnée" class="fa fa-trash text-danger ml-3 mt-1 fx-20 cursor-pointer"></span>

                            </span>
                        @endforeach

                    </div>
                </div>
            @endif
        @else

            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                <col>
                <col>
                <col>
                <colgroup span="2"></colgroup>
                <col>
                <tr class="text-center z-bg-secondary-dark ">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Matière </th>
                    <th colspan="2"scope="colgroup">Enseignants</th>
                    <th rowspan="2">Actions</th>
                </tr>
                <tr>
                    <th scope="col">Ancien</th>
                    <th scope="col">Nouveau</th>
                </tr>


                @foreach($dataToShow as $sb_id => $dt)
                <tr class="text-left text-center">
                    <th scope="row" class="text-center py-2">{{ $loop->iteration }}</th>
                    <th scope="row" class="text-center">
                        <span class="text-warning">{{$dt['subject']}}</span>
                    </th>
                    <th class="text-capitalize text-center pl-2 p-0 m-0">

                        @php 
                            $old_teacher = $classe->getClasseCurrentTeachers(false, $sb_id);
                        @endphp

                        @if($old_teacher)
                            <span title="Ancien prof de {{$dt['subject']}} de la classe de {{$classe->name}}" class="text-white-50"> 
                                Mr/Mme 
                                <span class="text-white-50">{{$old_teacher->getFormatedName()}}</span> 
                            </span>
                        @else
                            Non défini
                        @endif
                    </th>
                    <th>
                        <span title="Nouveau prof (si validé) de {{$dt['subject']}} de la classe de {{$classe->name}}" class="text-white-50"> Mr/Mme <span class="text-orange">{{$dt['teacher']}}</span> </span>
                    </th>
                    <th>
                        <span class="d-flex justify-content-between">
                            <span wire:click="retrieveFromData({{$sb_id}})" title="Retirer cette donnée et garder l'ancien prof" class="fa fa-trash text-danger ml-3 mt-1 fx-20 cursor-pointer"></span>
                            
                            <span wire:click="edit" title="Editer cette donnée" class="fa fa-edit text-primary ml-3 mt-1 fx-20 cursor-pointer"></span>
                        </span>
                    </th>
                </tr>
            @endforeach
            </table>

        @endif

        <div class="p-0 m-0 mx-auto w-100 pb-1 pt-1 d-flex justify-content-center mt-2">
            <span title="Annuler le processus et conserver les anciennes données" wire:click="cancel" class="btn btn-secondary border border-white px-3 w-50">
                <span class="fa fa-recycle"></span>
                <span>Annuler</span>
            </span>
        </div>
    </form>
    @endif
</x-z-modal-generator>