<?php

namespace App\Providers;

use App\Events\AbsencesAndLatesDeleterEvent;
use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\ClasseRefereesManagerEvent;
use App\Events\CompletedClasseCreationEvent;
use App\Events\DeletePupilsFromDataBaseEvent;
use App\Events\DetachPupilsFromSchoolYearEvent;
use App\Events\DispatchIrregularsSemestrialMarksToConcernedTeachersEvent;
use App\Events\DispatchIrregularsTeachersAndPupilsOnSemestrialMarksEvent;
use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\FreshAveragesIntoDBEvent;
use App\Events\ImportRegistredTeachersToTheCurrentYearEvent;
use App\Events\InitiateClasseParticipationMarksEvent;
use App\Events\InitiateClassePupilsDataUpdatingFromFileEvent;
use App\Events\InitiateClassePupilsMatriculeUpdateEvent;
use App\Events\InitiateClassePupilsNamesUpdateEvent;
use App\Events\InitiateMarksStoppingEvent;
use App\Events\InitiateSettingsOnMarksEvent;
use App\Events\JoinParentToPupilNowEvent;
use App\Events\LocalTransfertCreatedEvent;
use App\Events\MakeClassePresenceLateEvent;
use App\Events\MarksNullActionsEvent;
use App\Events\MarksRestorationEvent;
use App\Events\MarksStoppingDispatchingEvent;
use App\Events\MigrateDataToTheNewSchoolYearEvent;
use App\Events\NewJobStartEvent;
use App\Events\NewProductCreatedEvent;
use App\Events\ParentRequestToFollowPupilEvent;
use App\Events\PaymentSystemEvent;
use App\Events\PrepareClasseMarksExcelFileDataInsertionToDatabaseEvent;
use App\Events\PreparePupilDataToFetchEvent;
use App\Events\PrepareUserDeletingEvent;
use App\Events\PreparingToCreateNewTeacherEvent;
use App\Events\PupilAbandonnedClassesEvent;
use App\Events\ReloadClassesPromotionAndPositionEvent;
use App\Events\StartNewsPupilsInsertionEvent;
use App\Events\ThrowClasseMarksConvertionEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Events\UpdateClasseMarksToSimpleExcelFileEvent;
use App\Events\UpdateClasseSanctionsEvent;
use App\Events\UpdateSchoolModelEvent;
use App\Events\UserAccountBlockedEvent;
use App\Events\UserConnectedEvent;
use App\Events\UserTryingToUpdatePupilMarkEvent;
use App\Listeners\AbsencesAndLatesDeleterBatcherListener;
use App\Listeners\AdminGetNotificationAboutTheUserWhoTryingToUpdatePupilMarkListener;
use App\Listeners\BlockedOrUnblockedUserAccountListener;
use App\Listeners\ClasseMarksConverterBatcherListener;
use App\Listeners\ClasseMarksDeletionBatcherListener;
use App\Listeners\ClasseMarksInsertionBatchListener;
use App\Listeners\ClassePupilsNamesUpdatingListener;
use App\Listeners\ClasseRefereesManagerBatcherListener;
use App\Listeners\CompletedClasseCreationBatcherListener;
use App\Listeners\CreateNewTeacherListener;
use App\Listeners\CreatedTransferBatchListener;
use App\Listeners\DataMigrationToTheNewSchoolYearBatcherListener;
use App\Listeners\DeletePupilsFromDataBaseListener;
use App\Listeners\DeleteUserListener;
use App\Listeners\DetachPupilsFromSchoolYearBatcherListener;
use App\Listeners\DispatchIrregularsSemestrialMarksToConcernedTeachersListener;
use App\Listeners\DispatchIrregularsTeachersAndPupilsOnSemestrialMarksListener;
use App\Listeners\DispatchingMarksStoppedListener;
use App\Listeners\FlushAveragesIntoDataBaseBatcherListener;
use App\Listeners\FreshAveragesIntoDBBatcherListener;
use App\Listeners\ImportRegistredTeachersToTheCurrentYearBatcherListener;
use App\Listeners\InitMarksStoppingListener;
use App\Listeners\InitiateSettingsOnMarksBatcherListener;
use App\Listeners\InsertClasseMarksExcelFileDataToDatabaseListener;
use App\Listeners\JoinParentToPupilNowListener;
use App\Listeners\MakeClassePresenceLateBatcherListener;
use App\Listeners\MarksNullActionsBatcherListener;
use App\Listeners\MarksRestorationBatcherListener;
use App\Listeners\NewProductCreatedListener;
use App\Listeners\ParentRequestToFollowPupilListener;
use App\Listeners\PaymentSystemListener;
use App\Listeners\PreparePupilDataToFetchListener;
use App\Listeners\ProcessingNewsPupilsInsertionBatcherListener;
use App\Listeners\PupilAbandonnedClassesListener;
use App\Listeners\ReloadClassesPromotionAndPositionBatcherListener;
use App\Listeners\UpdateClasseAveragesIntoDatabaseBatcherListener;
use App\Listeners\UpdateClasseMarksToSimpleExcelFileListener;
use App\Listeners\UpdateClasseParticipationMarksListener;
use App\Listeners\UpdateClassePupilsDataFromFileListener;
use App\Listeners\UpdateClasseSanctionsListener;
use App\Listeners\UpdateSchoolModelListener;
use App\Listeners\UpdatingClassePupilsMatriculeListener;
use App\Listeners\UserConnectedListener;
use App\Models\Administrator;
use App\Models\Classe;
use App\Models\ClasseMarksExcelFile;
use App\Models\ClassesSecurity;
use App\Models\LockedUsersRequest;
use App\Models\Mark;
use App\Models\MarkActionHistory;
use App\Models\ParentRequestToFollowPupil;
use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\User;
use App\Models\UserAdminKey;
use App\Observers\AdministratorObserver;
use App\Observers\ClasseMarksExcelFileObserver;
use App\Observers\ClasseObserver;
use App\Observers\ClassesSecurityObserver;
use App\Observers\LockedUsersRequestObserver;
use App\Observers\MarkArchivesObserver;
use App\Observers\MarkObserver;
use App\Observers\ParentRequestToFollowPupilObserver;
use App\Observers\PupilObserver;
use App\Observers\RelatedMarkObserver;
use App\Observers\UserAdminKeyObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
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

        UserConnectedEvent::class => [
            UserConnectedListener::class,
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

        DeletePupilsFromDataBaseEvent::class => [
            DeletePupilsFromDataBaseListener::class,
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

        InitiateClassePupilsDataUpdatingFromFileEvent::class => [
            UpdateClassePupilsDataFromFileListener::class,
        ],

        InitiateClasseParticipationMarksEvent::class => [
            UpdateClasseParticipationMarksListener::class,
        ],

        ParentRequestToFollowPupilEvent::class => [
            ParentRequestToFollowPupilListener::class,
        ],

        JoinParentToPupilNowEvent::class => [
            JoinParentToPupilNowListener::class,
        ],

        UpdateClasseMarksToSimpleExcelFileEvent::class => [
            UpdateClasseMarksToSimpleExcelFileListener::class,
        ],

        PrepareClasseMarksExcelFileDataInsertionToDatabaseEvent::class => [
            InsertClasseMarksExcelFileDataToDatabaseListener::class,
        ],

        UserTryingToUpdatePupilMarkEvent::class => [
            AdminGetNotificationAboutTheUserWhoTryingToUpdatePupilMarkListener::class,
        ],

        PreparingToCreateNewTeacherEvent::class => [
            CreateNewTeacherListener::class,
        ], 

        PrepareUserDeletingEvent::class => [
            DeleteUserListener::class,
        ],
        
        InitiateMarksStoppingEvent::class => [
            InitMarksStoppingListener::class,
        ],

        MarksStoppingDispatchingEvent::class => [
            DispatchingMarksStoppedListener::class,
        ],

        DispatchIrregularsTeachersAndPupilsOnSemestrialMarksEvent::class => [
            DispatchIrregularsTeachersAndPupilsOnSemestrialMarksListener::class,
        ],

        // DispatchIrregularsSemestrialMarksToConcernedTeachersEvent::class => [
        //     DispatchIrregularsSemestrialMarksToConcernedTeachersListener::class,
        // ],

    
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

        LockedUsersRequest::observe(LockedUsersRequestObserver::class);

        ParentRequestToFollowPupil::observe(ParentRequestToFollowPupilObserver::class);

        ClasseMarksExcelFile::observe(ClasseMarksExcelFileObserver::class);
        
        MarkActionHistory::observe(MarkArchivesObserver::class);


        // Queue::before(function(JobProcessing $event){

        //     NewJobStartEvent::dispatch("event");

        // });

        // Queue::after(function(JobProcessed $event){



        // });

        Queue::looping(function(){

            while (DB::transactionLevel() > 0){

                DB::rollBack();

            }


        });

    }

}
