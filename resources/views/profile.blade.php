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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Profile') }}</h3>
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
								<form method="POST" id="profileform" name="profileform" class="profileform" enctype='multipart/form-data'>
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
											<div class="col-lg-6 col-md-12">
												<fieldset class="form-group">
													<label for="En Language">{{ __('languages.Image') }}</label>
													<div class="custom-file">
														<input type="file" class="custom-file-input" id="image" name="image">
														<label class="custom-file-label" for="inputGroupFile01"></label>
													</div>
												</fieldset>
											</div>
										</div>
										<div class="form-row">
											<div class="col-lg-6 col-md-12">
												@if($userData->image != '')
													<img src="{{ asset($userData->image) }}" alt="" height="100px" width="100px">
												@else
													<img src="{{ asset('assets/image/no-image.jpg') }}" alt="no-image" height="100px" width="100px">
												@endif
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="Chinease Name">{{ __('languages.member.Chinese_name') }}</label>
												<input type="text" class="form-control" id="Chinese_name" name="Chinese_name" placeholder="{{ __('languages.member.Chinese_name') }}" value="{{ $userData->Chinese_name }}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="English name">{{ __('languages.member.English_name') }}</label>
												<input type="text" class="form-control" id="English_name" name="English_name" placeholder="{{ __('languages.member.English_name') }}" value="{{ $userData->English_name }}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="Chinese address">{{ __('languages.member.Chinese_address') }}</label>
												<input type="text" class="form-control" id="Chinese_address" name="Chinese_address" placeholder="{{ __('languages.member.Chinese_address') }}" value="{{ $userData->Chinese_address }}">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="English address">{{ __('languages.member.English_address') }}</label>
												<input type="text" class="form-control" id="English_address" name="English_address" placeholder="{{ __('languages.member.English_address') }}" value="{{ $userData->English_address }}">
											</div>
										</div>

										@if(Session::get('user')['role_id'] == '2')
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="English address">{{ __('languages.member.Tokens') }}</label>
												<input type="text" class="form-control" id="tokens" name="tokens" placeholder="{{ __('languages.member.Tokens') }}" value="{{ !empty($tokenDetail->total_token) ? $tokenDetail->total_token : '0' }}" readonly="">
											</div>
										</div>
										@endif

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