<?php

namespace App\Jobs;

use App\Events\FileTransferedToLocalDiskEvent;
use App\Models\TransferFile;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class JobTransferLocalFileToDatabase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TransferFile $file)
    {
        $this->file = $file;
    }



    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $file = $this->file->storeAs($this->file->imagesFolder, $this->getName());

        FileTransferedToLocalDiskEvent::dispatch($this->file);
    }


    public function getName()
    {
        return $this->file->name;
    }



}
