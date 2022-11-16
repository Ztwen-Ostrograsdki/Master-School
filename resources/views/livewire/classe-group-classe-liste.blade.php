<div class="w-100 m-0 p-0 mt-3 px-2">
    @if(count($classe_group->classes))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <col>
                <colgroup span="1"></colgroup>
                <colgroup span="1"></colgroup>
                <colgroup span="3"></colgroup>
                <colgroup span="3"></colgroup>
                <colgroup span="4"></colgroup>
                <tr class="text-center">
                    <th rowspan="2" scope="colgroup">No</th>
                    <th rowspan="2" class="text-capitalize" scope="colgroup">Les Groupes pédagogiques</th>
                    <th colspan="3" scope="colgroup">Effectif</th>
                    <th colspan="4" scope="colgroup">Actions</th>
                </tr>
                <tr class="text-center">
                    <th scope="col">F</th>
                    <th scope="col">G</th>
                    <th scope="col">T</th>
                    
                    <th scope="col">Suppr</th>
                    <th scope="col">Update</th>
                    <th scope="col">Fermer</th>
                    <th scope="col">Vider</th>
                </tr>

                @foreach($classe_group->classes as $classe)
                    <tr class="">
                        <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                        <th scope="row" class="text-left pl-2">
                            <a title="charger le profil de la classe de {{ $classe->name }}" class="text-white w-100 d-inline-block" href="{{route('classe_profil', ['slug' => $classe->slug])}}">
                                {{ $classe->name }}
                            </a>
                        </th>
                        <th scope="row" class="text-center">{{ count($classe->getClassePupilsOnGender('female', (session('school_year_selected')))) }}</th>
                        <th scope="row" class="text-center">{{ count($classe->getClassePupilsOnGender('male', (session('school_year_selected')))) }}</th>
                        <th scope="row" class="text-center">{{ count($classe->getPupils((session('school_year_selected')))) }}</th>
                        <th scope="row" class="text-center cursor-pointer">
                            <span wire:click="removeClasseFromThisGroup({{$classe->id}})" title="Retirer la classe de {{$classe->name}} définitivement de ce groupe ou de cette promotion" class="col-4 m-0 p-0 cursor-pointer">
                                <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                            </span>
                        </th>
                        <th scope="row" class="text-center cursor-pointer">
                            <span title="Mettre à jour les données de la classe de {{$classe->name}}" class="w-100 m-0 p-0 cursor-pointer">
                                <span class="text-success fa fa-upload py-2 px-2"></span>
                            </span>
                        </th>
                        <th scope="row" class="text-center cursor-pointer">
                            <span title="Fermer la classe de {{$classe->name}}" class="col-4 m-0 p-0 cursor-pointer">
                                <span class="text-orange cursor-pointer fa fa-lock py-2 px-2"></span>
                            </span>
                        </th>
                        <th scope="row" class="text-center cursor-pointer">
                            <span title="Vider la classe de {{$classe->name}}" class="col-4 m-0 p-0 cursor-pointer">
                                <span class="text-warning cursor-pointer fa bi-trash py-2 px-2"></span>
                            </span>
                        </th>
                    </tr>
                @endforeach
        </table>            
    @else
        <div>
            <blockquote class="">
                <h6 class="h6 text-white-50">
                    La liste des groupes pédagogiques de <span class="text-warning">{{ $classe_group->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}} est viège. <br>
                    
                </h6>
            </blockquote>
        </div>
    @endif                                         
</div>
