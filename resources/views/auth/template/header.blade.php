<!DOCTYPE html>
<html class="loading" lang="{{ app()->getLocale() }}" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="Frest admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
	<meta name="keywords" content="admin template, Frest admin template, dashboard template, flat admin template, responsive admin template, web app">
	<meta name="author" content="PIXINVENT">
	<title>{{ config('app.name', 'HKACA') }}</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/qrscan/qrstyle.css') }}">
	{{-- <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}"> --}}
	{{-- <link rel="icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico')}}"> --}}
	<link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/hkaca_fav_icon.ico') }}">
	<link rel="icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/hkaca_fav_icon.ico')}}">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

	<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
	<!-- END: Vendor CSS-->

	<!-- BEGIN: Theme CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css')}}">
	<!-- END: Theme CSS-->

	<!-- BEGIN: Page CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/authentication.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">
	<!-- END: Page CSS-->

	<!-- BEGIN: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<script type="text/javascript">
		var BASE_URL = "{{ URL::to('/') }}";
	</script>
	@include('layouts.javascript_constante')
	<!-- END: Custom CSS-->
</head>