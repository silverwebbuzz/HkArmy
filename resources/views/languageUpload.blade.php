@extends('layouts.app')

@section('content')
<!-- top navigation -->
@include('layouts.header')
<!-- /top navigation -->
@include('layouts.sidebar')

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-12 mb-2 mt-1">
				<div class="row">
					<div class="col-12">
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Language.Language') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							@if(session()->has('success_msg'))
								<div class="alert alert-success alert-dismissible">
								<a href="javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									{{ session()->get('success_msg') }}
								</div>
							@endif
							@if(session()->has('error_msg'))
								<div class="alert alert-danger alert-dismissible">
									<a href="javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									{{ session()->get('error_msg') }}
								</div>
							@endif
							<form id="languageForm" name="languageForm=" action="{{ url('language') }}"  method="post" class="form-horizontal form-label-left" enctype='multipart/form-data'>
							<input type="hidden" name="_token" value="{{ csrf_token() }} ">
								<div class="col-lg-6 col-md-12">
									<fieldset class="form-group">
										<label for="En Language">{{ __('languages.Language.English_Language') }}</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="enlanguage" name="enlanguage">
											<label class="custom-file-label" for="inputGroupFile01"></label>
										</div>
									</fieldset>
								</div>
								<div class="col-lg-6 col-md-12">
									<fieldset class="form-group">
										<label for="En Language">{{ __('languages.Language.Chinese_Language') }}</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="chlanguage" name="chlanguage">
											<label class="custom-file-label" for="inputGroupFile01"></label>
										</div>
									</fieldset>
								</div>
								<input type="submit" class="btn btn-primary glow position-relative submit" value="{{ __('languages.Submit') }}" name="submit">
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection