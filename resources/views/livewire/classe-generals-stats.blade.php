<div>
    <div class="w-100 my-1">

    </div>
    <div class="my-2">
        @if($classe && $subjects && count($subjects) > 0)
        <div>
            <blockquote class="text-primary">
                <h5 class="m-0 p-0 text-white-50 h6 w-100 d-flex justify-content-between">
                    <span>Statisitiques générales de la classe de la {{ $classe->name }}</span>
                    <span class="d-flex justify-content-between">
                        @if($classe && $classe->classe_group)
                            <a title="charger le profil de la promotion" class="text-success mx-1" href="{{route('classe_group_profil', ['slug' => $classe->classe_group->name])}}">
                                Promotion {{ $classe->classe_group->name }}
                            </a>
                        @else
                            <span wire:click="editClasseGroup({{$classe->id}})" title="Cette classe n'est pas encore liée à une promotion, veuillez cliquer afin de le faire et d'avoir accès aux coéfiscients des différentes matières" class="mx-1 p-0 px-2 btn btn-success border border-white">
                                Promouvoir maintenant
                            </span>
                        @endif
                    </span>
                </h5>
            </blockquote>
            {{-- {{dd($stats);}} --}}
            <div class="w-100 m-0 p-0 mt-3">
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                    <col>
                    <colgroup span="2"></colgroup>
                    <colgroup span="2"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="3"></colgroup>
                    <col>
                    <tr class="text-center">
                        <th rowspan="2" scope="colgroup">No</th>
                        <th rowspan="2" scope="colgroup">Les matières</th>
                        <th colspan="3" scope="colgroup">Effectif actif</th>
                        <th colspan="2" scope="colgroup">Plus fortes moy</th>
                        <th colspan="2" scope="colgroup">Plus faibles moy</th>
                        <th colspan="3" scope="colgroup">Nbre de réussite</th>
                        <th colspan="3" scope="colgroup">Taux de réussite (%)</th>
                        <th colspan="3" scope="colgroup">Moyennes de la classe</th>
                        <th rowspan="2" scope="colgroup">
                            <span class="bi-tools"></span>
                        </th>
                    </tr>
                    <tr class="text-center">
                        <th scope="col">G</th>
                        <th scope="col">F</th>
                        <th scope="col">T</th>
                        <th scope="col">G</th>
                        <th scope="col">F</th>
                        <th scope="col">G</th>
                        <th scope="col">F</th>
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
                    @foreach($stats as $subject_id => $subject_stats)
                        <tr class="text-left text-center">
                            <th scope="row" class="text-center border-right">{{ $loop->iteration }}</th>
                            <th class="text-capitalize text-left pl-2 p-0 m-0">
                                {{ $subject_stats['subject_name'] }}
                            </th>
                            @if(isset($subject_stats) && $subject_stats)
                                @if($subject_stats['effectif']['T'] > 0)

                                    {{-- Efectif ayant de notes --}}
                                    <th scope="row"> 
                                        {{ $subject_stats['effectif'] ? $subject_stats['effectif']['G'] : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['effectif'] ? $subject_stats['effectif']['F'] : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['effectif'] ? $subject_stats['effectif']['T'] : ' - '}}  
                                    </th>

                                    {{-- fortes moyennes --}}
                                    <th scope="row"> 
                                        {{ $subject_stats['bestBoy'] ? $subject_stats['bestBoy']['moy'] : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['bestGirl'] ? $subject_stats['bestGirl']['moy'] : ' - '}}  
                                    </th>
                                    {{-- faibles moyennes --}}
                                    <th scope="row"> 
                                        {{ $subject_stats['weakBoy'] ? $subject_stats['weakBoy']['moy'] : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['weakGirl'] ? $subject_stats['weakBoy']['moy'] : ' - '}}  
                                    </th>
                                    {{-- stats en nombre--}}
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['G']['number']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['F']['number']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['T']['number']) : ' - '}}  
                                    </th>
                                    {{-- stats en %--}}
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['G']['percentage']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['F']['percentage']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['stats'] ? ($subject_stats['stats']['succeed']['T']['percentage']) : ' - '}}  
                                    </th>

                                    {{-- Moyenne de la classe --}}
                                    <th scope="row"> 
                                        {{ $subject_stats['moyenne'] ? ($subject_stats['moyenne']['boy_moy']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['moyenne'] ? ($subject_stats['moyenne']['girl_moy']) : ' - '}}  
                                    </th>
                                    <th scope="row"> 
                                        {{ $subject_stats['moyenne'] ? ($subject_stats['moyenne']['classe_moy']) : ' - '}}  
                                    </th>
                                    <th scope="row"> - </th>
                                @else
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>
                                    <th scope="row"> - </th>

                                @endif
                            @else
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                                <th scope="row"> - </th>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @endif
        @if($subjects == [] || $stats == [])
        <div class="my-2 p-2 text-center border rounded">
            <h6 class="mx-auto p-3">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ session('classe_selected') }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                pour le <span class="text-warning">{{ $semestre_type . ' ' . session('semestre_selected')}}</span>

                <blockquote class="text-info">
                    Veuillez sectionner un autre {{ $semestre_type }} ou une autre année scolaire
                </blockquote>
            </h6>
        </div>
        @endif
    </div>

</div>
