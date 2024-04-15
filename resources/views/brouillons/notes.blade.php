@if(!isset($data[$pupil->id]))
    @if(!$targeted_pupil)
        <input autofocus="autofocus" placeholder="Matricule de l'apprenant {{$pupil->getName()}}" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('ltpk_matricule') text-danger border-primary @enderror" wire:model.defer="ltpk_matricule" type="text" name="ltpk_matricule">
        @error('ltpk_matricule')
            <small class="py-1 z-text-orange">{{$message}}</small>
        @enderror
    @else
        <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition du matricule de {{$pupil->getName()}} en cours ..." type="text">

    @endif
@else
    @if($targeted_pupil && $targeted_pupil == $pupil->id)
        <input autofocus="autofocus" placeholder="Matricule de l'apprenant {{$pupil->getName()}}" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('ltpk_matricule') text-danger border-primary @enderror" wire:model.defer="ltpk_matricule" type="text" name="ltpk_matricule">
        @error('ltpk_matricule')
            <small class="py-1 z-text-orange">{{$message}}</small>
        @enderror
    @else
        <input disabled class="text-success form-control bg-transparent border border-white px-2 text-cursive" placeholder="Edition du matricule de l'apprenant {{$pupil->getName()}} en cours..." type="text">

    @endif
@endif

<tr class="@isset($data[$pupil->id]) bg-secondary-light-2 @endisset @if($targeted_pupil && $targeted_pupil !== $pupil->id) opacity-50  @else opacity-100 @endif">
                                        <th class="px-2">{{$loop->iteration}}</th>
                                        <th class="text-left pl-2">{{$pupil->getName()}}</th>
                                        <th>
                                            

                                        </th>
                                        

                                    </tr>