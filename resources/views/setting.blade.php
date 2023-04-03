@extends('layouts.app')
@section('content')
@include('layouts.header')
@include('layouts.sidebar')

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-12 mb-2 mt-1">
				<div class="row">
					<div class="col-12">
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Settings.Settings') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="basic-dropdown">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<form method="POST" id="settingsForm" name="settingsForm" class="settingsForm" action="{{ url('settings/update') }}"  enctype='multipart/form-data'>
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
											<div class="col-lg-6 col-md-12">
												<fieldset class="form-group">
													<label for="Logo">{{ __('languages.Settings.Logo') }}</label>
													<div class="custom-file">
														<input type="file" class="custom-file-input" id="image" name="image">
														<label class="custom-file-label" for="inputGroupFile01"></label>
													</div>
												</fieldset>
											</div>
										</div>
										<div class="form-row">
											<div class="col-lg-6 col-md-12">
												@if(!empty($Setting->Logo) && $Setting->Logo != '')
													<img src="{{ !empty($Setting->Logo) ? asset($Setting->Logo) : '' }}" alt="" height="100px" width="100px">
												@else
													<img src="{{ asset('assets/image/no-image.jpg') }}" alt="no-image" height="100px" width="100px">
												@endif
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="min_hour">{{ __('languages.Settings.Early_late_margin') }}</label>
												<input type="text" class="form-control" id="min_hour" name="min_hour" placeholder="{{ __('languages.Settings.Early_late_margin') }}" value="{{ !empty($Setting->min_hour) ? $Setting->min_hour : ''}}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="HKD">{{ __('languages.Settings.HKD') }}</label>
												<input type="text" class="form-control" id="HKD" name="HKD" placeholder="{{ __('languages.Settings.HKD') }}" value="{{ !empty($Setting->HKD) ? $Setting->HKD : '' }}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="sitename">{{ __('languages.Settings.Sitename') }}</label>
												<input type="text" class="form-control" id="SiteName" name="SiteName" placeholder="{{ __('languages.Settings.Sitename') }}" value="{{ !empty($Setting->SiteName) ? $Setting->SiteName : '' }}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="tokenExpireDay">{{__('languages.Settings.token_expire_day')}}</label>
												<input type="text" class="form-control" id="tokenExpireDay" name="tokenExpireDay" placeholder="Token expire day" value="{{ !empty($Setting->token_expire_day) ? $Setting->token_expire_day : '' }}">
											</div>
										</div>
										<input type="submit" name="submit" id="submit" value="{{ __('languages.Submit') }}" class="btn btn-primary glow position-relative submit">
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@include('layouts.footer')
@endsection