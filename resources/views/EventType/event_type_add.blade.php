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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.EventType.Add_EventType') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ url('event-type') }}" method="POST" id="eventtypeForm" name="eventtypeForm">
						<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="ChineseEventType">{{ __('languages.EventType.Chinese_EventType') }}</label>
										<input type="text" class="form-control" id="chineseeventtype" name="chineseeventtype" placeholder="{{ __('languages.EventType.Chinese_EventType') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="englishqualification">{{ __('languages.EventType.English_EventType') }}</label>
										<input type="text" class="form-control" id="englisheventtpye" name="englisheventtpye" placeholder="{{ __('languages.EventType.English_EventType') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.EventType.Service_type') }}</label>
										<select class="form-control" id="type_id" name="type_id">
											<option value="">{{ __('languages.EventType.Select_service_type') }}</option>
											<option value="1">Training</option>
											<option value="2">Activity</option>
											<option value="3">Service</option>
										</select>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.event.Select_status') }}</option>
											<option value="1" selected>{{ __('languages.Active') }}</option>
											<option value="2">{{ __('languages.Inactive') }}</option>
										</select>
									</div>
								</div>
								<input type="submit" class="btn btn-primary glow" value="{{ __('languages.Submit') }}" name="submit">
							</div>
						</form>
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