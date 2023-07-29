<?php

namespace App\Observers;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\classe;
use Illuminate\Support\Carbon;

class ClasseObserver
{

    use ModelQueryTrait;
    /**
     * Handle the classe "created" event.
     *
     * @param  \App\Models\classe  $classe
     * @return void
     */
    public function created(classe $classe)
    {

    }

    /**
     * Handle the classe "updated" event.
     *
     * @param  \App\Models\classe  $classe
     * @return void
     */
    public function updated(classe $classe)
    {
        $this->doJob($classe);
    }

    /**
     * Handle the classe "deleted" event.
     *
     * @param  \App\Models\classe  $classe
     * @return void
     */
    public function deleted(classe $classe)
    {

    }

    /**
     * Handle the classe "restored" event.
     *
     * @param  \App\Models\classe  $classe
     * @return void
     */
    public function restored(classe $classe)
    {
        //
    }

    /**
     * Handle the classe "force deleted" event.
     *
     * @param  \App\Models\classe  $classe
     * @return void
     */
    public function forceDeleted(classe $classe)
    {
        //
    }


    public function doJob($classe)
    {
        $school_year_model = $this->getSchoolYear();

        $semestres = $this->getSemestres();


    }
}
