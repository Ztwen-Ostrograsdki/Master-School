<?php

namespace App\Observers;

use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobForceMarksDestroyingAfterMoreDays;
use App\Jobs\JobToArchiveMarkAction;
use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use App\Jobs\UpdateAverageTable;
use App\Models\Mark;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MarkObserver
{
    /**
     * Handle the Mark "created" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function created(Mark $mark)
    {
        // $this->doJob($mark);
    }

    /**
     * Handle the Mark "updated" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function updated(Mark $mark)
    {
        $targets = ['subject_id', 'classe_id', 'semestre', 'type', 'authorized', 'forget', 'forced_mark'
        ];

        if($mark->isDirty($targets)){

            $this->doJob($mark);

        }

        // $mark->value > 0 && $mark->isDirty() ? $this->doJob($mark) : $this->doNotJob();
    }

    public function updating(Mark $mark)
    {

        $archives_targets = ['subject_id', 'classe_id', 'semestre', 'type'];

        if($mark->isDirty($targets)){

            $this->doJob($mark);

        }

        if($mark->isDirty($archives_targets)){

            $action = "updated";

            $user = auth_user();

            $description = "La note (" . get_mark_type($mark->type) . ") N°" . $mark->mark_index . " de " . $mark->pupil->getName() . " en " . $mark->subject->name . " a été éditée par " . $user->pseudo . "(Email: " . $user->email . ")." ;

            if($mark->isDirty('value')){

                $description .= " La valeur de la note est passée de " . $mark->getOriginal('value') . " à " . $mark->value; 

            }

            if($mark->isDirty('type')){

                $description .= " Le type de la note est passé de " . get_mark_type($mark->getOriginal('type')) . " à " . get_mark_type($mark->type); 

            }

            dispatch(new JobToArchiveMarkAction("deleted", $description, $mark, $user));

        }

        // $mark->value > 0 && $mark->isDirty() ? $this->doJob($mark) : $this->doNotJob();
    }


    /**
     * Handle the Mark "deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function deleted(Mark $mark)
    {
        if($mark->type !== 'participation'){

            $mark->value > 0 && $mark->isDirty() ? $this->doJob($mark) : $this->doNotJob();

        }

    }


    public function deleting(Mark $mark)
    {

        dispatch(new JobForceMarksDestroyingAfterMoreDays($mark))->delay(Carbon::now()->addDays(30));

        $action = "deleted";

        $user = Auth::user();

        $description = "La note (" . get_mark_type($mark->type) . ") N°" . $mark->mark_index . " de " . $mark->pupil->getName() . " en " . $mark->subject->name . " a été supprimée par " . $user->pseudo . "(Email: " . $user->email . ")." ;

        dispatch(new JobToArchiveMarkAction("deleted", $description, $mark, $user));

    }

    /**
     * Handle the Mark "restored" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function restored(Mark $mark)
    {
        // $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }

    /**
     * Handle the Mark "force deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function forceDeleted(Mark $mark)
    {
        // $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }


    public function doJob($mark)
    {

        $school_year_model = $mark->school_year;

        $semestre = $mark->semestre;

        $classe = $mark->classe;

        if($classe && $semestre){

            $user = $mark->user;

            FlushAveragesIntoDataBaseEvent::dispatch($user, $classe, $school_year_model, $semestre);

        }

    }

    public function doNotJob()
    {
        //DO ANYTHINK
    }
}
