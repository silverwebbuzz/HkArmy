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
						<h3 class="content-header-title float-left pr-1 mb-0">Add Attendance</h3>
					</div>
				</div>
			</div>
			@if(session()->has('success_msg'))
				<div class="alert alert-success">
					{{ session()->get('success_msg') }}
				</div>
			@endif
			@if(session()->has('error_msg'))
				<div class="alert alert-danger">
					{{ session()->get('error_msg') }}
				</div>
			@endif
		</div>
		<div class="content-body">
			<section id="basic-dropdown">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form action="{{ url('attendanceManagement') }}" method="POST" id="attendanceForm" name="attendanceForm" class="attendanceForm">
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="MemberName">Member Name</label>
												<fieldset class="form-group">
													<select class="form-control" id="members" name="members">
														<option value="">Select Member</option>
														@if($members)
														@foreach($members as $member)
															@if($member['UserName'])
																<option value="{{ $member['id'] }}" data-code="{{ $member['MemberCode'] }}">{{ $member['UserName'] }}</option>
															@else
																<option value="{{ $member['id'] }}" data-code="{{ $member['MemberCode'] }}">{{ $member['Chinese_name'] }} & {{ $member['English_name'] }}</option>
															@endif
														@endforeach
														@endif
													</select>
												</fieldset>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="MemberCode">Member Code</label>
												<input type="text" class="form-control" id="memberCode" name="memberCode" placeholder="Member Code" value="" readonly>
											</div>
										</div>
										<div class="title">
											<h4 class="card-title py-2 m-0">Event Detail</h4>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label for="users-list-role">Event Name</label>
												<fieldset class="form-group">
													<select class="form-control" id="eventName" name="eventName">
														<option value="">Select Event name</option>
														@if($events)
															@foreach($events as $event)
																<option value="{{ $event['id'] }}" data-event-type="{{ $event['event_type'] }}">{{ $event['event_name'] }}</option>
															@endforeach
														@endif
													</select>
												</fieldset>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="Event Type">Event Type</label>
												<input type="text" class="form-control" id="eventType" name="eventType" value=""  placeholder="Event Type" readonly>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label for="inputfirstname4">In time</label>
												<fieldset class="form-group position-relative has-icon-left">
													<input type="text" class="form-control" placeholder="Select In Time" id="inTime" name="inTime" autocomplete="off">
													<div class="form-control-position">
														<i class='bx bx-history'></i>
													</div>
												</fieldset>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label for="inputlastname4">Out time</label>
												<fieldset class="form-group position-relative has-icon-left">
													<input type="text" class="form-control" placeholder="Select Out Time" id="outTime" name="outTime" autocomplete="off">
													<div class="form-control-position">
														<i class='bx bx-history'></i>
													</div>
												</fieldset>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="Date">Date</label>
												<fieldset class="form-group position-relative has-icon-left">
													<input type="text" class="form-control" placeholder="Select Date" name="date" id="date" autocomplete="off">
													<div class="form-control-position">
														<i class='bx bx-calendar'></i>
													</div>
												</fieldset>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="Hours">Hours</label>
												<input type="text" class="form-control" id="hours" name="hours" placeholder="Hours" readonly>
											</div>
										</div>
										<input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary glow position-relative submit">
									</form>
								</div>
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