<div>
    @if($has_school)
        <div class="w-100 d-flex">
            <h5 class="mt-2 mr-1">Année-Scolaire :</h5>
            <form action="">
                {{csrf_field()}}
                <select wire:model="school_year_selected" class="form-select custom-select">
                    <option disabled value="{{null}}">Sélectionner une année scolaire</option>
                    @foreach($school_years as $school_year)
                        <option value="{{$school_year->school_year}}"> {{ $school_year->school_year }} </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif
</div>
