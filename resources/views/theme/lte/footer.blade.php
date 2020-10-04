{{-- -------------------------------------------------- --}}
{{-- LIBRERÍAS DE LA PLANTILLA --}}
{{-- -------------------------------------------------- --}}
<!-- jQuery -->
<script src="{{asset("assets/$theme/plugins/jquery/jquery.min.js")}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset("assets/$theme/plugins/bootstrap/js/bootstrap.bundle.min.js")}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset("assets/$theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js")}}"></script>
<!-- AdminLTE App -->
<script src="{{asset("assets/$theme/dist/js/adminlte.min.js")}}"></script>
{{-- -------------------------------------------------- --}}
{{-- LIBRERÍAS ADICIONALES --}}
{{-- -------------------------------------------------- --}}
<!-- Jquery Validations -->
<script src="{{asset("assets/$theme/plugins/jquery-validation/jquery.validate.min.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/jquery-validation/localization/messages_es.min.js")}}"></script>
<!-- Sweet Alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
{{-- -------------------------------------------------- --}}
{{-- PLUGINS PERSONALIZADOS  Y SCRIPS--}}
{{-- -------------------------------------------------- --}}
@yield('scriptPlugins')

<script src="{{asset("assets/js/functions.js")}}"></script>
{{-- <script src="{{asset("assets/js/scripts.js")}}"></script> --}}

@yield('scripts')