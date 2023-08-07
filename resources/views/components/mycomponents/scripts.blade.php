
<script src="{{ asset('myvendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>


<!-- Additional Scripts -->
<script src="{{ asset('myassets/js/custom.js') }}" defer></script>
<script src="{{ asset('myassets/js/owl.js') }}" defer></script>
<script src="{{ asset('myassets/js/slick.js') }}" defer></script>
<script src="{{ asset('myassets/js/isotope.js') }}" defer></script>
<script src="{{ asset('myassets/js/accordions.js') }}" defer></script>


<script src="{{asset('registrationVendor/jquery/jquery.js')}}"></script>
<script src="{{asset('myassets/js/registration/global.js')}}"></script>
<script src="{{asset('js/modals.js')}}"></script>
<script src="{{asset('myassets/js/bootstrap.min.js')}}"></script>
<script src="{{asset('myassets/js/popper.min.js')}}"></script>
<script src="{{asset('myassets/js/ztw-animate.js')}}"></script>
<script src="{{asset('myassets/js/chat.js')}}"></script>
<link rel="stylesheet" href="{{ asset('myassets/css/admin/sweetAlert.css')}}">
<script src="{{asset('js/toasters.js')}}"></script>
<script src="{{asset('js/actions.js')}}"></script>
    @stack('modals')

    @livewireScripts

