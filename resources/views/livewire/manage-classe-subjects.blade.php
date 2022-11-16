<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Définition des matières'" :width="6" :icon="'fa fa-filter'" :modalName="'classeSubjectManageModal'" :modalBodyTitle="$title">
<form autocomplete="off" class="form-group pb-3 px-2 bg-transparent">
    @if($onClasseGroup)
        @if($classe_group)

        @endif
    @else
        @if($classe)

        @endif
    @endif

    <div class="row justify-between">
        <div class="mt-0 col-11 mx-auto">
            <div class="card w-100">
                <div class="card-body p-0 m-0 w-100">
                    <div class="tab-content w-100">
                        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                            <thead class="text-white text-center">
                                <th class="py-2 text-center">#ID</th>
                                <th class="">Matières</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($subjects as $k => $subject)
                                    <tr class="">
                                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                                        <td class="text-capitalize pl-2">
                                            {{$subject->name}}
                                            @if(in_array($subject->id, $subjects_selecteds))
                                                <small class="bi-check-all mx-2 text-success"></small>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!in_array($subject->id, $subjects_selecteds))
                                                <span wire:click="join({{$subject->id}})" class="cursor-pointer w-100 py-1 d-inline-block btn btn-primary">
                                                    <span>Ajouter</span>
                                                    <span class="fa fa-plus"></span>
                                                </span>
                                            @else
                                                <span wire:click="disjoin({{$subject->id}})" class="cursor-pointer w-100 py-1 d-inline-block btn btn-warning">
                                                    <span>Retirer</span>
                                                    <span class="fa fa-minus"></span>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center row m-0 p-0 w-100">
        <blockquote class="text-info col-10 row">
            <h6 class="m-0 p-0 h6 py-1 text-white-50 col-12 shadow d-flex justify-content-between">
                @if(count($subjects_selecteds) > 0)
                    <span class="pl-2">
                       {{count($subjects_selecteds) > 9 ? count($subjects_selecteds) : '0'.count($subjects_selecteds)}} matière(s) liée(s)!
                    </span>
                @else
                    <span class="pl-2">
                       Aucune matière encore liée!
                    </span>
                @endif
            </h6>
        </blockquote>
    </div>
    <div class="p-0 m-0 mx-auto d-flex justify-content-center row pb-1 pt-1 mt-2">
        <span wire:click="hideForm" class="cursor-pointer col-7 btn btn-primary p-2 border border-white">
            Terminer
        </span>
    </div>
</form>
</x-z-modal-generator>
