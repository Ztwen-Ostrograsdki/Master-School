<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            
            <div class="card-header bg-dark my-2"> 
                <h5 style="letter-spacing: 1.2px;" class="card-title cursor-pointer text-uppercase text-cursive text-orange" data-card-widget="collapse">Détails sur les épreuves de composition déjà envoyées</h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                  <div class="card-tools">
                    
                    <ul class="nav nav-pills ml-auto position-relative" style="right: 100px;">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" wire:click="resetLates" tabindex="-1" href="#">Tout approuver</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Tous réfuser</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Vérouiller les épreuves</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Signaler les retardataires</a>
                            <a class="dropdown-item" wire:click="createNewClasse" tabindex="-1" href="#">Fermer la réception des épreuves</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="m-0 p-0 px-2 d-flex">
                            <form class="d-inline" action="">
                                @csrf()
                                <select id="semestre_selected" wire:model="semestre_selected" class="form-select custom-select border border-warning">
                                  <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                    @foreach ($semestres as $semestre)
                                        <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form class="d-inline mx-2 " action="">
                                @csrf()
                                <select style="letter-spacing: 1.2px;" wire:model="section_selected" class="form-select text-uppercase font-weight-bold border border-orange custom-select ml-3">
                                  <option value="{{null}}">Veuillez sélectionner une section</option>

                                </select>
                            </form>
                        </div>

                    <div class="mx-auto w-100 my-2 p-2">
                        <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">

                            <col>
                            <col>
                            <col>
                            <colgroup span="2">
                            <col>
                            <col>
                            <col>
                            <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-3">
                                <th rowspan="2" class="py-2">No</th>
                                <th rowspan="2">Matières / Atélier</th>
                                <th rowspan="2">Prof. chargé (AE)</th>
                                <th colspan="2" >Epreuves</th>
                                <th rowspan="2">Date d'envoie</th>
                                <th rowspan="2">Statut</th>
                                <th rowspan="2">Action</th>
                            </tr>
                            <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-2">
                                <th scope="colgroup">Attendues</th>
                                <th scope="colgroup">Réçues</th>
                            </tr>
                            @foreach($subjects as $subject)

                                <tr>
                                    <th>{{$loop->iteration}}</th>
                                    <th class="py-2 pl-2 text-left">{{$subject->name}}</th>
                                    <th> - </th>
                                    <th> - </th>
                                    <th> - </th>
                                    <th> - </th>
                                    <th> 
                                        <span class="btn btn-success w-100">
                                            <span class="fa fa-check"></span>
                                            <span class="">Envoyé</span>
                                        </span>
                                    </th>
                                    <th>
                                        <span class="btn btn-danger w-100">
                                            <span class="fa fa-trash"></span>
                                            <span class="">Réfusé</span>
                                        </span>
                                    </th>
                                </tr>
                            @endforeach
                            <tr class="bg-secondary-light-0 fx-20 font-italic" style="letter-spacing: 1.2px;">
                                <th colspan="3" class="py-3"> Total </th>
                                <th> 12 </th>
                                <th> 10 </th>
                                <td> - </td>
                                <td> - </td>
                                <td> - </td>

                            </tr>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
