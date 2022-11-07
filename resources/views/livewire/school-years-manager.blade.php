<div>
    @if($has_school)
        <div class="w-100 d-flex" x-data>
            <h5 class="mt-2 mr-1">Année-Scolaire :</h5>
            <form action="">
                {{csrf_field()}}
                <select x-model="$wire.school_year_selected" x-on:change=" @this.call('changeSchoolYear');" class="form-select ">
                    <template x-for="school_year in $wire.school_years">
                        <option :selected="school_year == $wire.school_year_selected" x-bind:value="school_year" x-text="school_year"></option>
                    </template>
                </select>
            </form>
        </div>
    @endif
</div>
