document.addEventListener("DOMContentLoaded", ()=>{

	Livewire.hook('element.updated', (el, component) => {

		$('.form-searcher').addClass('d-flex');

	});

});

$(function() {
	let k = 1;
   $('.form-search-closer').on('click', (e) => {
		$('.form-searcher').removeClass('d-flex');
		$('.form-searcher').removeClass('navbar-search-open');
		$('.form-searcher').addClass('d-none', (e) =>{
			k = 3;
			$('.form-searcher').removeClass('d-none');
		})
	});
});

$(function() {
   $('.form-search-opener').on('click', (e) => {
		$('.form-searcher').removeClass('d-none');
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

$(function() {
    $("#focus_photo_prf").click(function() {
        $("#photo_prf").focus();
    });
});
