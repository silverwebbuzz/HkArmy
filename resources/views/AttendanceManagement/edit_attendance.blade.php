<!-- <div class="modal fade" id="eventLogoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Attendance</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div> -->

			<form action="{{ url('attendanceManagement',$attendance['id']) }}" method="POST" id="attendanceForm" name="attendanceForm" class="attendanceForm">
				<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
				{{ method_field('PUT') }}
				<div class="form-row">
					<div class="form-group col-md-6 mb-50">
						<label class="text-bold-600" for="MemberName">Member Name</label>
						<fieldset class="form-group">
							<select class="form-control" id="members" name="members">
								<option value="">Select Member</option>
								@if($members)
								@foreach($members as $member)
								@if($member['UserName'])
								<option value="{{ $member['id'] }}" data-code="{{ $member['MemberCode'] }}" @if($member['id'] == $attendance['user_id']) selected @endif>{{ $member['UserName'] }}</option>
								@else
								<option value="{{ $member['id'] }}" data-code="{{ $member['MemberCode'] }}" @if($member['id'] == $attendance['user_id']) selected @endif>{{ $member['Chinese_name'] }} & {{ $member['English_name'] }}</option>
								@endif
								@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group col-md-6 mb-50">
						<label class="text-bold-600" for="MemberCode">Member Code</label>
						<input type="text" class="form-control" id="memberCode" name="memberCode" placeholder="Member Code" value="{{ $attendance['member_code'] }}" readonly>
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
								<option value="{{ $event['id'] }}" data-event-type="{{ $event['event_type'] }}" @if($event['id'] ==  $attendance['event_id']) selected @endif>{{ $event['event_name'] }}</option>
								@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group col-md-6 mb-50">
						<label class="text-bold-600" for="Event Type">Event Type</label>
						<input type="text" class="form-control" id="eventType" name="eventType" value="{{ $attendance['event_type'] }}"  placeholder="Event Type" readonly>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6 mb-50">
						<label for="inputfirstname4">In time</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control" placeholder="Select In Time" id="inTime" name="inTime" autocomplete="off" value="{{ $attendance['in_time'] }}">
							<div class="form-control-position">
								<i class='bx bx-history'></i>
							</div>
						</fieldset>
					</div>
					<div class="form-group col-md-6 mb-50">
						<label for="inputlastname4">Out time</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control" placeholder="Select Out Time" id="outTime" name="outTime" autocomplete="off" value="{{ $attendance['out_time'] }}">
							<div class="form-control-position">
								<i class='bx bx-history'></i>
							</div>
						</fieldset>
					</div>
				</div>
				<div class="form-row">
					
					<div class="form-group col-md-6 mb-50">
						<label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.event.End Date') }}</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control" placeholder="{{ __('languages.event.Select_date') }}" id="eventenddate" name="date"  autocomplete="off" value="{{ date('d F, Y',strtotime($attendance['date'])) }}">
							<div class="form-control-position">
								<i class='bx bx-calendar'></i>
							</div>
						</fieldset>
					</div>
					<div class="form-group col-md-6 mb-50">
						<label class="text-bold-600" for="Hours">Hours</label>
						<input type="text" class="form-control" id="hours" name="hours" placeholder="Hours" readonly value="{{ $attendance['hours'] }}">
					</div>
				</div>
			<!-- 	<input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary glow position-relative submit">
			</form> -->

								<!-- </div>
							</div>
						</div> -->