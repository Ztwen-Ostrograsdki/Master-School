<?php

namespace App\Providers;

use App\Events\AbsencesAndLatesDeleterEvent;
use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\ClasseRefereesManagerEvent;
use App\Events\CompletedClasseCreationEvent;
use App\Events\DetachPupilsFromSchoolYearEvent;
use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\FreshAveragesIntoDBEvent;
use App\Events\ImportRegistredTeachersToTheCurrentYearEvent;
use App\Events\InitiateClassePupilsMatriculeUpdateEvent;
use App\Events\InitiateClassePupilsNamesUpdateEvent;
use App\Events\InitiateSettingsOnMarksEvent;
use App\Events\LocalTransfertCreatedEvent;
use App\Events\MakeClassePresenceLateEvent;
use App\Events\MarksNullActionsEvent;
use App\Events\MarksRestorationEvent;
use App\Events\MigrateDataToTheNewSchoolYearEvent;
use App\Events\NewProductCreatedEvent;
use App\Events\PaymentSystemEvent;
use App\Events\PreparePupilDataToFetchEvent;
use App\Events\PupilAbandonnedClassesEvent;
use App\Events\ReloadClassesPromotionAndPositionEvent;
use App\Events\StartNewsPupilsInsertionEvent;
use App\Events\ThrowClasseMarksConvertionEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Events\UpdateClasseSanctionsEvent;
use App\Events\UpdateSchoolModelEvent;
use App\Events\UserAccountBlockedEvent;
use App\Listeners\AbsencesAndLatesDeleterBatcherListener;
use App\Listeners\BlockedOrUnblockedUserAccountListener;
use App\Listeners\ClasseMarksConverterBatcherListener;
use App\Listeners\ClasseMarksDeletionBatcherListener;
use App\Listeners\ClasseMarksInsertionBatchListener;
use App\Listeners\ClassePupilsNamesUpdatingListener;
use App\Listeners\ClasseRefereesManagerBatcherListener;
use App\Listeners\CompletedClasseCreationBatcherListener;
use App\Listeners\CreatedTransferBatchListener;
use App\Listeners\DataMigrationToTheNewSchoolYearBatcherListener;
use App\Listeners\DetachPupilsFromSchoolYearBatcherListener;
use App\Listeners\FlushAveragesIntoDataBaseBatcherListener;
use App\Listeners\FreshAveragesIntoDBBatcherListener;
use App\Listeners\ImportRegistredTeachersToTheCurrentYearBatcherListener;
use App\Listeners\InitiateSettingsOnMarksBatcherListener;
use App\Listeners\MakeClassePresenceLateBatcherListener;
use App\Listeners\MarksNullActionsBatcherListener;
use App\Listeners\MarksRestorationBatcherListener;
use App\Listeners\NewProductCreatedListener;
use App\Listeners\PaymentSystemListener;
use App\Listeners\PreparePupilDataToFetchListener;
use App\Listeners\ProcessingNewsPupilsInsertionBatcherListener;
use App\Listeners\PupilAbandonnedClassesListener;
use App\Listeners\ReloadClassesPromotionAndPositionBatcherListener;
use App\Listeners\UpdateClasseAveragesIntoDatabaseBatcherListener;
use App\Listeners\UpdateClasseSanctionsListener;
use App\Listeners\UpdateSchoolModelListener;
use App\Listeners\UpdatingClassePupilsMatriculeListener;
use App\Models\Administrator;
use App\Models\Classe;
use App\Models\ClassesSecurity;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\User;
use App\Models\UserAdminKey;
use App\Observers\AdministratorObserver;
use App\Observers\ClasseObserver;
use App\Observers\ClassesSecurityObserver;
use App\Observers\MarkObserver;
use App\Observers\PupilObserver;
use App\Observers\RelatedMarkObserver;
use App\Observers\UserAdminKeyObserver;
use App\Observers\UserObserver;
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

        MigrateDataToTheNewSchoolYearEvent::class => [
            DataMigrationToTheNewSchoolYearBatcherListener::class,
        ],

        ImportRegistredTeachersToTheCurrentYearEvent::class => [
            ImportRegistredTeachersToTheCurrentYearBatcherListener::class,
        ],
        
        StartNewsPupilsInsertionEvent::class => [
            ProcessingNewsPupilsInsertionBatcherListener::class,
        ],

        DetachPupilsFromSchoolYearEvent::class => [
            DetachPupilsFromSchoolYearBatcherListener::class,
        ],

        MakeClassePresenceLateEvent::class => [
            MakeClassePresenceLateBatcherListener::class,
        ],

        AbsencesAndLatesDeleterEvent::class => [
            AbsencesAndLatesDeleterBatcherListener::class,
        ],

        UpdateClasseSanctionsEvent::class => [
            UpdateClasseSanctionsListener::class,
        ],

        MarksNullActionsEvent::class => [
            MarksNullActionsBatcherListener::class,
        ],

        ClasseRefereesManagerEvent::class => [
            ClasseRefereesManagerBatcherListener::class,
        ],


        ThrowClasseMarksConvertionEvent::class => [
            ClasseMarksConverterBatcherListener::class,
        ], 

        CompletedClasseCreationEvent::class => [
            CompletedClasseCreationBatcherListener::class,
        ],

        ReloadClassesPromotionAndPositionEvent::class => [
            ReloadClassesPromotionAndPositionBatcherListener::class,
        ],

        UserAccountBlockedEvent::class => [
            BlockedOrUnblockedUserAccountListener::class,
        ],

        PupilAbandonnedClassesEvent::class => [
            PupilAbandonnedClassesListener::class,
        ], 

        PreparePupilDataToFetchEvent::class => [
            PreparePupilDataToFetchListener::class,
        ],

        UpdateSchoolModelEvent::class => [
            UpdateSchoolModelListener::class,
        ],

        InitiateClassePupilsMatriculeUpdateEvent::class => [
            UpdatingClassePupilsMatriculeListener::class,
        ],

        InitiateClassePupilsNamesUpdateEvent::class => [
            ClassePupilsNamesUpdatingListener::class,
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

        RelatedMark::observe(RelatedMarkObserver::class);

        UserAdminKey::observe(UserAdminKeyObserver::class);

        Administrator::observe(AdministratorObserver::class);

        User::observe(UserObserver::class);

    }

}
