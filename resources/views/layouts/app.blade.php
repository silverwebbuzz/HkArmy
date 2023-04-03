<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'HKACA') }}</title>
	{{-- <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/S_blue.png') }}"> --}}
	<link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/hkaca_fav_icon.png') }}">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/font-awesome.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/qrscan/qrstyle.css') }}">
	{{-- <link rel="icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico')}}"> --}}
	<link rel="icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/hkaca_fav_icon.ico')}}">
	<link href="{{ asset('app-assets/vendors/css/vendors.min.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/select.dataTables.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/dataTables.checkboxes.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}" type="text/css" />
	<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/dragula.min.css') }}" type="text/css" />
	<link href="{{ asset('app-assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ asset('app-assets/css/bootstrap-extended.css')}}">
	<link href="{{ asset('app-assets/css/colors.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/components.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/themes/dark-layout.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}" rel="stylesheet">
	<link href="{{ asset('app-assets/css/pages/page-users.css') }}" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css')}}">
	<link href="{{ asset('assets/css/bootstrap-multiselect.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
	
	<link href="{{ asset('app-assets/css/pages/page-user-profile.css') }}" rel="stylesheet">

	<link href="{{ asset('assets/css/jquery.timepicker.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/fullcalendar.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/jquery-ui.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	
	<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	<script src="{{ asset('app-assets/vendors/js/jquery.min.js') }}"></script>
	<!-- <script src="{{ asset('assets/qrscan/html5-qrcode.min.js') }}"></script> -->
	<script>
		var BASE_URL = "{{ URL::to('/') }}";
		var ASSET_URL = "{{ URL::to('/public') }}";
		//var ASSET_URL = "{{ URL::to('/') }}";
	</script>

	@include('layouts.javascript_constante')
	<!-- <script type="text/javascript">
    window.onerror = function(message, url, lineNumber) {  
        // code to execute on an error  
        return true; // prevents browser error messages  
    };
</script>  -->
</head>
<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
	<div class="loader"></div>
	<div id="cover-spin"></div>
		@yield('content')
	<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('assets/js/additional-methods.min.js') }}"></script>
	<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
	<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}" type="text/javascript"></script>
	<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
	@if(app()->getLocale() == 'ch')
		<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
	@else
		<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables1.min.js') }}"></script>
	@endif
	<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('assets/js/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
	<!-- <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script>	 -->
	<script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
	<script src="{{ asset('assets/js/dataTables.select.min.js') }}"></script>
	<script src="{{ asset('assets/js/dataTables.checkboxes.min.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/extensions/dragula.min.js') }}"></script>
	<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
	<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/footer.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/pages/page-users.js') }}"></script>

	<script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>

	<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
	<script src="{{ asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/bootstrap-datetimepicker.min.js') }}"></script>
	<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
	<script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap-multiselect.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.timepicker.min.js') }}"></script>
	<script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.session.min.js') }}"></script>



	<script src="{{ asset('app-assets/js/scripts/charts/chart-apex.js') }}"></script>

	@if(request()->is('eventManagement/create'))
	<script src="{{ asset('assets/js/event_add_scripts.js') }}"></script>
	@endif
	@if(request()->is('eventManagement/*/edit'))
	<script src="{{ asset('assets/js/event_edit_scripts.js') }}"></script>
	@endif
	<script src="{{ asset('assets/js/scripts.js') }}"></script>

	<script src="{{ asset('assets/js/productScripts.js') }}"></script>
  </body>
</html>