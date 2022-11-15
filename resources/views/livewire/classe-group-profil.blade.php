<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Informations Générales de la
                    <span class="text-warning">
                        {{ $classe_group ? ' Promotion ' . $classe_group->name : "" }}
                    </span>

                </h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="fa fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Effectif</span>
                                    <span class="info-box-number"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-primary">
                                <span class="info-box-icon"><i class="fa fa-user-nurse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les Notes</span>
                                    <span class="info-box-number">90 000</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                  <span class="info-box-text">Scolarités</span>
                                  <span class="info-box-number">92 050</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-danger">
                                <span class="info-box-icon"><i class="fa fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Emploi du temps</span>
                                    <span class="info-box-number">114 381</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if($classe_group)
    <div class="my-1 mt-3 px-2">
        <h6 class="m-0 p-0 py-1 rounded text-white-50 shadow border border-secondary d-flex justify-content-between">
            <span class="pt-2 pl-2">
                Listes des groupes pédagogiques de la promotion <span class="text-warning">{{ $classe_group->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}}
            </span>

            <span class="justify-content-between">
                <span wire:click="deleteAllRelatedMarks" title="Ajouter une note relative : Sanction ou Bonus" class="float-right btn btn-primary mr-2 border">
                    <span class="bi-trash text-orange"></span>
                    <span class="ml-1">Vider</span>
                </span>
                <span wire:click="addNewsClassesToThisClasseGroup({{$classe_group->id}})" title="Ajouter un groupe pédagogique à la cette promotion de {{$classe_group->name}}" class="float-right btn btn-success mr-2 border">
                    <span class="ml-1 text-dark">
                        <span class="fa fa-plus"></span>
                        <span class="bi-filter"></span>
                    </span>
                </span>

            </span>
        </h6>
    </div>
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
    @endif
</div>
