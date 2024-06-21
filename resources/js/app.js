require('intersection-observer');
IntersectionObserver.prototype.POLL_INTERVAL = 100;
require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


import Echo from 'laravel-echo';

var e = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001'
});

window.ClientUser = {
    id: 0,
};

if (window.User) {
    window.ClientUser = window.User;
}

e.private('user.' + window.ClientUser.id)
    .listen('UserLeavingChannelEvent', function(e) {

        Livewire.emit('UserLeavingChannelLiveEvent', e);
    })
    .listen('DispatchIrregularsSemestrialMarksToConcernedTeachersEvent', function(e) {

        classe = e.classe;

        Swal.fire({
            icon: 'success',
            title: "Vous avez reçu une notification et un couriel",
            text: "Il semble que certains de cos apprenants de la classe de " + classe.name + " n'ont pas le nombre de notes minimal requis!",
            toast: true,
            showConfirmButton: true,
        });
    })
    .listen('UserJoiningChannelEvent', function(e) {


        Livewire.emit('UserJoiningChannelLiveEvent', e);
    })
    .listen('PupilDataAreReadyToFetchEvent', function(e) {


        Livewire.emit('DataAreReadyToFetchLiveEvent', e);
    })
    .listen('ParentRequestAcceptedEvent', function(e) {

        Swal.fire({
            icon: 'success',
            title: "Demande Acceptée",
            text: " Votre compte parent est désormais actif et accessible",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('NotifyMeWhenMyRequestAccepted', e.user);
    })
    .listen('PupilSetToOrFromAbandonnedEvent', function(e) {

        Livewire.emit('ReloadClasseListDataAbandonLiveEvent');

    })
    .listen('ForcingUserDisconnectionEvent', function(e) {

        Swal.fire({
            icon: 'warning',
            title: "PROCEDURE INEVITABLE: RUPTURE DE CONNEXION",
            text: " Vous allez être déconnecté dans quelques secondes!",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('RedirectoLoginPage');

    })
    .listen('UserAdminSessionKeyExpiredEvent', function(e) {

        window.location.reload();

    })
    .listen('UserRetrievedFromAdminEvent', function(e) {

        Swal.fire({
            icon: 'warning',
            title: "Status Admin retiré",
            text: " Vous n'avez plus le status administrateur",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('ReloadComponentEvent');

        window.location.reload();

    })
    .listen('UserExtendsToAdminEvent', function(e) {

        Swal.fire({
            icon: 'info',
            title: "Status Admin attribué",
            text: " Vous avez reçu le status administrateur",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('ReloadComponentEvent');

        // window.location.reload(); 

    })
    .listen('InitiateClasseDataUpdatingEvent', function(e) {
        
        Livewire.emit('InitiateClasseDataUpdatingLiveEvent');
    }) 
    .listen('ParentAccountBlockedEvent', function(e) {
        
        Livewire.emit('UpdateParentAccountAfterBlocked', e.user);
    }) 
    .listen('TeacherFileWasSentWithSuccessEvent', function(e) {
        
        Livewire.emit('FileWasSendWithSuccess', e.user);
    })
    .listen('ParentAccountDeletedEvent', function(e) {

        window.location.reload();

    })
    .listen('ClasseMarksWasFailedEvent', function(e) {

        Swal.fire({
            icon: 'error',
            title: "ERREURE DE TRAITEMENT!",
            text: "Une erreure est survenue lors du traitement!",
            toast: true,
            showConfirmButton: true,
        });
    })
    .listen('DataMigratedToTheNewSchoolYearEvent', function(e) {

        Livewire.emit('DataMigratedToTheNewSchoolYearLiveEvent');
    })
    .listen('TeachersDataUploadingEvent', function(e) {
        
        Livewire.emit('InitiateTeachersDataUploadingLiveEvent');
    })
    .listen('TeachersToTheCurrentYearCompletedEvent', function(e) {

        Livewire.emit('OldsTeachersUploadingCompletedLiveEvent');
    }) 
    .listen('ClassePupilsListUpdatingEvent', function(e) {
        
        Livewire.emit('ClassePupilsListUpdatingLiveEvent');
    }) 
    .listen('ClassePupilsListUpdatedEvent', function(e) {
        
        Livewire.emit('ClassePupilsListUpdatedLiveEvent');
    }) 
    .listen('ClassePupilsMatriculeUpdatedEvent', function(e) {

        Livewire.emit('ClasseDataWasUpdated');
        
        Livewire.emit('ClassePupilsListUpdatedLiveEvent');
    }) 
    .listen('ClassePupilsNamesUpdatedEvent', function(e) {

        Livewire.emit('ClasseDataWasUpdated');
        
        Livewire.emit('ClassePupilsListUpdatedLiveEvent');
    }) 
    .listen('ClassePupilsDataWasUpdatedFromFileEvent', function(e) {

        Livewire.emit('ClasseDataWasUpdated');
        
        Livewire.emit('ClassePupilsListUpdatedLiveEvent');
    }) 
    .listen('ClassePresenceLateWasCompletedEvent', function(e) {
        
        Livewire.emit('PresenceLateWasUpdated');
    }) 
    .listen('ClasseDataWasUpdateSuccessfullyEvent', function(e) {
        
        Livewire.emit('ClasseDataWasUpdated');
    })
    
    .listen('MyParentRequestToFollowPupilCreatedEvent', function(e) {
        
        Livewire.emit('MyParentRequestToFollowPupilCreatedLiveEvent');
    }) 
    .listen('DispatchDetailsAboutMyParentsRequestsEvent', function(e) {

        console.log(e);
        
        Livewire.emit('AboutMyParentRequestsLiveEvent');
    })  
    .listen('ClasseMarksToSimpleExcelFileCompletedEvent', function(e) {

        Livewire.emit('ClasseMarksToSimpleExcelFileCompletedLiveEvent', e.file_name);
    }) 
    .listen('ClasseExcelsFilesWasUpdatedEvent', function(e) {

        Livewire.emit('ClasseExcelsFilesWasUpdatedLiveEvent');
    }) 
    .listen('UpdatePupilsMarksInsertionProgressEvent', function(e) {

        Livewire.emit('UpdatePupilsMarksInsertionProgressLiveEvent');
    }) 
    .listen('PupilsMarksUpdatingFailedEvent', function(e) {

        Livewire.emit('PupilsMarksUpdatingFailedLiveEvent', e.data);
    }) 
    .listen('NewTeacherWasCreatedEvent', function(e) {

        Livewire.emit('NewTeacherWasCreatedLiveEvent');
    })
     

e.private('master')
    .listen('MarksStoppingDispatchedEvent', function(e){

        Livewire.emit('MarksStoppingDispatchedLiveEvent');

        Swal.fire({
            icon: 'success',
            title: "LA CLOTURE EST TERMINEE",
            text: "La cloture de l'année scolaire ou du semestre s'est bien déroulée!",
            toast: true,
            showConfirmButton: true,
        });

    })

    .listen('DispatchingMarksStoppingFailedEvent', function(e){

        Livewire.emit('DispatchingMarksStoppingFailedLiveEvent');

        Swal.fire({
            icon: 'error',
            title: "LA CLOTURE A ECHOUE",
            text: "La cloture de l'année scolaire ou du semestre a échoué!",
            toast: true,
            showConfirmButton: true,
        });

    })

    .listen('PupilDetachingFailedEvent', function(e) {

        console.log(e);

        Swal.fire({
            icon: 'error',
            title: "DETACHEMENT ECHOUE",
            text: "Le détachement des apprenants de l'année scolaire a échoué",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('RefreshLockedRequestListLiveEvent');
    })
    .listen('PupilDeletionFailedEvent', function(e) {

        console.log(e);
        
        Swal.fire({
            icon: 'error',
            title: "SUPPRESSION ECHOUE",
            text: "La suppression des apprenants a échoué",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('RefreshLockedRequestListLiveEvent');
    })
    .listen('PupilDetachingOrDeletionCompletedEvent', function(e) {

        console.log(e);
        
        Swal.fire({
            icon: 'success',
            title: "SUPPRESSION - DETACHEMENT TERMINE AVEC SUCCES",
            text: "La suppression/détachement des apprenants s'est bien déroulée",
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('RefreshLockedRequestListLiveEvent');
    })
    .listen('DispatchTransactionsCommitedEvent', function(e) {

        console.log(e);

        // Livewire.emit('RefreshLockedRequestListLiveEvent');
    })
    .listen('RefreshLockedRequestListEvent', function(e) {

        Livewire.emit('RefreshLockedRequestListLiveEvent');
    })
    .listen('UserSentLockedRequestEvent', function(e) {

        Livewire.emit('UserSentLockedRequestLiveEvent');
    })
    .listen('NewAddParentRequestEvent', function(e) {

        Livewire.emit('NewParentRequest');
    })
    .listen('NewEpreuveWasUploadedEvent', function(e) {

        Livewire.emit('NewEpreuveWasUploadedLiveEvent');
    })
    .listen('ParentRequestToFollowPupilCreatedSuccessfullyEvent', function(e) {

        Livewire.emit('NewParentRequestToFollowPupilLiveEvent');

    })
    .listen('ParentRequestToFollowPupilWasDeletedEvent', function(e) {

        Livewire.emit('UpdateParentRequestsListLiveEvent');

    })
    .listen('ParentHaveBeenJoinedToPupilEvent', function(e) {

        Livewire.emit('ParentHaveBeenJoinedToPupilLiveEvent');

    })
    .listen('NewUserCreatedEvent', function(e) {
        
        Livewire.emit('NewUserCreatedLiveEvent');
    }) 
    .listen('UserWasDeletedEvent', function(e) {
        
        Livewire.emit('UserWasDeletedLiveEvent');
    }) 
    .listen('UserDeletionFailedEvent', function(e) {

        console.log(e);

        Swal.fire({
            icon: 'error',
            title: "SUPPRESSION ECHOUE",
            text: "La suppression de l'utilisateur a échoué",
            toast: true,
            showConfirmButton: true,
        });
        
        Livewire.emit('UserDeletionFailedLiveEvent');
    }) 
    .listen('UserConfirmedEmailEvent', function(e) {
        
        Livewire.emit('UserConfirmedEmailLiveEvent');
    }) 
    .listen('UpdatePupilsMarksUpdatingRequestsEvent', function(e) {

        Swal.fire({
            icon: 'info',
            title: "Un prof a tenté de modifier la note d'un apprenant",
            toast: true,
            showConfirmButton: true,
        });
        
        Livewire.emit('UpdatePupilsMarksUpdatingRequestsLiveEvent');
    }) 

    .listen('ClasseMarksWasFailedEvent', function(e) {

        Swal.fire({
            icon: 'error',
            title: "Le traitement des notes a échoué",
            toast: true,
            showConfirmButton: true,
        });
    })
    .listen('NewJobStartEvent', function(e) {

        console.log(e);
    })
    .listen('NewTeacherWasCreatedEvent', function(e) {

        Livewire.emit('NewTeacherWasCreatedLiveEvent');
    })
    .listen('TeacherCreatingOrUpdatingFailedEvent', function(e) {

        Swal.fire({
            icon: 'error',
            title: "ERREURE CREATION - EDITION ENSEIGNANT",
            text: e.error_message,
            toast: true,
            showConfirmButton: true,
        });

        Livewire.emit('TeacherCreatingOrUpdatingFailedLiveEvent');
    })







e.private('reloadMarkChannel.' + window.ClientUser.id)


e.private('users')
    .listen('MarksStoppingDispatchedEvent', function(e){

        Livewire.emit('MarksStoppingDispatchedLiveEvent');

    });


e.private('reloader.' + window.ClientUser.id)

    .listen('ClasseMarksWasUpdatedIntoDBSuccessfullyEvent', function(e) {

        Livewire.emit('ClasseDataLoadedSuccessfully');

        Livewire.emit('NewClasseMarksInsert');
        
    })


e.join('online')
    .here(function(users) {

        Livewire.emit('OnlineUsersLiveEvent', users);
    })
    .joining(function(user) {

        // Swal.fire({
        //     text: user.pseudo + " est en ligne ",
        //     toast: true,
        //     showConfirmButton: false,
        // });
    })
    .leaving(function(user) {


        // Swal.fire({
        //     text: user.pseudo + " s'est déconnecté ",
        //     toast: true,
        //     showConfirmButton: false,
        // });
    });