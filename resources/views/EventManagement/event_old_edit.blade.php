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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.event.Edit Event') }}</h3>
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
								<form action="{{ url('eventManagement',$edit_event['id']) }}" method="POST" id="eventform" name="eventform" class="eventform">
										{{ method_field('PUT') }}
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="eventname">{{ __('languages.event.Event Name') }}</label>
												<input type="text" class="form-control" id="eventname" name="event_name" placeholder="{{ __('languages.event.Event Name') }}" value="{{ $edit_event->event_name }}">
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="eventnumber">{{ __('languages.event.Event_code') }}</label>
												<input type="text" class="form-control" id="eventnumber" name="event_code" placeholder="{{ __('languages.event.Event_code') }}" value="{{ $edit_event->event_code }}" date-eventid="{{ $edit_event['id'] }}" readonly>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label for="users-list-role">{{ __('languages.event.Event Type') }}</label>
												<fieldset class="form-group">
													<select class="form-control eventTypeForCode" id="eventtype" name="event_type">
														<option value="">{{ __('languages.event.Select_event_type') }} </option>
														@if(!empty($get_event_type_list))
														@php
															echo $get_event_type_list;
														@endphp
														@endif
													</select>
												</fieldset>
											</div>
											<div class="form-group col-md-3 mb-50">
												<label class="text-bold-600" for="assesmentyes">{{ __('languages.event.Assesment') }}</label>
												<ul class="list-unstyled mb-0">
													<li class="d-inline-block my-1 mr-1 mb-1">
														<fieldset>
															<div class="custom-control custom-radio">
																<input type="radio" class="custom-control-input" name="assessment" id="assesmentyes" value="Yes"  @if($edit_event->assessment == 'Yes') checked @endif>
																<label class="custom-control-label" for="assesmentyes">{{ __('languages.Yes') }}</label>
															</div>
														</fieldset>
													</li>
													<li class="d-inline-block mt-1 mr-1 mb-1">
														<fieldset>
															<div class="custom-control custom-radio">
																<input type="radio" class="custom-control-input" name="assessment" id="assesmentno" value="No" @if($edit_event->assessment == 'No') checked @endif>
																<label class="custom-control-label" for="assesmentno">{{ __('languages.No') }}</label>
															</div>
														</fieldset>
													</li>
												</ul>
												<div class="assesment-error-cls"></div>
											</div>
											<div class="form-group col-md-3 mb-50">
												<div class="form-row assesment-decl-cls" @if($edit_event->assessment == 'Yes') style="display: block;" @else style="display: none;" @endif>
													<div class="form-group col-md-12 mb-50">
														<label class="text-bold-600" for="Experience"></label>
														<input type="text" class="form-control" id="experience" placeholder="{{ __('languages.event.Assesment') }}" value="{{ $edit_event['assessment_text'] }}" name="assessment_text">
													</div>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="event_money">{{ __('languages.event.event_money') }}</label>
												<input type="text" class="form-control" id="event_money" name="event_money" placeholder="{{ __('languages.event.event_money') }}" value="{{ $edit_event['event_money'] }}">
											</div>

											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="eventnumber">{{ __('languages.Status') }}</label>
												<select class="form-control" id="edit_event_status" name="status">
																<option value="">{{ __('languages.event.Select_status') }}</option>
																<option value="1" data-id="{{ $edit_event['id'] }}" @if($edit_event['status'] == '1') selected @endif>{{ __('languages.event.Published') }}</option>
																<option value="2" data-id="{{ $edit_event['id'] }}" @if($edit_event['status'] == '2') selected @endif>{{ __('languages.event.Unpublished') }}</option>
																<option value="3" data-id="{{ $edit_event['id'] }}" @if($edit_event['status'] == '3') selected @endif>{{ __('languages.event.Ready_to_close') }}</option>
																<option value="4" data-id="{{ $edit_event['id'] }}" @if($edit_event['status'] == '4') selected @endif>{{ __('languages.event.Close_event') }}</option>
															</select>
											</div>

										</div>

										@if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id'])))

											@if($edit_event['status'] != '1')
												<div class="form-row add-evens-cls1">
													<div class="form-group col-md-12 mb-50">
														<button type="button" class="btn btn-primary glow position-relative edit_new_event_dates" style="float: right;">Add Dates</button>
													</div>
												</div>
											@endif
										@endif
										<div class="form-row">
											<div id="calendar" height="300px;"></div>
										</div>

										<!-- <div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label for="users-list-role">{{ __('languages.Status') }}</label>
												<select class="form-control" id="status" name="status">
													<option value="">{{ __('languages.event.Select_status') }}</option>
													<option value="0" @if($edit_event['status'] == '0') selected @endif>{{ __('languages.event.Draft') }}</option>
													<option value="1" @if($edit_event['status'] == '1') selected @endif>{{ __('languages.event.Published') }}</option>
													<option value="2" @if($edit_event['status'] == '2') selected @endif>{{ __('languages.event.Unpublished') }}</option>
												</select>
											</div>
										</div>
										<input type="submit" name="submit" id="submit" value="{{ __('languages.Submit') }}" class="btn btn-primary glow position-relative submit"> -->
									</div>
									@if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id'])))
										<div class="form-row add-event-cls-bottom">
											<div class="form-group col-md-12 mb-50">
												<button type="submit" class="btn btn-primary glow position-relative">Update</button>

											</div>
										</div>
										@endif
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

