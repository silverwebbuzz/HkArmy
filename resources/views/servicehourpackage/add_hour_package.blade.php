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
						<h3 class="content-header-title float-left pr-1 mb-0">Add Service Hour Package </h3>
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
								<form action="{{ url('service-hour-package') }}" method="POST" id="hourPacakgeForm" name="hourPacakgeForm" class="hourPacakgeForm">
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="packagename">Package Name</label>
												<input type="text" class="form-control" id="packagename" name="packagename" placeholder="Package Name" value="">
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="hours">Hours</label>
												<input type="text" class="form-control" id="hours" name="hours" placeholder="Hours" value="">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label for="users-list-role">{{ __('languages.Status') }}</label>
												<select class="form-control" id="status" name="status">
													<option value="">{{ __('languages.event.Select_status') }}</option>
													<option value="1">Active</option>
													<option value="2">Inactive</option>
												</select>
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

<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection