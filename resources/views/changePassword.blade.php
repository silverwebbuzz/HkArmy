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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.ChangePassword.ChangePassword') }}</h3>
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
								<form method="POST" id="changepasswordform" name="changepasswordform" class="changepasswordform">
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="old password">{{ __('languages.ChangePassword.Old_password') }}</label>
												<input type="text" class="form-control" id="old_password" name="old_password" placeholder="{{ __('languages.ChangePassword.Old_password') }}" value="">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="New password">{{ __('languages.ChangePassword.New_password') }}</label>
												<input type="text" class="form-control" id="new_password" name="new_password" placeholder="{{ __('languages.ChangePassword.New_password') }}" value="">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="confirm password">{{ __('languages.ChangePassword.Confirm_password') }}</label>
												<input type="text" class="form-control" id="confirm_password" name="confirm_password" placeholder="{{ __('languages.ChangePassword.Confirm_password') }}" value="">
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