@php
$dayofweek = '';
if($weekofday == '1'){
	$dayofweek = 'first';
}else if($weekofday == '2'){
	$dayofweek = 'second';
}
else if($weekofday == '3'){
	$dayofweek = 'third';
}
else if($weekofday == '4'){
	$dayofweek = 'fourth';
}
else if($weekofday == '5'){
	$dayofweek = 'fifth';
}
@endphp
@if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id'])))
<div class="modal fade assign-tq" id="eventmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<form method="POST" id="recurringeventform" name="recurringeventform" class="recurringeventform">
				<input type="hidden" name="_token"  id="csrf-tokens" value="{{ csrf_token() }}">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ __('languages.event.Add Event') }}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600" for="StartDate">{{ __('languages.event.Start Date') }}</label>
								<fieldset class="form-group position-relative has-icon-left">
									<input type="text" class="form-control" placeholder="Select Date" id="eventstartdate" name="eventstartdate" value="{{ date('l, d F, yy') }}">
									<div class="form-control-position">
										<i class='bx bx-calendar'></i>
									</div>
								</fieldset>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6 mb-50">
								<label class="text-bold-600" for="StartTime">{{ __('languages.event.Start_time') }}</label>
								<input type="text" class="form-control" placeholder="Select time" id="eventstarttime" name="eventstarttime" value="">
							</div>
							<div class="form-group col-md-6 mb-50">
								<label class="text-bold-600" for="EndTime">{{ __('languages.event.End_time') }}</label>
								<input type="text" class="form-control" placeholder="Select time" id="eventendtime" name="endtime" value="">
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600" for="TotalHours">{{ __('languages.event.Total_hour') }}</label>
								<input type="text" class="form-control totaleventhours"id="totaleventhours" value="0" name="eventhours" readonly>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label for="users-list-role">{{ __('languages.Status') }}</label>
								<select class="form-control" id="status" name="status">
									<option value="">{{ __('languages.event.Select_status') }}</option>
									<option value="0" selected>{{ __('languages.event.Draft') }}</option>
									<option value="1">{{ __('languages.event.Published') }}</option>
									<option value="2">{{ __('languages.event.Unpublished') }}</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label for="users-list-role">{{ __('languages.event.Occurs') }}</label>
								<fieldset class="form-group">
									<select class="form-control occurs" id="occurs" name="occurs">
										<option value="">{{ __('languages.event.Select_occurs') }}</option>
										<option value="Once">{{ __('languages.event.Once') }}</option>
										<option value="Daily">{{ __('languages.event.Daily') }}</option>
										<option value="Weekly">{{ __('languages.event.Weekly') }}</option>
										<option value="Monthly">{{ __('languages.event.Monthly') }}</option>
									</select>
								</fieldset>
							</div>
						</div>
						<div class="form-row weeklyoccurs">
							<div class="form-group col-md-12 mb-50">
								<ul class="list-unstyled mb-0">
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" class="checkbox-input weeklychkcls" id="checkbox2" value="0">
												<label for="checkbox2">{{ __('languages.event.Su') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox3" value="1" class="checkbox-input weeklychkcls">
												<label for="checkbox3">{{ __('languages.event.Mo') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox4" value="2" class="checkbox-input weeklychkcls">
												<label for="checkbox4">{{ __('languages.event.Tu') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox5" value="3" class="checkbox-input weeklychkcls">
												<label for="checkbox5">{{ __('languages.event.We') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox6" value="4" class="checkbox-input weeklychkcls">
												<label for="checkbox6">{{ __('languages.event.Th') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox7" value="5" class="checkbox-input weeklychkcls">
												<label for="checkbox7">{{ __('languages.event.Fr') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-2 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="checkbox8" value="6" class="checkbox-input weeklychkcls">
												<label for="checkbox8">{{ __('languages.event.Sa') }}</label>
											</div>
										</fieldset>
									</li>
								</ul>
							</div>
						</div>
						<div class="form-row monthlyoccurs">
							<div class="form-group col-md-12 mb-50">
								<fieldset class="form-group">
									
									<select class="form-control occur-monthly-clsss" id="monthly_occurs" name="monthly_occurs">
										<option value="">{{ __('languages.event.Select_Monthly') }}</option>
										<option value="{{ $number_today_date }}/month">{{ __('languages.event.On_the') }} {{ $today_date }}</option>
										<option value="{{ $today_day }}/weekday">{{ __('languages.event.On_the_first') }} {{ $dayofweek }} {{ $today_day }}</option>
									</select>
									<div class="occur-monthly-cls"></div> <!-- Don't remove this class -->
								</fieldset>
							</div>
						</div>
						<div class="dailyoccurs-cls">
							<div class="border-cls"></div>
							<h4>{{ __('languages.event.Ending') }}</h4>
							<div class="form-row">
								<div class="form-group col-md-12 mb-50">
									<label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.event.End Date') }}</label>
									<fieldset class="form-group position-relative has-icon-left">
										<input type="text" class="form-control" placeholder="{{ __('languages.event.Select_date') }}" id="eventenddate" name="eventenddate"  autocomplete="off">
										<div class="form-control-position">
											<i class='bx bx-calendar'></i>
										</div>
									</fieldset>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" name="submit" id="submit_event" class="btn btn-primary" value="{{ __('languages.event.Save') }}">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endif
@if(in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id'])))
<div class="modal fade" id="action_event" tabindex="-1" role="dialog" aria-labelledby="action_event" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			 <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Action</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p class="p-cls">Click to get your action</p>
				<button type="button" class="btn btn-success edit-btn-event">Edit</button>
				<button type="button" class="btn btn-danger delete-btn-event">Delete</button>
			</div>
		</div>
	</div>
</div>
@endif
<div class="modal fade assign-tq" id="editeventmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<form method="POST" id="editRecurringEventform" name="editRecurringEventform">
				<input type="hidden" name="_token"  class="csrf-token" value="{{ csrf_token() }}">
				<input type="hidden" name="eventid"  class="event-id-cls" value="">
				<input type="hidden" name="eventscheduleid"  class="event-schedule-id-cls" value="">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ __('languages.event.Edit Event') }}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600" for="StartDate">{{ __('languages.event.Start Date') }}</label>
								<fieldset class="form-group position-relative has-icon-left">
									<input type="text" class="form-control" placeholder="Select Date" id="editeventstartdate" name="eventstartdate" value="{{ date('l, d F, yy') }}"  disabled="">
									<div class="form-control-position">
										<i class='bx bx-calendar'></i>
									</div>
								</fieldset>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6 mb-50">
								<label class="text-bold-600" for="StartTime">{{ __('languages.event.Start_time') }}</label>
								<input type="text" class="form-control" placeholder="Select time" id="editeventstarttime" name="starttime" value="">
							</div>
							<div class="form-group col-md-6 mb-50">
								<label class="text-bold-600" for="EndTime">{{ __('languages.event.End_time') }}</label>
								<input type="text" class="form-control" placeholder="Select time" id="editeventendtime" name="endtime" value="">
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600" for="TotalHours">{{ __('languages.event.Total_hour') }}</label>
								<input type="text" class="form-control totaleventhours"id="edittotaleventhours" value="0" name="eventhours" readonly>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label for="users-list-role">{{ __('languages.Status') }}</label>
								<select class="form-control" id="status" name="status" disabled="">
									<option value="">{{ __('languages.event.Select_status') }}</option>
									<option value="0">{{ __('languages.event.Draft') }}</option>
									<option value="1">{{ __('languages.event.Published') }}</option>
									<option value="2">{{ __('languages.event.Unpublished') }}</option>
								</select>
							</div>
						</div>
						<!-- <div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label for="users-list-role">{{ __('languages.event.Occurs') }}</label>
								<fieldset class="form-group">
									<select class="form-control editoccurs" id="occurs" name="occurs" disabled="">
										<option value="">{{ __('languages.event.Select_occurs') }}</option>
										<option value="Once">{{ __('languages.event.Once') }}</option>
										<option value="Daily">{{ __('languages.event.Daily') }}</option>
										<option value="Weekly">{{ __('languages.event.Weekly') }}</option>
										<option value="Monthly">{{ __('languages.event.Monthly') }}</option>
									</select>
								</fieldset>
							</div>
						</div> -->
						<input type="text" class="form-control dailydates" id="dailydates" value="" name="dailydates" style="display: none;">
						<div class="form-row weeklyoccurs">
							<div class="form-group col-md-12 mb-50">
								<ul class="list-unstyled mb-0">
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" class="checkbox-input weeklychkcls" id="weekly1" value="0" disabled="">
												<label for="weekly1">{{ __('languages.event.Su') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly2" value="1" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly2">{{ __('languages.event.Mo') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly3" value="2" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly3">{{ __('languages.event.Tu') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly4" value="3" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly4">{{ __('languages.event.We') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly5" value="4" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly5">{{ __('languages.event.Th') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-1 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly6" value="5" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly6">{{ __('languages.event.Fr') }}</label>
											</div>
										</fieldset>
									</li>
									<li class="d-inline-block mr-2 mb-1">
										<fieldset>
											<div class="checkbox">
												<input type="checkbox" name="weekly_occurs[]" id="weekly7" value="6" class="checkbox-input weeklychkcls" disabled="">
												<label for="weekly7">{{ __('languages.event.Sa') }}</label>
											</div>
										</fieldset>
									</li>
								</ul>
							</div>
						</div>
						<input type="text" class="form-control weeklydates" id="weeklydates" value="" name="weeklydates" style="display: none;">
						<div class="form-row monthlyoccurs">
							<div class="form-group col-md-12 mb-50">
								<fieldset class="form-group">
									<select class="form-control occur-monthly-clsss" id="monthly_occurs" name="monthly_occurs">
										<option value="">{{ __('languages.event.Select_Monthly') }}</option>
										<option value="{{ $number_today_date }}/month">{{ __('languages.event.On_the') }} {{ $today_date }}</option>
										<option value="{{ $today_day }}/weekday">{{ __('languages.event.On_the_first') }} {{ $dayofweek }} {{ $today_day }}</option>
									</select>
									<div class="occur-monthly-edit-cls"></div>
								</fieldset>
							</div>
						</div>
						<input type="text" class="form-control monthly_dates_cls" id="monthdates" value="" name="monthdates" style="display: none;">
						<input type="text" class="form-control weekmonthday" id="monthweekdate" value="" name="monthweekdate" style="display: none;">
						<div class="dailyoccurs-cls">
							<div class="border-cls"></div>
							<h4>{{ __('languages.event.Ending') }}</h4>
							<div class="form-row">
								<div class="form-group col-md-12 mb-50">
									<label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.event.End Date') }}</label>
									<fieldset class="form-group position-relative has-icon-left">
										<input type="text" class="form-control" placeholder="{{ __('languages.event.Select_date') }}" id="editeventenddate" name="eventenddate"  autocomplete="off" disabled="">
										<div class="form-control-position">
											<i class='bx bx-calendar'></i>
										</div>
									</fieldset>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" name="submit" id="editsubmit_event" class="btn btn-primary" value="{{ __('languages.event.Save') }}">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection