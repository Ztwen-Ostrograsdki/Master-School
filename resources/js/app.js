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
    .listen('PupilDataAreReadyToFetchEvent', function(e) {

        console.log(e.data)

        Livewire.emit('DataAreReadyToFetchLiveEvent', e);
    })
    .listen('ParentRequestAcceptedEvent', function(e) {

        Swal.fire({
            icon: 'success',
            title: "Demande Acceptée",
            text: " Votre compte parent est désormais actif et accessible",
            toast: true,
            showConfirmButton: false,
        });

        Livewire.emit('NotifyMeWhenMyRequestAccepted', e.user);
    })
    .listen('PupilSetToOrFromAbandonnedEvent', function(e) {

        Livewire.emit('ReloadClasseListDataAbandonLiveEvent');

    }).listen('ForcingUserDisconnectionEvent', function(e) {

        Swal.fire({
            icon: 'warning',
            title: "PROCEDURE INEVITABLE: RUPTURE DE CONNEXION",
            text: " Vous allez être déconnecté dans quelques secondes!",
            toast: true,
            showConfirmButton: false,
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
            showConfirmButton: false,
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
            showConfirmButton: false,
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
    .listen('ClassePresenceLateWasCompletedEvent', function(e) {
        
        Livewire.emit('PresenceLateWasUpdated');
    }) 
    .listen('ClasseDataWasUpdateSuccessfullyEvent', function(e) {
        
        Livewire.emit('ClasseDataWasUpdated');
    }) 

e.private('master')
    .listen('NewAddParentRequestEvent', function(e) {

        Livewire.emit('NewParentRequest');
    })
    .listen('NewEpreuveWasUploadedEvent', function(e) {

        Livewire.emit('NewEpreuveWasUploadedLiveEvent');
    })

    .listen('ClasseMarksWasFailedEvent', function(e) {

        Swal.fire({
            icon: 'error',
            title: "Le traitement des notes a échoué",
            text: " Votre demande a été Accepté",
            toast: true,
            showConfirmButton: false,
        });
    })

e.private('reloadMarkChannel.' + window.ClientUser.id)
   
e.private('reloader.' + window.ClientUser.id)

    .listen('ClasseMarksWasUpdatedIntoDBSuccessfullyEvent', function(e) {

        Livewire.emit('ClasseDataLoadedSuccessfully');

        Livewire.emit('NewClasseMarksInsert');
        
    })

// e.private('classe.marks.insert.user.' + window.ClientUser.id)
//     .listen('JobFinisched', function(u) {
//         // console.log(u);
//         Swal.fire({
//             icon: 'success',
//             title: "Demande Acceptée",
//             text: " Votre demande a été Accepté",
//             toast: true,
//             showConfirmButton: false,
//         });
//         // Livewire.emit('NewParentRequest');
//     })
   

// e.join('online')
//     .here(function(users) {
//         // console.log('users on line', users);
//     })
//     .joining(function(user) {
//         Swal.fire({
//             text: user.name + " est en ligne ",
//             toast: true,
//             showConfirmButton: false,
//         });
//     })
//     .leaving(function(user) {
//         Swal.fire({
//             text: user.name + " est s'est déconnecté ",
//             toast: true,
//             showConfirmButton: false,
//         });
//     });