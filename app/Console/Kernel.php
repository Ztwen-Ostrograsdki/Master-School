<?php

namespace App\Console;

use App\Models\Mark;
use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\UpdatePupilsMarksBatches;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('model:prune', [

            '--model' => [Mark::class, RelatedMark::class],

        ])->sundays();

        $schedule->command('model:prune', [

            '--model' => [Pupil::class],

        ])->sundays();

        $schedule->command('model:prune', [

            '--model' => [UpdatePupilsMarksBatches::class],

        ])->sundays();

        // $schedule->command('queue:prune-batches --hours=48 --unfinished=72')->daily();

        // $schedule->command('queue:prune-batches --hours=48 --cancelled=72')->daily();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
