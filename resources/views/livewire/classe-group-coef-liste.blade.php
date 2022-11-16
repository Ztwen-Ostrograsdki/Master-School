<div class="w-100 m-0 p-0 mt-3 px-2">
    @if(count($classe_group->classes))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <col>
            <colgroup span="1"></colgroup>
            <colgroup span="1"></colgroup>
            <colgroup span="{{count($classe_group->subjects)}}"></colgroup>
            <colgroup span="{{count($classe_group->classes)}}"></colgroup>
            <colgroup span="2"></colgroup>
            <tr class="text-center">
                <th rowspan="2" scope="colgroup">No</th>
                <th rowspan="{{count($classe_group->subjects)}}" scope="colgroup">Les matières</th>
                <th colspan="{{count($classe_group->classes)}}" scope="colgroup">Les classes</th>
                <th rowspan="2" scope="colgroup">Action</th>
            </tr>
            <tr class="text-center">
                @foreach ($classe_group->classes as $classe)
                    <th scope="row" >
                        <span>{{$classe->getNumericName()['root']}}</span>
                        <sup>{{$classe->getNumericName()['sup']}}</sup>
                        <span>{{$classe->getNumericName()['idc']}}</span>
                    </th>
                @endforeach
                
                @foreach($classe_group->subjects as $subject)
                    <tr class="text-left">
                        <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                        <th scope="row" class="text-center">{{ $subject->name }}</th>
                        @foreach($classe_group->classes as $classe)
                            <th class="text-center border-right">
                                @if($classe_group->getCoef($subject->id))
                                    <span  class="cursor-pointer w-100 d-inline-block" wire:click="editClasseGroupCoeficient({{$classe_group->id}}, {{$subject->id}}, {{$classe_group->getCoef($subject->id)->id}})">
                                        {{ $classe_group->getCoef($subject->id)->coef }}
                                    </span>
                                @else
                                    <span class="cursor-pointer w-100 d-inline-block"  wire:click="editClasseGroupCoeficient({{$classe_group->id}}, {{$subject->id}}, {{null}})"> - </span>
                                @endif
                            </th>
                        @endforeach
                        <th class="text-center">
                            <span class="fa bi-refresh text-success"></span>
                        </th>
                    </tr>
                @endforeach
                
            </tr>
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
