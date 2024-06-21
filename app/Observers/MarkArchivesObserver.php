<?php

namespace App\Observers;

use App\Events\NewMarkHistoryArchivedEvent;
use App\Models\MarkActionHistory;

class MarkArchivesObserver
{
    /**
     * Handle the MarkActionHistory "created" event.
     *
     * @param  \App\Models\MarkActionHistory  $markActionHistory
     * @return void
     */
    public function created(MarkActionHistory $markActionHistory)
    {
        NewMarkHistoryArchivedEvent::dispatch($markActionHistory);
    }

    /**
     * Handle the MarkActionHistory "updated" event.
     *
     * @param  \App\Models\MarkActionHistory  $markActionHistory
     * @return void
     */
    public function updated(MarkActionHistory $markActionHistory)
    {
        //
    }

    /**
     * Handle the MarkActionHistory "deleted" event.
     *
     * @param  \App\Models\MarkActionHistory  $markActionHistory
     * @return void
     */
    public function deleted(MarkActionHistory $markActionHistory)
    {
        //
    }

    /**
     * Handle the MarkActionHistory "restored" event.
     *
     * @param  \App\Models\MarkActionHistory  $markActionHistory
     * @return void
     */
    public function restored(MarkActionHistory $markActionHistory)
    {
        //
    }

    /**
     * Handle the MarkActionHistory "force deleted" event.
     *
     * @param  \App\Models\MarkActionHistory  $markActionHistory
     * @return void
     */
    public function forceDeleted(MarkActionHistory $markActionHistory)
    {
        //
    }
}
