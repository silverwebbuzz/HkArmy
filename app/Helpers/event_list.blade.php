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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.event.Events Management') }}</h3>
				</div>
			</div>
		</div>
	</div>
	<div class="content-body">
		<!-- users list start -->
		<section class="users-list-wrapper">
			<div class="users-list-filter px-1">
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
				<div class="row border rounded py-2 mb-2">
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control filter_date_event" id="filter_date_event" name="filter_date_event" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<select class="form-control" id="filter_event_type" name="filter_event_type">
								<option value="">{{ __('languages.event.Select_event_type') }} </option>
								@if(!empty($get_event_type_list))
									@php
										echo $get_event_type_list;
									@endphp
								@endif
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							{{-- <select class="form-control" id="event_status" name="event_status">
								<option value="">{{ __('languages.Status') }}</option>
								<!-- <option value="0">{{ __('languages.event.Draft') }}</option> -->
								<option value="1">{{ __('languages.event.Published') }}</option>
								<option value="2">{{ __('languages.event.Unpublished') }}</option>
								<!-- <option value="3">{{ __('languages.event.ready_for_close') }}</option> -->
								<option value="4">{{ __('languages.event.close') }}</option>
							</select> --}}
							<select class="form-control" id="event_status" name="event_status">
								<option value="">{{ __('languages.Status') }}</option>
								<!-- <option value="0">{{ __('languages.event.Draft') }}</option> -->
								<option value="1">{{ __('languages.event.Published') }}</option>
								<option value="3">{{ __('languages.event.Unpublished') }}</option>
								<!-- <option value="3">{{ __('languages.event.ready_for_close') }}</option> -->
								<option value="4">{{ __('languages.event.close') }}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<input type="text" class="form-control" id="search_text" name="search_text" placeholder="{{ __('languages.search_event_name_event_code') }}" autocomplete="off">
						</fieldset>
					</div>
					<!-- <div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<select class="form-control" id="filter_occurs" name="filter_occurs">
								<option value="">{{ __('languages.event.Select_occurs') }} </option>
								<option value="Once">Once</option>
								<option value="Daily">Daily</option>
								<option value="Weekly">Weekly</option>
								<option value="Monthly">Monthly</option>
							</select>
						</fieldset>
					</div> -->
					<div class="float-right align-items-center ml-1">
						<input type="button" class="btn btn-primary glow submit serach-events-cls" value="{{ __('languages.Submit') }} " name="submit">
					</div>
					<div class="float-right align-items-center ml-1">
						<a href="{{url("eventManagement")}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
					</div>
					@if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id'])))
					<div class="float-right align-items-center ml-1">
						<a href="{{ route('eventManagement.create') }}" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.event.Add Event') }}</a>
					</div>
					@endif
				</div>
			</div>
		
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="import-export-btn ml-1">
					{{-- <a href="{{route('import-events')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.event.events') }}</a> --}}
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-events mb-0"> {{ __('languages.export') }} {{ __('languages.event.events') }}</a>
					<!-- <a href="{{asset('uploads\sample_files\event.csv')}}">
						<button class="btn"><i class="bx bxs-download"></i>{{__('languages.download_sample_file')}}</button>
					</a> -->
				</div>
			</div>
			{{-- Export Button End --}}
			
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive event-search-list-cls">
									<table id="eventtable" class="table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="eventIds[]" class="select-all-event-chkbox" value="all">
												</th>
												<th>{{ __('languages.event.Event Name') }}</th>
												<th>{{ __('languages.event.Event Type') }}</th>
												<th>{{ __('languages.event.Event_code') }}</th>
												<th>{{ __('languages.event.Start Date') }}</th>
												<th>{{ __('languages.event.End Date') }}</th>
												<th>{{ __('languages.event.Start_time') }}</th>
												<th>{{ __('languages.event.End_time') }}</th>
												<th>{{ __('languages.event.Hours') }}</th>
												<th>{{ __('languages.event.no_of_dates') }}</th>
												@if(in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id'])))
													<th>{{ __('languages.Status') }}</th>
												@endif
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($events))
												@php
													$EventType = 'event_type_name_'.app()->getLocale();
												@endphp
												@foreach($events as $event)
												@php
													$totalhour = Helper::totalhourEvent($event['event_code']);
													$eventTypeName = Helper::getEventTypeName($event['event_type']);
												@endphp
													<tr>
														<td>
															<input type="checkbox" name="eventIds[]" class="select-event-chkbox" value="{{$event['id']}}">
														</td>
														<td>
															<a href="{{ route('eventManagement.edit',$event['id']) }}">{{ $event['event_name'] }}</a>
														</td>
														<!-- <td>{{ $event['event_type'][$EventType] ?? '' }}</td> -->
														<td>{{ $eventTypeName }}</td>
														<td>{{ $event['event_code'] }}</td>
														<td>@if(!empty($event['event_start_date'])){{ $event['event_start_date'] }} @endif</td>
														<td>@if(!empty($event['event_end_date'])){{ $event['event_end_date'] }} @endif</td>
														<td>@if(!empty($event['scheduleData'])){{ $event['scheduleData'][0]['start_time'] }} @endif</td>
														<td>@if(!empty($event['scheduleData'])){{ $event['scheduleData'][0]['end_time'] }} @endif</td>
														<td>@if(!empty($event['totaleventhour'])){{ $event['totaleventhour'] }} @endif</td>
														<td>{{ $event['no_of_dates'] }}</td>
														@if(in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id'])))
														<td>
															<select class="form-control status" id="status" >
																<option value="">{{ __('languages.event.Select_status') }}</option>
																<option value="1" data-id="{{ $event['id'] }}" @if($event['status'] == '1') selected @endif>{{ __('languages.event.Published') }}</option>
																<option value="2" data-id="{{ $event['id'] }}" @if($event['status'] == '2') selected @endif>{{ __('languages.event.Unpublished') }}</option>
																<!-- <option value="3" data-id="{{ $event['id'] }}" @if($event['status'] == '3') selected @endif>{{ __('languages.event.Ready_to_close') }}</option> -->
																<option value="4" data-id="{{ $event['id'] }}" @if($event['status'] == '4') selected @endif>{{ __('languages.event.Close_event') }}</option>
															</select>
														</td>
														@endif
														<td>
														@if($event['status'] != '1')
															@if(in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id'])))
																<a href="{{ route('eventManagement.edit',$event['id']) }}"><i class="bx bx-edit-alt"></i></a>
															@endif
														@else
														@php
															$url = URL::to('/').'/attendanceManagement?event_id='.Helper::encodekey($event['id']);
														@endphp
															<a href="{{ $url }}" title="Attendance" target="_blank"><i class="bx bxs-book-open"></i></a>
														@endif
														@if(in_array('event_management_delete', Helper::module_permission(Session::get('user')['role_id'])))
															<a href="javascript:void(0);" data-id="{{ $event['id'] }}" class="deletEvent"><i class="bx bx-trash-alt"></i> </a>
														@endif
														</td>
													</tr>
												@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<div class="modal fade assign-modal user-assign-model" id="eventAssignuser" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<form name="event-assign-user" method="post" id="event-assign-user">
				<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">{{ __('languages.event.Assign_user') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="assign-user-cls">
						<strong>{{ __('languages.event.Assign_user') }}:</strong>
						<select id="event-Assign-user" multiple="multiple" name="eventAssignuser[]">
							@if(!empty($users))
								@foreach($users as $val)
									@if( $val['UserName'] != '')
										<option value="{{ $val['ID'] }}">{{ $val['UserName'] }}</option>
									@else
										<option value="{{ $val['ID'] }}">{{ $val['English_name'] }} & {{ $val['Chinese_name'] }}</option>
									@endif
								@endforeach
							@endif
						</select>
					</div>
					<div class="assing-user-cls-error"></div>
					<input type="hidden" name="assingeventid" id="assingeventid" value="">
				</div>
				<div class="modal-footer">
					<input type="submit" class="btn btn-primary" value="{{ __('languages.event.Assign_user') }}" name="submit" id="submit">
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade assign-user-tq" id="assign-user-tq" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h3 class="align-items-center">Thank you.</h3>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary close-thnkyou " data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- footer content -->
@include('layouts.footer')

<!-- Modal -->
<div class="modal fade" id="exportEventSelectField" tabindex="-1" role="dialog" aria-labelledby="exportEventSelectField" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">{{__('languages.export_fields.select_export_fields')}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="all-event-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="event_name" checked>
						<span>{{__('languages.export_fields.events.event_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="event_type" checked>
						<span>{{__('languages.export_fields.events.event_type')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="event_code" checked>
						<span>{{__('languages.export_fields.events.event_code')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="startdate" checked>
						<span>{{__('languages.export_fields.events.event_start_date')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="enddate" checked>
						<span>{{__('languages.export_fields.events.event_end_date')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="start_time" checked>
						<span>{{__('languages.export_fields.events.event_start_time')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="end_time" checked>
						<span>{{__('languages.export_fields.events.event_end_time')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="event_hours" checked>
						<span>{{__('languages.export_fields.events.event_hours')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportEventFields[]" class="event-field-checkbox" value="no_of_dates" checked>
						<span>{{__('languages.export_fields.events.no_of_dates')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportEvents()">{{__('languages.export_fields.events.export_events')}}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportEventFieldColumnList = ['event_name','event_type','event_code','startdate','enddate','start_time','end_time','event_hours','no_of_dates'];
var EventIds = [];
$(function () {

	$(document).on("click", ".serach-events-cls", function () {
        $("#cover-spin").show();
        $.ajax({
            type: "POST",
            url: BASE_URL + "/event-list-search",
            data: {
                _token: $("#csrf-token").val(),
                filter_date_event: $("#filter_date_event").val(),
                filter_event_type: $("#filter_event_type").val(),
                filter_occurs: $("#filter_occurs").val(),
                event_status: $("#event_status").val(),
                search_text: $("#search_text").val(),
                eventIds: EventIds = [],
            },
            success: function (response) {
                $("#cover-spin").hide();
                $(".event-search-list-cls").html(response);
                $("#search-eventtable").dataTable();
                $("#eventtable").hide();
            },
        });
    });

	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-event-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#eventtable,#search-eventtable")
			.DataTable()
			.table("#eventtable,#search-eventtable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-event-chkbox").prop('checked', true);
				var eventid = row.closest('tr').find(".select-event-chkbox").val();
				if (EventIds.indexOf(eventid) !== -1) {
					// Current value is exists in array
				} else {
					EventIds.push(eventid);
				}
			});
		} else {
			$("#eventtable")
			.DataTable()
			.table("#eventtable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-event-chkbox").prop('checked', false);
			});
			EventIds = [];
		}
	});

	$(document).on("click", ".select-event-chkbox", function (){
		if($('.select-event-chkbox').length === $('.select-event-chkbox:checked').length){
			$(".select-all-event-chkbox").prop('checked',true);
		}else{
			$(".select-all-event-chkbox").prop('checked',false);
		}
		eventid = $(this).val();
		if ($(this).is(":checked")) {
			if (EventIds.indexOf(eventid) !== -1) {
				// Current value is exists in array
			} else {
				EventIds.push(eventid);
			}
		} else {
			EventIds = $.grep(EventIds, function(value) {
				return value != eventid;
			});
		}
	});

	$(document).on("click", ".export-events", function () {
		$("#exportEventSelectField").modal('show');
	});

	$(document).on("click", ".all-event-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".event-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var eventColumnName = $(this).val();
				if (ExportEventFieldColumnList.indexOf(eventColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportEventFieldColumnList.push(eventColumnName);
				}
			});
		} else {
			$(".event-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportEventFieldColumnList = [];
		}
	});

	$(document).on("click", ".event-field-checkbox", function (){
		alert($('.event-field-checkbox').length);
		if($('.event-field-checkbox').length === $('.event-field-checkbox:checked').length){
			$(".all-event-field-checkbox").prop('checked',true);
		}else{
			$(".all-event-field-checkbox").prop('checked',false);
		}
		var eventColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportEventFieldColumnList.indexOf(eventColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportEventFieldColumnList.push(eventColumnName);
			}
		} else {
			ExportEventFieldColumnList = $.grep(ExportEventFieldColumnList, function(value) {
				return value != eventColumnName;
			});
		}
	});
});

function exportEvents(){
	if($('.event-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#eventtable,#search-eventtable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/events",
			data: {
				'filter_date_event' : $('#filter_date_event').val(),
				'filter_event_type' : $('#filter_event_type').val(),
				'event_status' : $('#event_status').val(),
				'search_text' : $('#search_text').val(),
				'columnList' : ExportEventFieldColumnList,
				'eventIds' : EventIds
			},
			contentType: 'application/json; charset=utf-8',
			success: function (data) {
				//return false;
				var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
				if (!isHTML(data)) {
					var downloadLink = document.createElement("a");
					var fileData = ["\ufeff" + data];

					var blobObject = new Blob(fileData, {
						type: "text/csv;charset=utf-8;",
					});

					var url = URL.createObjectURL(blobObject);
					downloadLink.href = url;
					downloadLink.download = "Events.csv";

					document.body.appendChild(downloadLink);
					downloadLink.click();
					document.body.removeChild(downloadLink);
				}
			},
		});
	}
}
</script>
<!-- /footer content -->
@endsection
