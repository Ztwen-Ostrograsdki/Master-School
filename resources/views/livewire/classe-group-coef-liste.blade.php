<div class="w-100 m-0 p-0 mt-3 px-2">
    @if(count($classe_group->classes))
        <table class="w-100 m-0 p-0 table-bordered z-table text-white text-center">
            <col>
            <colgroup span="{{count($classe_group->classes)}}"></colgroup>
            <colgroup span="2"></colgroup>
            <tr>
                <td rowspan="2">Matières</td>
                <th scope="colgroup"  colspan="{{count($classe_group->classes)}}">Les Groupes Pédagogiques</th>
                <th scope="colgroup" colspan="2" rowspan="2">Actions</th>
            </tr>
            <tr>
                @foreach($classe_group->classes as $classe)
                    <th scope="col">
                        <a title="charger le profil de la classe de {{$classe->name}}" class="text-white d-inline-block w-100" href="{{route('classe_profil', ['slug' => $classe->slug])}}">
                            <span>{{$classe->getNumericName()['root']}}</span><sup>{{$classe->getNumericName()['sup']}}</sup>
                            <span>{{$classe->getNumericName()['idc']}}</span>
                        </a>
                    </th>
                @endforeach
            </tr>
            @foreach($classe_group->subjects as $subject)
                <tr>
                    <th scope="row">{{ $subject->name }}</th>
                    @foreach($classe_group->classes as $c)
                        <td title="Cliquer pour éditer">
                            @if($classe_group->getCoef($subject->id))
                                <span  class="cursor-pointer w-100 d-inline-block" wire:click="editClasseGroupCoeficient({{$classe_group->id}}, {{$subject->id}}, {{$classe_group->getCoef($subject->id)->id}})">
                                    {{ $classe_group->getCoef($subject->id)->coef }}
                                </span>
                            @else
                                <span class="cursor-pointer w-100 d-inline-block"  wire:click="editClasseGroupCoeficient({{$classe_group->id}}, {{$subject->id}}, {{null}})"> - </span>
                            @endif
                        </td>
                    @endforeach
                    <td>
                        <span class="bi-trash text-orange cursor-pointer d-inline-block w-100"></span>
                    </td>
                    <td>
                        <span class="bi-lock text-warning cursor-pointer d-inline-block w-100"></span>
                    </td>
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
