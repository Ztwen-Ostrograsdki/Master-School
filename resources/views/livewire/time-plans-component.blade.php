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
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir</a>
                            
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
                                            <input placeholder="Veuillez entrer une classe ou une promotoion ..." class="form-control bg-transparent py-1" type="text" name="search" wire:model="search">
                                        </form>
                                        <div wire:click="resetSearch" data-card-widget="collapse" class="btn-secondary rounded text-center p-1 cursor-pointer border border-white col-2">
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

    <div class="card">
        <div class="card-header d-flex justify-content-between p-0">
            <span class="ml-3 mt-2">
                <span title="Insérer un nouvel emploi de temps" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addTimePlan">
                    <span class="bi-download"></span>
                    <span>Ajouter</span>
                </span>
            </span>
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item">
                    <select wire:change="changeSection('level')" wire:model="level_id_selected" class="form-select z-bg-secondary custom-select">
                        <option value="{{null}}"> Le Cycle </option>
                        @foreach($levels as $lev)
                            <option value="{{$lev->id}}"> {{ $lev->getName() }} </option>
                        @endforeach
                    </select>
                </li>

                <li class="nav-item mx-2">
                    <select wire:change="changeSection('classe')" wire:model="classe_id_selected" class="form-select z-bg-secondary custom-select">
                        <option value="{{null}}"> Les classes </option>
                        @foreach($classes as $cl)
                            <option value="{{$cl->id}}"> {{ $cl->name }} </option>
                        @endforeach
                    </select>
                </li>

                <li class="nav-item">
                    <select wire:change="changeSection('classe_group')" wire:model="classe_group_id_selected" class="form-select z-bg-secondary custom-select">
                        <option value="{{null}}"> Les Promotions </option>
                        @foreach($classe_groups as $cg)
                            <option value="{{$cg->id}}"> {{ $cg->name }} </option>
                        @endforeach
                    </select>
                </li>
            </ul>
        </div>
    </div>

    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                EMPLOI DU TEMPS DE L'ANNEE SCOLAIRE  <span class="text-warning"> {{ session('school_year_selected') ? session('school_year_selected') : 'En cours...' }}</span>
                <span class="float-right text-muted"> </span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3 px-1">
        @if($classesToShow && count($classesToShow) > 0)
            <div class="w-100 m-0 p-0 mt-3 px-1 py-2" style="overflow-x: auto;">
                <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">
                    <col>
                    @foreach($classesToShow as $cl0)
                        <colgroup span="{{5}}"></colgroup>
                    @endforeach
                    <colgroup span="3"></colgroup>
                    <tr class="text-center z-bg-secondary">
                        <th rowspan="2" scope="colgroup">Les classes</th>
                        @foreach($classesToShow as $cl1)
                        <th colspan="{{5}}" scope="colgroup">{{$cl1->name}}</th>
                        @endforeach
                    </tr>
                    @foreach($classesToShow as $cl2)
                        <th scope="col" class="z-bg-secondary-dark">L</th>
                        <th scope="col" class="z-bg-secondary-dark">M</th>
                        <th scope="col" class="z-bg-secondary-dark">M</th>
                        <th scope="col" class="z-bg-secondary-dark">J</th>
                        <th scope="col" class="z-bg-secondary-dark">V</th>
                    @endforeach
                    @foreach($morning_times1 as $hm1)
                        <tr class="text-left">
                            <th class=" text-capitalize pl-2 p-0 m-0 z-bg-secondary-light-opac text-dark">
                                {{ $hm1 }}
                            </th>
                                {{-- LES Programmes --}}
                            @foreach($classesToShow as $cl3)
                                @for ($dys=1;$dys<=5;$dys++)
                                    <td class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> {{ $randomSubjectsTab[rand(0, count($randomSubjectsTab) - 1)] }} </small>
                                    </td>
                                @endfor
                            @endforeach
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
                            <i class="text-warning small">Aucune classe n'a trouvé! Aucune donnée n'est peut-être disponible</i>
                        </span>
                    </blockquote>
                </div>
            </div>
        @endif                                                                                 
    </div>
</div>
