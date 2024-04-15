window.addEventListener('hide-form', function(e) {
    $('.modal').modal('hide');
});

window.addEventListener('reloadPage', function(e) {
    location.reload(true);
});

window.addEventListener('modal-confirmation', function(e) {
    $('#confirmatorModal').modal('show');
});

window.addEventListener('modal-makeClassePresenceLate', function(e) {
    $('#classePresenceLateModal').modal('show');
});

window.addEventListener('modal-ConfirmClasseMarksDeletion', function(e) {
    $('#confirmClasseMarksDeletionModal').modal('show');
});
window.addEventListener('modal-manageAdminStatus', function(e) {
    $('#adminManagerModal').modal('show');
});

window.addEventListener('modal-parentFollowPupil', function(e) {
    $('#ParentFollowPupilModal').modal('show');
});

window.addEventListener('modal-quotaManager', function(e) {
    $('#quotaManager').modal('show');
});
window.addEventListener('modal-manageTeacherClasses', function(e) {
    $('#manageTeacherClasses').modal('show');
});
window.addEventListener('modal-importPupilsIntoClasse', function(e) {
    $('#migratePupilsIntoClasseModal').modal('show');
});

window.addEventListener('modal-definedClasseTeachers', function(e) {
    $('#definedClasseTeachersModal').modal('show');
});

window.addEventListener('modal-addNewTeacher', function(e) {
    $('#addNewTeacherModal').modal('show');
});
window.addEventListener('modal-startAdvancedRequests', function(e) {
    $('#advancedrequestsModal').modal();
});
window.addEventListener('modal-updateProfilImage', function(e) {
    $('#updateProfilImageModal').modal('show');
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

window.addEventListener('modal-movePupilToNewClasse', function(e) {
    $('#movePupilToNewClasseModal').modal('show');
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
window.addEventListener('modal-manageClasseReferees', function(e) {
    $('#manageClasseReferees').modal('show');
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

window.addEventListener('modal-marksSettings', function(e) {
    $('#marksSettingsModal').modal('show');
});

window.addEventListener('modal-marksNullActionsConfirmation', function(e) {
    $('#marksNullActionsConfirmation').modal('show');
});
window.addEventListener('modal-resetAbsencesAndLatesConfirmation', function(e) {
    $('#resetAbsencesAndLatesConfirmation').modal('show');
});

window.addEventListener('modal-eventPeriodManager', function(e) {
    $('#eventPeriodManagerModal').modal('show');
});

window.addEventListener('modal-definedSemestresPeriods', function(e) {
    $('#definedSemestrePeriodModal').modal('show');
});

window.addEventListener('modal-insertTimePlan', function(e) {
    $('#insertTimePlanModal').modal('show');
});

window.addEventListener('modal-classeMarksDeleter', function(e) {
    $('#classeMarksDeleterModal').modal('show');
});
window.addEventListener('modal-classeMarksRestorer', function(e) {
    $('#marksRestorerModal').modal('show');
});

window.addEventListener('modal-classeMarksConvertion', function(e) {
    $('#confirmClasseMarksConvertionModal').modal('show');
});

window.addEventListener('modal-updateClassePupilsLTPKMatricule', function(e) {
    $('#updateClassePupilsLTPKMatricule').modal('show');
});
window.addEventListener('modal-updateClassePupilsNames', function(e) {
    $('#updateClassePupilsNames').modal('show');
});

window.addEventListener('modal-insertClassePupilsMarksTogether', function(e) {
    $('#insertClassePupilsMarksTogether').modal('show', function(ex) {
        
    });
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
    $('#OpenEditPhotoProfilModal').dblclick(function() {
        $('#editPhotoProfilModal').modal();
    });
});
