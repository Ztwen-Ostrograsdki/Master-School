<div class="row w-100 mx-auto mt-1 p-2">
    @if(isset($classe) && $classe)

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-0">
                    <span style="letter-spacing: 1.2px;" class="ml-3 mt-2">
                       <span class="info-box-text">Effectif : 
                            <b class="text-warning">
                                {{ $classe ? count($classe->getPupils(session('school_year_selected'))) : 'vide'}}
                            </b>
                        </span>
                        <span class="info-box-number d-flex flex-column m-0 p-0">
                            <span class="small">
                                <i class="font-italic"> Garçons </i> : 
                                <small> 
                                    {{ $classe ? count($classe->getClassePupilsOnGender('male', session('school_year_selected'))) : '00'}}
                                </small>
                            </span>

                            <span class="small">
                                <i class="font-italic"> Filles </i> : 
                                <small> 
                                    {{ $classe ? count($classe->getClassePupilsOnGender('female', session('school_year_selected'))) : '00' }}
                                </small>
                            </span>
                        </span>
                    </span>
                    <ul class="nav nav-pills ml-auto p-2">
                        <span class="text-orange mx-1">
                            @if($classe)
                                @php
                                    $cl = $classe->getNumericName();
                                @endphp
                                <span class="fa fa-3x">
                                    {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                </span>
                            @else
                                <span>Classe inconnue</span>
                            @endif
                        </span>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div>
                            <blockquote class="text-warning">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0 p-0 h6 text-white-50 py-2">
                                        Liste des apprenants de la <a class="text-warning" href="{{route('classe_profil', ['slug' => $classe->slug])}}">{{$classe->name}}</a> la plateforme <span class="text-warning"></span>
                                    </h6>
                                
                                </div>
                            </blockquote>
                        </div>
                    </div>

                    <div>

                        @livewire('classe-pupils-lister', ['classe_id' => $classe->id])

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

