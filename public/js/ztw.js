window.addEventListener('hide-form', function(e) {
    $('.modal').modal('hide');
});

window.addEventListener('reloadPage', function(e) {
    location.reload(true);
});

window.addEventListener('modal-startAdvancedRequests', function(e) {
    $('#advancedrequestsModal').modal();
});
window.addEventListener('modal-manageClasseSubjects', function(e) {
    $('#classeSubjectManageModal').modal('show');
});
window.addEventListener('modal-addNewPupilToClasse', function(e) {
    $('#addNewPupil').modal('show');
});
window.addEventListener('modal-insertMultiplePupils', function(e) {
    $('#insertMultiplePupilsModal').modal('show');
});
window.addEventListener('modal-editPupilPersoData', function(e) {
    $('#pupilPersoData').modal('show');
});
window.addEventListener('modal-createNewClasse', function(e) {
    $('#createNewClasse').modal('show');
});
window.addEventListener('modal-createNewClasseGroup', function(e) {
    $('#createNewClasseGroup').modal('show');
});
window.addEventListener('modal-editClasseGroupData', function(e) {
    $('#editClasseGroupDataModal').modal('show');
});
window.addEventListener('modal-manageClasseGroup', function(e) {
    $('#classeGroupManageModal').modal('show');
});
window.addEventListener('modal-editClasseGroup', function(e) {
    $('#editClasseGroupModal').modal('show');
})
;window.addEventListener('modal-editClasseGroupCoeficients', function(e) {
    $('#editClasseGroupCoeficientsModal').modal('show');
});
window.addEventListener('modal-createNewLevel', function(e) {
    $('#createNewLevel').modal('show');
});
window.addEventListener('modal-createNewSubject', function(e) {
    $('#createNewSubject').modal('show');
});
window.addEventListener('modal-insertPupilRelatedMark', function(e) {
    $('#insertPupilRelatedMarks').modal('show');
});

window.addEventListener('modal-insertPupilMarks', function(e) {
    $('#insertPupilMarks').modal('show', function(ex) {
        $(this).find('.z-focus').focus();
    });
});

window.addEventListener('modal-markManager', function(e) {
    $('#markManagerModal').modal('show');
});

window.addEventListener('modal-displayMyNotifications', function(e) {
    $('#displayMyNotificationsModal').modal('show');
});
window.addEventListener('modal-manageClasseModalities', function(e) {
    $('#manageClasseModalitiesModal').modal('show');
    $("#manageClasseModalitiesModal input").focus();
});

window.addEventListener('modal-openSingleChatModal', function(e) {
    $('#singleChatModal').modal('show');
    $("#singleChatModal .chat-input").focus();
});
window.addEventListener('modal-adminAuthenticationModal', function(e) {
    $('#adminAuthenticationModal').modal('show');
    $("#adminAuthenticationModal input").focus();
});


$(function() {
    $(".z-modal-closer").click(function() {
        $('.modal').modal('hide');
    });
});



$(function() {
    $("#focus_photo_prf").click(function() {
        $("#photo_prf").focus();
    });
});


window.addEventListener('FireAlert', event => {
    if (event.detail.title !== undefined) {
        Swal.fire({
            title: event.detail.title,
            icon: event.detail.type,
            text: event.detail.message,
            timer: 3000,
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
        });
    } else {
        Swal.fire({
            icon: event.detail.type,
            text: event.detail.message,
            timer: 2000,
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
        });
    }
});

window.addEventListener('FireAlertDoNotClose', event => {
    if (event.detail.title !== undefined) {
        Swal.fire({
            title: event.detail.title,
            icon: event.detail.type,
            text: event.detail.message,
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
        });
    } else {
        Swal.fire({
            icon: event.detail.type,
            text: event.detail.message,
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
        });
    }
});


window.addEventListener('Logout', event => {
    Swal.fire({
        title: "D??connexion r??ussie",
        icon: 'success',
        text: "Vous serez redirig?? vers la page d'acceuil dans quelques secondes!",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
    });
});

window.addEventListener('Login', event => {
    Swal.fire({
        title: "Connexion r??ussie",
        icon: 'success',
        text: "Vous ??tes connect??",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
    });
});
window.addEventListener('RegistredSelf', event => {
    Swal.fire({
        title: "Inscription r??ussie",
        icon: 'success',
        text: "Nous allons vous connecter automatiquement dans quelques secondes!",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
    });
});

window.addEventListener('RegistredNewUser', event => {
    Swal.fire({
        title: "Inscription r??ussie",
        icon: 'success',
        text: event.detail.username + " a ??t?? inscrit avec succ??s",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,

    });
});

window.addEventListener('MessageDeleted', event => {
    Swal.fire({
        icon: 'success',
        text: "Le message a ??t?? supprim?? avec succ??s",
        timer: 1000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,

    });
});
window.addEventListener('Toast', event => {
    if (event.detail.title !== undefined) {
        Swal.fire({
            icon: event.detail.type,
            title: event.detail.title,
            text: event.detail.message,
            className: "text-dark",
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    } else {
        Swal.fire({
            icon: event.detail.type,
            text: event.detail.message,
            toast: true,
            position: 'center',
            showConfirmButton: false,
            className: "text-dark",
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }

        });
    }
});
window.addEventListener('ToastDoNotClose', event => {
    if (event.detail.title !== undefined) {
        Swal.fire({
            icon: event.detail.type,
            title: event.detail.title,
            text: event.detail.message,
            className: "text-dark",
            toast: true,
            position: 'center',
            showConfirmButton: false,
        });
    } else {
        Swal.fire({
            icon: event.detail.type,
            text: event.detail.message,
            className: "text-dark",
            toast: true,
            position: 'center',
            showConfirmButton: false,
        });
    }
});


$(function() {
    $('#OpenEditPhotoProfilModal').dblclick(function() {
        $('#editPhotoProfilModal').modal();
    });
});



$(function() {
    $('#chat-form textarea').on('input', function() {
        $("#errorBagTexto").addClass('d-none');
        $("#messages-container").addClass('border-warning');
        $("#messages-container").removeClass('border-danger');
        $('#sendBtnForChat').removeClass('text-danger');
        $('#sendBtnForChat').removeClass('btn-info');
        $('#sendBtnForChat').addClass('text-white');
        $('#sendBtnForChat').addClass('btn-primary');
        $('#chat-form textarea').addClass('text-dark');

    });
});



// $(function() {
//     $('#tooltip-2').mouseenter(function() {
//         $("#tooltip-3").tooltip();
//     });

// });


window.addEventListener('clear-textarea', event => {
    $('#chat-form textarea').val('');
});