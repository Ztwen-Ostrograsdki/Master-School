<div>
    <div class="m-0 p-0 w-100">
        <blockquote class="text-warning py-2">
            {{ count($classes) }} Classes 
        </blockquote>
        <hr class="text-warning w-100 m-0 p-0 bg-warning">
    </div>
    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-0">
                    <span class="ml-3 mt-2">
                        <span title="Créer une nouvelle classe" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addNewClasse">
                            <span class="bi-house"></span>
                            <span>Ajouter</span>
                        </span>
                    </span>
                    <ul class="nav nav-pills ml-auto p-2">
                        <span wire:click="disjoinAll" title="Supprimer les données de toutes les classes relatives à cette année scolaire {{$school_year_model->school_year}} " class="fa bi-trash fx-25 mr-4 text-orange mt-2 cursor-pointer"></span>

                        <span wire:click="joinAll" title="Recharger toutes les classes pour cette année scolaire {{$school_year_model->school_year}} " class="fa fa-recycle fx-25 mr-4 text-info mt-2 cursor-pointer"></span>

                        <span wire:click="resetSelectedData" title="Recharger la page" class="fa fa-recycle fx-25 mr-4 text-secondary mt-2 cursor-pointer"></span>
                        
                        <li class="nav-item mx-2">
                            <select wire:model="classe_group_id" class="form-select z-bg-secondary custom-select">
                                <option value=""> Liste classes selon les promotions </option>
                                @foreach($classe_groups as $cl)
                                    <option value="{{$cl->id}}"> Promotion {{ $cl->name }} </option>
                                @endforeach
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body z-bg-secondary">
                <div class="w-100 m-0 p-0 mt-1">
                    @if(count($classes) > 0)
                       <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                            <col>
                            <col>
                            <colgroup span="3"></colgroup>
                            <col>
                            <colgroup span="2"></colgroup>
                            <col>
                            <tr class="text-center bg-secondary-light-0">
                                <td rowspan="2">No</td>
                                <td rowspan="2">Classe</td>
                                <th colspan="3" scope="colgroup">Effectif</th>
                                <th rowspan="2">Prof. Principal {{$school_year_model->school_year}} 
                                    @if($school_year_model->id !== $lastYear->id)
                                        <br class="m-0 p-0">
                                        @if($school_year_model->id !== $lastYear->id)
                                            <small class="text-orange d-block">
                                                (de l'année {{$lastYear->school_year}} )
                                            </small>
                                        @endif
                                    @endif
                                </th>
                                <th colspan="2" scope="colgroup">Les Responsables de {{$school_year_model->school_year}}

                                    @if($school_year_model->id !== $lastYear->id)
                                        <small class="text-orange d-block">
                                            (de l'année {{$lastYear->school_year}} )
                                        </small>
                                    @endif

                                </th>
                                <td rowspan="2">
                                    <span class="bi-tools"></span>
                                </td>
                            </tr>
                            <tr class="text-center z-bg-secondary-dark">
                                <th scope="col" class="">F</th>
                                <th scope="col" class="">G</th>
                                <th scope="col" class="">T</th>
                                <th scope="col" class="">Respo 1</th>
                                <th scope="col" class="">Respo 2</th>
                            </tr>
                            @foreach($classes as $k => $classe)
                                <tr class="">
                                    <td class="text-center border-right">{{ $loop->iteration }}</td>
                                    <td class="text-left px-2"> 
                                        <a class="text-white m-0 p-0 py-1" href="{{route('classe_profil', ['slug' => $classe->slug])}}">
                                            <span class="d-flex justify-content-between">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                                    {{ $classe->name }}
                                                </span>
                                            </span>
                                        </a>
                                    </td>
                                    <td class="text-center"> {{{ $classe->getEffectif('female') > 9 ? $classe->getEffectif('female') : '0' . $classe->getEffectif('female')  }}} </td> 
                                    <td class="text-center"> {{{ $classe->getEffectif('male') > 9 ? $classe->getEffectif('male') : '0' . $classe->getEffectif('male')  }}} </td> 
                                    <td class="text-center"> {{{ $classe->getEffectif() > 9 ? $classe->getEffectif() : '0' . $classe->getEffectif()  }}} </td>
                                    <td class="">
                                        @if($classe->hasPrincipal() && $classe->currentPrincipal())
                                            <span class="text-warning">
                                                <span>{{ $classe->currentPrincipal()->getFormatedName() }}</span>
                                                
                                            </span>
                                        @else
                                            <span class="text-white-50 font-italic">Non défini</span>
                                        @endif
                                        <br>

                                        <span class="text-orange small">
                                            @if($lastYear->id !== $school_year_model->id)
                                                @if($classe->hasPrincipal($lastYear->id) && $classe->currentPrincipal($lastYear->id))
                                                    <span class="">
                                                        <span>{{ $classe->currentPrincipal($lastYear->id)->getFormatedName() }}</span>
                                                        
                                                    </span>
                                                @else
                                                    <span class="font-italic">Non défini</span>
                                                @endif
                                            @endif
                                        </span>

                                    </td>
                                    <td class="">
                                        @if($classe->pupil_respo1())
                                            <span class="text-white-50">
                                                <span>{{ $classe->pupil_respo1()->getName() }}</span>
                                            </span>
                                        @else
                                            <span class="text-secondary font-italic">Non défini</span>
                                        @endif

                                        <br>

                                        <span class="text-orange small">
                                            @if($lastYear->id !== $school_year_model->id)
                                                @if($classe->pupil_respo1($lastYear->id))
                                                    <span class="">
                                                        <span>{{ $classe->pupil_respo1($lastYear->id)->getName() }}</span>
                                                    </span>
                                                @else
                                                    <span class=" font-italic">Non défini</span>
                                                @endif
                                            @endif
                                        </span>

                                    </td>
                                    <td class="">
                                        @if($classe->pupil_respo2())
                                            <span class="text-white-50">
                                                <span>{{ $classe->pupil_respo2()->getName() }}</span>
                                            </span>
                                        @else
                                            <span class="text-secondary font-italic">Non défini</span>
                                        @endif

                                        <br>

                                        <span class="text-orange small">
                                            @if($lastYear->id !== $school_year_model->id)
                                                @if($classe->pupil_respo2($lastYear->id))
                                                    <span class="">
                                                        <span>{{ $classe->pupil_respo2($lastYear->id)->getName() }}</span>
                                                    </span>
                                                @else
                                                    <span class="font-italic">Non défini</span>
                                                @endif
                                            @endif

                                        </span>
                                    </td>
                                    <td class="text-center"> 
                                        <span class="text-center w-100 m-0 p-0">
                                            @if($classe->isClasseOfThisYear())
                                                <span wire:click="disjoin({{$classe->id}})" title="Supprimer les données de cette {{$classe->name}} relatives à l'année{{ $school_year_model->school_year}}" class="text-info m-0 p-0 cursor-pointer">
                                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                                </span>

                                            @else
                                                <span wire:click="join({{$classe->id}})" title="Générer les données de cette {{$classe->name}} relatives à l'année{{ $school_year_model->school_year}}" class="text-info m-0 p-0 cursor-pointer">
                                                    <span class="text-success cursor-pointer fa fa-recycle py-2 px-2"></span>
                                                </span>

                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </table> 
                    @else
                        <div>
                            <div class="d-flex justify-content-center mx-auto w-100">
                                <span class="fa fa-trash text-muted fa-8x"></span>
                            </div>
                            <blockquote class="text-warning">
                                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                                    <span class="fa fa-heart text-danger"></span>
                                    <span class="fa fa-heart text-danger"></span>
                                    <span class="fa fa-heart text-danger"></span>
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