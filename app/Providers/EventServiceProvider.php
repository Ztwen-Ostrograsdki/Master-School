<?php

namespace App\Providers;

use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\FreshAveragesIntoDBEvent;
use App\Events\InitiateSettingsOnMarksEvent;
use App\Events\LocalTransfertCreatedEvent;
use App\Events\MarksRestorationEvent;
use App\Events\NewProductCreatedEvent;
use App\Events\PaymentSystemEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Listeners\ClasseMarksDeletionBatcherListener;
use App\Listeners\ClasseMarksInsertionBatchListener;
use App\Listeners\CreatedTransferBatchListener;
use App\Listeners\FlushAveragesIntoDataBaseBatcherListener;
use App\Listeners\FreshAveragesIntoDBBatcherListener;
use App\Listeners\InitiateSettingsOnMarksBatcherListener;
use App\Listeners\MarksRestorationBatcherListener;
use App\Listeners\NewProductCreatedListener;
use App\Listeners\PaymentSystemListener;
use App\Listeners\UpdateClasseAveragesIntoDatabaseBatcherListener;
use App\Models\Classe;
use App\Models\ClassesSecurity;
use App\Models\Mark;
use App\Models\Pupil;
use App\Observers\ClasseObserver;
use App\Observers\ClassesSecurityObserver;
use App\Observers\MarkObserver;
use App\Observers\PupilObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        NewProductCreatedEvent::class => [
            NewProductCreatedListener::class,
        ],

        PaymentSystemEvent::class => [
            PaymentSystemListener::class,
        ],

        LocalTransfertCreatedEvent::class => [
            CreatedTransferBatchListener::class,
        ],

        ClasseMarksInsertionCreatedEvent::class => [
            ClasseMarksInsertionBatchListener::class,
        ],

        UpdateClasseAveragesIntoDatabaseEvent::class => [
            UpdateClasseAveragesIntoDatabaseBatcherListener::class,
        ],

        ClasseMarksDeletionCreatedEvent::class => [
            ClasseMarksDeletionBatcherListener::class,
        ],

        FlushAveragesIntoDataBaseEvent::class => [
            FlushAveragesIntoDataBaseBatcherListener::class,
        ],

        FreshAveragesIntoDBEvent::class => [
            FreshAveragesIntoDBBatcherListener::class,
        ],
        
        InitiateSettingsOnMarksEvent::class => [
            InitiateSettingsOnMarksBatcherListener::class,
        ],

        MarksRestorationEvent::class => [
            MarksRestorationBatcherListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Pupil::observe(PupilObserver::class);

        ClassesSecurity::observe(ClassesSecurityObserver::class);

        Mark::observe(MarkObserver::class);

        Classe::observe(ClasseObserver::class);

    }

}
