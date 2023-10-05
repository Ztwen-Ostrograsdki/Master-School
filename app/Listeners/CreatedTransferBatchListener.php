<?php

namespace App\Listeners;

use App\Events\FileTransferToDiskCompletedEvent;
use App\Events\LocalTransfertCreatedEvent;
use App\Jobs\JobTransferLocalFileToDatabase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class CreatedTransferBatchListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LocalTransfertCreatedEvent $event)
    {
        $transfer = $event->getTransfer();

        $jobs = $transfer->files->mapInto(JobTransferLocalFileToDatabase::class);

        $batch = Bus::batch($jobs)
                    ->finally(function() use ($transfer){
                        FileTransferToDiskCompletedEvent::dispatch($transfer);
                    })->dispatch();

        $event->getTransfer()->update(['batch_id' => $batch->id]);
    }
}
