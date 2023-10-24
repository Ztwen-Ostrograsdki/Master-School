<?php

namespace App\Observers;

use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\RelatedMark;

class RelatedMarkObserver
{
    use ModelQueryTrait;
    /**
     * Handle the RelatedMark "created" event.
     *
     * @param  \App\Models\RelatedMark  $relatedMark
     * @return void
     */
    public function created(RelatedMark $relatedMark)
    {
        //
    }

    /**
     * Handle the RelatedMark "updated" event.
     *
     * @param  \App\Models\RelatedMark  $relatedMark
     * @return void
     */
    public function updated(RelatedMark $relatedMark)
    {
        $this->doJob($relatedMark);
    }

    /**
     * Handle the RelatedMark "deleted" event.
     *
     * @param  \App\Models\RelatedMark  $relatedMark
     * @return void
     */
    public function deleted(RelatedMark $relatedMark)
    {
        $this->doJob($relatedMark);
    }

    /**
     * Handle the RelatedMark "restored" event.
     *
     * @param  \App\Models\RelatedMark  $relatedMark
     * @return void
     */
    public function restored(RelatedMark $relatedMark)
    {
        $this->doJob($relatedMark);
    }

    /**
     * Handle the RelatedMark "force deleted" event.
     *
     * @param  \App\Models\RelatedMark  $relatedMark
     * @return void
     */
    public function forceDeleted(RelatedMark $relatedMark)
    {
        $this->doJob($relatedMark);
    }


    public function doJob($relatedMark)
    {

        $school_year_model = $this->getSchoolYear();

        $semestre = $relatedMark->semestre;

        $classe = $relatedMark->classe;

        if($classe && $semestre){

            $user = auth()->user();

            FlushAveragesIntoDataBaseEvent::dispatch($user, $classe, $school_year_model, $semestre);

        }

    }
}
