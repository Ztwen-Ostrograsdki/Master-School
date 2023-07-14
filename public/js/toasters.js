
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
        title: "Déconnexion réussie",
        icon: 'success',
        text: "Vous serez redirigé vers la page d'acceuil dans quelques secondes!",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
    });
});

window.addEventListener('Login', event => {
    Swal.fire({
        title: "Connexion réussie",
        icon: 'success',
        text: "Vous êtes connecté",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
    });
});
window.addEventListener('RegistredSelf', event => {
    Swal.fire({
        title: "Inscription réussie",
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
        title: "Inscription réussie",
        icon: 'success',
        text: event.detail.username + " a été inscrit avec succès",
        timer: 3000,
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,

    });
});

window.addEventListener('MessageDeleted', event => {
    Swal.fire({
        icon: 'success',
        text: "Le message a été supprimé avec succès",
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
