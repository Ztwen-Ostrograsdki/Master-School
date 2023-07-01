<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Effectuer une recherche...</h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
                    <div class="card-tools">
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir la liste</a>
                            
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-transparent">
                                <span class="info-box-icon"><i class="fa bi-search"></i></span>
                                <div class="info-box-content">
                                    <div class="d-flex justify-content-between">
                                        <form action="" class="col-10">
                                            <input placeholder="Veuillez saisir un mot clé à retrouver ..." class="form-control bg-transparent py-1" type="text" name="search" wire:model="search">
                                        </form>
                                        <div x-on:click="@this.call('resetSearch')" data-card-widget="collapse" class="btn-secondary rounded text-center p-1 cursor-pointer border border-white col-2">
                                            <span>Annuler</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                Liste des classes enregistrés sur la plateforme <span class="text-warning"></span>

                <span class="float-right text-muted"> </span>
            </h6>
        </blockquote>
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
                        <span wire:click="resetSelectedData" title="Recharger toutes les matières" class="fa fa-recycle fx-25 mr-4 text-info mt-2 cursor-pointer"></span>
                        <li class="nav-item ml-2">
                            <select wire:model="level_id" class="form-select z-bg-secondary custom-select">
                                <option value=""> Liste classes selon le cycle </option>
                                @foreach($levels as $lev)
                                    <option value="{{$lev->id}}"> {{ $lev->getName() }} </option>
                                @endforeach
                            </select>
                        </li>

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
                            <tr class="text-center z-bg-secondary-dark">
                                <td rowspan="2">No</td>
                                <td rowspan="2">Classe</td>
                                <th colspan="3" scope="colgroup">Effectif</th>
                                <th rowspan="2">Prof. Principal</th>
                                <th colspan="2" scope="colgroup">Les Responsables</th>
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
                                        <a class="text-white m-0 p-0 py-1" href="#">
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
                                                <span wire:click="editClasseReferees({{$classe->id}})" title="Retirer l'AE"class="text-danger ml-3 cursor-pointer">
                                                    <span class="text-danger cursor-pointer fa fa-trash fx-20 py-2 px-2"></span>
                                                </span>
                                            </span>
                                        @else
                                            <span class="text-white-50 font-italic">Non défini</span>
                                        @endif

                                        <span wire:click="editClasseReferees({{$classe->id}})" title="Définir/Changer le PP" class="text-info float-right text-right m-0 p-0 cursor-pointer">
                                                <span class="text-info cursor-pointer fa fa-edit py-2 px-2"></span>
                                            </span>

                                    </td>
                                    <td class="">
                                        @if($classe->pupil_respo1())
                                            <span class="text-white-50">
                                                <span>{{ $classe->pupil_respo1()->getName() }}</span>
                                                <span wire:click="editClasseReferees({{$classe->id}})" title="Retirer l'AE"class="text-danger ml-3 cursor-pointer">
                                                    <span class="text-danger cursor-pointer fa fa-trash fx-20 py-2 px-2"></span>
                                                </span>
                                            </span>
                                        @else
                                            <span class="text-secondary font-italic">Non défini</span>
                                        @endif

                                        <span wire:click="editClasseReferees({{$classe->id}})" title="Définir/Changer le premier responsable" class="text-info float-right text-right m-0 p-0 cursor-pointer">
                                            <span class="text-info cursor-pointer fa fa-edit py-2 px-2"></span>
                                        </span>

                                    </td>
                                    <td class="">
                                        @if($classe->pupil_respo2())
                                            <span class="text-white-50">
                                                <span>{{ $classe->pupil_respo2()->getName() }}</span>
                                                <span wire:click="editClasseReferees({{$classe->id}})" title="Retirer l'AE"class="text-danger ml-3 cursor-pointer">
                                                    <span class="text-danger cursor-pointer fa fa-trash fx-20 py-2 px-2"></span>
                                                </span>
                                            </span>
                                        @else
                                            <span class="text-secondary font-italic">Non défini</span>
                                        @endif

                                        <span wire:click="editClasseReferees({{$classe->id}})" title="Définir/Changer le second responsable" class="text-info float-right text-right m-0 p-0 cursor-pointer">
                                            <span class="text-info cursor-pointer fa fa-edit py-2 px-2"></span>
                                        </span>

                                    </td>
                                    <td class="text-center"> 
                                        <span class="row w-100 m-0 p-0">
                                            <span title="Supprimer cette classe" wire:click="deleteClasse({{$classe->id}})" class="text-danger col-2 m-0 p-0 cursor-pointer">
                                                <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                            </span>
                                            <span title="Fermer cette classe" class="text-warning col-2 border-right border-left m-0 p-0 cursor-pointer">
                                                <span class="text-warning cursor-pointer fa fa-lock py-2 px-2"></span>
                                            </span>
                                            <span title="Vérouiller mise à jour des notes de cette classe" class="text-warning col-2 m-0 p-0 cursor-pointer">
                                                <span class="text-warning cursor-pointer fa fa-pen text-orange py-2 px-2"></span>
                                            </span>
                                            <span title="Bloquer Profs de cette classe" class="text-warning col-2 border-right border-left m-0 p-0 cursor-pointer">
                                                <span class="text-danger cursor-pointer fa bi-person-x py-2 px-2"></span>
                                            </span>
                                            <span title="Réinitialiser cette classe" class="text-info col-2 m-0 p-0 cursor-pointer border-right">
                                                <span class="text-info cursor-pointer fa fa-recycle py-2 px-2"></span>
                                            </span>
                                            <span wire:click="updateName({{$classe->id}})" title="Editer cette classe" class="text-primary col-2 m-0 p-0 cursor-pointer">
                                                <span class="text-primary cursor-pointer fa fa-edit py-2 px-2"></span>
                                            </span>
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