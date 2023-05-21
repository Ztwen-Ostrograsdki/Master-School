<div>
    <div class="p-0 m-0">
        <div class="card container-fluid m-0 p-0 w-100 z-bg-secondary border border-dark my-1">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer text-info" data-card-widget="collapse">Veuillez renseigner les détails de la statistique à ressortir...</h5>
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
                            <div class="info-box m-0 p-0 z-bg-secondary">
                                <div class="info-box-content">
                                    <div class="d-flex justify-content-between">
                                        <form action="" class="w-100 p-2">
                                            <div class="form-group w-100 d-flex justify-content-between">
                                                <div class="w-100 d-flex justify-content-between row m-0 p-0">
                                                    <div class="col-3 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La classe </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model="classe_id" name="classe_id">
                                                            <option value="{{null}}">Sélectionner la classe </option>
                                                                @foreach ($classes as $cl)
                                                                    <option value="{{$cl->id}}">{{ $cl->name }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('classe_id')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_selected') text-danger border border-danger @enderror" wire:model="subject_selected" name="subject_selected">
                                                            <option value="{{null}}">Sélectionner la matière </option>
                                                                @foreach ($subjects as $sub)
                                                                    <option value="{{$sub->id}}">{{ $sub->name }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('subject_selected')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le semestre </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('semestre_selected') text-danger border border-danger @enderror" wire:model="semestre_selected" name="semestre_selected">
                                                            <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                                                @foreach ($semestres as $sem)
                                                                    <option value="{{$sem}}">{{$semestre_type . ' ' . $sem}}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('semestre_selected')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type d'évaluation </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('type') text-danger border border-danger @enderror" wire:model="type" name="type">
                                                            <option disabled class="" value="{{null}}">Choisissez le type</option>
                                                            @foreach ($types_of_marks as $key => $type)
                                                                <option value="{{$key}}">{{$type}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'évaluation </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('mark_index') text-danger border border-danger @enderror" wire:model="mark_index" name="mark_index">
                                                            <option disabled class="" value="{{null}}">Choisissez l'évaluation</option>
                                                            @for ($ev = 1; $ev <= $maxLenght; $ev++)
                                                                <option value="{{$ev}}">{{ 'Evaluation ' . $ev}}</option>
                                                            @endfor
                                                        </select>
                                                        @error('mark_index')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group w-100 d-flex justify-content-between">
                                                <input placeholder="Veuillez les différents intervalles de notes à rechercher ..." class="form-control bg-transparent py-1 col-9 text-white border border-white" type="text" name="intervalles" wire:model.defer="intervalles">
                                                <span wire:click="getStats" class="btn btn-primary border border-white px-2 col-2" type="submit">Valider</span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 p-0 m-0">
        <blockquote class="text-primary">
            <h5 class="m-0 h6 p-0 py-2 text-white-50">
                Etude statistique d'une évaluation de la classe de <span class="text-warning">{{$classe->name}}</span> année-scolaire {{session('school_year_selected')}} 
                <span class="float-right cursor-pointer">Filtrer et lister les apprenants
                    <span class="fa fa-filter {{ $showList ? ' text-success' : 'text-danger' }} "></span>
                </span>
            </h5>
        </blockquote>
    </div>

    <div class="p-0 m-0">
        <div class="card container-fluid m-0 p-0 w-100 z-bg-secondary border border-dark px-0 my-1">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer text-white-50" data-card-widget="collapse">Tableau des stats</h5>
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
                        <div class="card active p-0" href="#tab_2" data-toggle="tab">
                            <div class="info-box m-0 p-2 z-bg-secondary">
                                <div class="info-box-content">
                                    <div class="m-0">
                                        <div class="w-100 m-0 p-0 mt-3">
                                            <div class="w-100 p-0 m-0">
                                                <blockquote class="border-warning bg-info text-black-50">
                                                    <h5 class="m-0 h6 p-0 py-2 w-100 text-black-50 d-flex justify-content-between">
                                                        <span class="d-flex justify-content-between col-4">
                                                            @if(isset($stats['global_stats']) && isset($stats['global_stats']['effectif']) && isset($stats['global_stats']['presents']) && isset($stats['global_stats']['absents']))
                                                                @php
                                                                    $effectif = $stats['global_stats']['effectif'];
                                                                    $presents = $stats['global_stats']['presents'];
                                                                    $absents = $stats['global_stats']['absents'];
                                                                @endphp    

                                                                <span>
                                                                    Effectif: {{ $effectif > 9 ? $effectif : '0' . $effectif  }}
                                                                </span>
                                                                <span>
                                                                    Présents: {{ $presents > 9 ? $presents : '0' . $presents  }}
                                                                </span>
                                                                <span>
                                                                    Absents: {{ $absents > 9 ? $absents : '0' . $absents  }}
                                                                </span>
                                                            @else
                                                                <span>
                                                                    Effectif: {{ 'Inconnu' }}
                                                                </span>
                                                                <span>
                                                                    Présents: {{ 'Inconnu' }}
                                                                </span>
                                                                <span>
                                                                    Absents: {{ 'inconnu' }}
                                                                </span>
                                                            @endif
                                                        </span>

                                                        <span class="text-white">
                                                            Note 
                                                            <span class="text-warning">
                                                                {{ 'N°' . $mark_index . ' '}}
                                                            </span> 
                                                            des
                                                            <span class="text-black-50"> {{ $type }} </span> du
                                                            <span class="">
                                                                {{  $semestre_type . ' ' . $semestre_selected }} en
                                                            </span>
                                                            <span class="text-warning"> 
                                                                {{ $subject }}
                                                            </span>
                                                        </span>
                                                    </h5>

                                                </blockquote>
                                            </div>
                                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                                                <col>
                                                <colgroup span="2"></colgroup>
                                                <colgroup span="2"></colgroup>
                                                <colgroup span="3"></colgroup>
                                                <colgroup span="3"></colgroup>
                                                <colgroup span="3"></colgroup>
                                                <col>
                                                <tr class="text-center">
                                                    <th rowspan="2" scope="colgroup">No</th>
                                                    <th rowspan="2" scope="colgroup">Intervalles</th>
                                                    <th colspan="3" scope="colgroup">Taux de réussite en nombre</th>
                                                    <th colspan="3" scope="colgroup">Taux de réussite</th>
                                                    <th colspan="3" scope="colgroup">Moyennes de la classe</th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th scope="col">G</th>
                                                    <th scope="col">F</th>
                                                    <th scope="col">T</th>
                                                    <th scope="col">G</th>
                                                    <th scope="col">F</th>
                                                    <th scope="col">T</th>
                                                    <th scope="col">G</th>
                                                    <th scope="col">F</th>
                                                    <th scope="col">T</th>
                                                </tr>

                                                @if(isset($stats['stats']))
                                                    @foreach($stats['stats'] as $interval => $stat)
                                                        <tr class="text-left text-center">
                                                            <th scope="row" class="text-center border-right">{{ $loop->iteration }}</th>
                                                            <th class="text-capitalize text-center pl-2 p-0 m-0">
                                                                {{ $interval }}
                                                            </th>
                                                            <th> - </th>
                                                            <th> - </th>
                                                            <th> {{ $stat['total'] }} </th>
                                                            <th> - </th>
                                                            <th> - </th>
                                                            <th> {{ $stat['percentage'] }} % </th>
                                                            <th> - </th>
                                                            <th> - </th>
                                                            <th> {{ $stat['total'] }} </th>
                                                        </tr>
                                                    @endforeach

                                                @endif

                                            </table>
                                        </div>
                                        <div class="w-100 m-0 p-0 mt-3">
                                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                                                <col>
                                                <colgroup span="2"></colgroup>
                                                <colgroup span="2"></colgroup>
                                                <tr class="text-center">
                                                    <th rowspan="3" scope="colgroup">Statistiques Globales


                                                    </th>
                                                    <th colspan="2" scope="colgroup">Ayant manqué la moyenne (N<10) </th>
                                                    <th colspan="2" scope="colgroup">Ayant eu la moyenne (N>=10) </th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th scope="col">Effectif</th>
                                                    <th scope="col">Pourcentage</th>
                                                    <th scope="col">Effectif</th>
                                                    <th scope="col">Pourcentage</th>
                                                </tr>
                                                @isset($stats['global_stats'])
                                                    <tr class="text-left text-center">
                                                        <th> {{ $stats['global_stats']['failed_number'] }} </th>
                                                        <th> {{ $stats['global_stats']['failed_percentage'] }} % </th>
                                                        <th> {{ $stats['global_stats']['succeed_number'] }} </th>
                                                        <th> {{ $stats['global_stats']['succeed_percentage'] }} % </th>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                        <div class="w-100 m-0 p-0 mt-3">
                                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                                                <col>
                                                <colgroup span="2"></colgroup>
                                                <colgroup span="2"></colgroup>
                                                <tr class="text-center">
                                                    <th rowspan="3" scope="colgroup">Statistiques particulières


                                                    </th>
                                                    <th colspan="2" scope="colgroup">Meilleurs Notes </th>
                                                    <th colspan="2" scope="colgroup">Faibles Notes </th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th scope="col">Note</th>
                                                    <th scope="col">Apprenants</th>
                                                    <th scope="col">Note</th>
                                                    <th scope="col">Apprenants</th>
                                                </tr>
                                                @isset($stats['b_w_stats'])
                                                    @php
                                                        $best_mark = $stats['b_w_stats']['best_stats'];
                                                        $weak_mark = $stats['b_w_stats']['weak_stats'];
                                                    @endphp
                                                    <tr class="text-left text-center">
                                                        <th>
                                                            {{ $best_mark['mark'] > 9 ? $best_mark['mark'] : '0' . $best_mark['mark'] }} / 20
                                                        </th>
                                                        <th>
                                                            @foreach($best_mark['pupils'] as $p)
                                                                <li class="ml-1 list-unstyled text-left">{{ $p->getName() }}</li>
                                                            @endforeach
                                                        </th>

                                                        <th>
                                                            {{ $weak_mark['mark'] > 9 ? $weak_mark['mark'] : '0' . $weak_mark['mark'] }} / 20
                                                        </th>
                                                        <th>
                                                            @foreach($weak_mark['pupils'] as $p)
                                                                <li class="ml-1 list-unstyled text-left">{{ $p->getName() }}</li>
                                                            @endforeach
                                                        </th>
                                                    </tr>
                                                @endif
                                            </table>
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

    <div class="p-0 m-0 {{ !$showList ? 'd-none' : '' }} ">
        <div class="card container-fluid m-0 p-0 w-100  z-bg-secondary border border-dark px-0 my-1">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer text-primary" data-card-widget="collapse">Les apprenants selon les notes</h5>
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
                        <div class="card active p-0" href="#tab_2" data-toggle="tab">
                            <div class="info-box m-0 p-2 z-bg-secondary">
                                <div class="info-box-content">
                                    <div class="m-0">
                                        <div class="w-100 m-0 p-0 mt-3">
                                            <div class="w-100 p-0 m-0">
                                                <blockquote class="border-warning bg-success text-black-50">
                                                    <h5 class="m-0 h6 p-0 w-100 text-black-50 d-flex justify-content-between">
                                                        <span class="d-flex mt-2 justify-content-between col-4">
                                                            @if(isset($stats['global_stats']) && isset($stats['global_stats']['effectif']) && isset($stats['global_stats']['presents']) && isset($stats['global_stats']['absents']))
                                                                @php
                                                                    $effectif = $stats['global_stats']['effectif'];
                                                                    $presents = $stats['global_stats']['presents'];
                                                                    $absents = $stats['global_stats']['absents'];
                                                                @endphp    

                                                                <span>
                                                                    Effectif: {{ $effectif > 9 ? $effectif : '0' . $effectif  }}
                                                                </span>
                                                                <span>
                                                                    Présents: {{ $presents > 9 ? $presents : '0' . $presents  }}
                                                                </span>
                                                                <span>
                                                                    Absents: {{ $absents > 9 ? $absents : '0' . $absents  }}
                                                                </span>
                                                            @else
                                                                <span>
                                                                    Effectif: {{ 'Inconnu' }}
                                                                </span>
                                                                <span>
                                                                    Présents: {{ 'Inconnu' }}
                                                                </span>
                                                                <span>
                                                                    Absents: {{ 'inconnu' }}
                                                                </span>
                                                            @endif
                                                        </span>

                                                        <span class="text-white p-0">
                                                            <span wire:click="getStats" class="btn btn-primary border border-white px-2 m-0" type="submit">Recharger les données</span>
                                                        </span>
                                                    </h5>

                                                </blockquote>
                                            </div>
                                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                                                <col>
                                                <col>
                                                <col>
                                                <col>
                                                <tr class="text-center">
                                                    <th class="bg-info text-black-50" scope="col">No</th>
                                                    <th class="bg-info text-black-50" scope="col">Intervalles</th>
                                                    <th class="bg-info text-black-50" scope="col">Nombres</th>
                                                    <th class="bg-info text-black-50" scope="col">Listes</th>
                                                </tr>
                                                @if(isset($stats['stats']))
                                                    @foreach($stats['stats'] as $intv => $data)
                                                        <tr class="text-left text-center">
                                                            <th scope="row" class="text-center border-right">{{ $loop->iteration }}</th>
                                                            <th class="text-capitalize text-center px-2 p-0 m-0">
                                                                {{ $intv }}
                                                            </th>
                                                            <th class="text-capitalize text-center px-2 p-0 m-0">
                                                                {{ $data['liste']? count(explode(' || ', $data['liste'])) : ' 0 ' }}
                                                            </th>
                                                            <th class="text-capitalize text-left px-2 p-0 m-0">
                                                                <span class="">
                                                                    {{ $data['liste'] ? $data['liste'] : 'La liste est vide !!!' }}
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    @endforeach

                                                @endif

                                            </table>
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
</div>
