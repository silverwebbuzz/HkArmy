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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Attendance.Attendance_Report') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
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
					<form name="event_report_form" id="event_report_form">
						<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
						<div class="row border rounded py-2 mb-2">
							<div class="col-12 col-sm-6 col-lg-2">
								<label for="users-list-role">Date</label>
								<fieldset class="form-group">
									<input type="text" class="form-control filter_date_event" id="filter_date_event" name="filter_date" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
								</fieldset>
							</div>

							<div class="col-12 col-sm-6 col-lg-2">
								<label for="users-list-role">{{ __('languages.member.Member_Name') }}</label>
								<fieldset class="form-group">
									<select class="form-control" id="member_name" name="member_name">
										@if($users)
										<option value="">{{ __('languages.member.Select_Member_Name') }}</option>
											@foreach($users as $val)
												@if(!empty($val['UserName']))
													<option value="{{ $val['ID'] }}">{{ $val['UserName'] }}</option>
												@else
													<option value="{{ $val['ID'] }}">{{ $val['Chinese_name'] }} & {{ $val['English_name'] }}</option>
												@endif
											@endforeach
										@endif
									</select>
								</fieldset>
							</div>
							<div class="col-12 col-sm-6 col-lg-2">
								<label for="users-list-role">{{ __('languages.event.Event Name') }}</label>
								<fieldset class="form-group">
									<select class="form-control" id="event_name" name="event_name">
										<option value="">{{ __('languages.event.Select_Event_Name') }}</option>
										@if(!empty($events))
											@foreach($events as $val)
												<option value="{{ $val['id'] }}">{{ $val['event_name'] }} - {{ $val['occurs'] }}</option>
											@endforeach
										@endif
									</select>
								</fieldset>
							</div>
							<div class="col-12 col-sm-6 col-lg-2">
								<label for="users-list-status">{{ __('languages.event.Event Type') }}</label>
								<fieldset class="form-group">
									<select class="form-control" id="event_type" name="event_type">
										<option value="">{{ __('languages.event.Select_event_type') }} </option>
										@if(!empty($get_event_type_list))
											@php
												echo $get_event_type_list;
											@endphp
										@endif
									</select>
								</fieldset>
							</div>
							<button type="button" class="btn btn-primary glow event_report_search d-flex align-items-center">{{ __('languages.Search') }}</button>
							<a href="{{ url('attendance-report') }}" class="btn btn-primary glow clear-cls d-flex align-items-center">{{ __('languages.Clear') }}</a>
						</div>
					</form>
				</div>
				{{-- Export Button Start  --}}
				<div class="row mb-2">
					<div class="float-right align-items-center ml-1">
						<a href="javascript:void(0);" class="btn btn-primary btn-block glow export_attendance_event_member mb-0"> {{ __('languages.export') }} {{ __('languages.Attendance.attendance_event_member') }}</a>
					</div>
				</div>
				{{-- Export Button End --}}
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive event-serach-data-cls">
									<table id="attendanceReporttable" class="table event-report">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="attendanceIds[]" class="select-all-attendance-chkbox" value="all">
												</th>
												<th>{{ __('languages.event.Event Name') }}</th>
												<th>{{ __('languages.event.Event Type') }}</th>
												<th>{{ __('languages.Attendance.Event Date') }}</th>
												<!-- <th>{{ __('languages.Attendance.Member_Name') }}</th> -->
												<th>{{ __('languages.member.English_name') }}</th>
												<th>{{ __('languages.member.Chinese_name') }}</th>
												<th>{{ __('languages.training_hours') }}</th>
												<th>{{ __('languages.activity_hours') }}</th>
												<th>{{ __('languages.service_hours') }}</th>
												<!-- <th>{{ __('languages.Attendance.Used Hour') }}</th>
												<th>{{ __('languages.Attendance.Remaining Hours') }}</th>
												<th>{{ __('languages.Attendance.Total Hour') }}</th> -->
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if($attendancesreport)
											@php
												$EventType = 'event_type_name_'.app()->getLocale();
											@endphp
											@foreach($attendancesreport as $key => $val)
											@if(!empty($val['users']))
												@if(!empty($val['event']['event_name']))
												<tr>
													<td>
														<input type="checkbox" name="attendanceIds[]" class="select-attendance-chkbox" value="{{$val['id']}}">
													</td>
													<td>{{$val['event']['event_name'] }}</td>
													<td>{{ $val['event_type'][$EventType] }}</td>
													<td>{{ Helper::dateConvertDDMMYYY(',','',$val['event']['startdate']) }}</td>
													<!-- @if(!empty($val['users']['UserName']))
														<td>{{ $val['users']['UserName'] }}</td>
													@else
														<td>{{ $val['users']['Chinese_name'] }} & {{ $val['users']['English_name'] }}</td>
													@endif -->
													<td>{{ ($val['users']['English_name']) ? $val['users']['English_name'] : '' }}</td>
													<td>{{ ($val['users']['Chinese_name']) ? $val['users']['Chinese_name'] : '' }}</td>
													<td>{{ ($val['training_hour'] != '00:00' && $val['training_hour'] != '0:00') ? $val['training_hour'] : '---' }}</td>
													<td>{{ ($val['activity_hour'] != '00:00' && $val['activity_hour'] != '0:00') ? $val['activity_hour'] : '---' }}</td>
													<td>{{ ($val['service_hour'] != '00:00' && $val['service_hour'] != '0:00') ? $val['service_hour'] : '---' }}</td>
													<!-- <td>{{ $val['hours'] }}</td>
													<td>{{ $val['remaining_hour'] }}</td>
													<td>{{ $val['users']['hour_point'] }}</td> -->
													<td>
														<a href="{{ url('attendance-report-detail',$val['id']) }}"><i class="bx bx-show-alt"></i></a>
													</td>
												</tr>
												@endif
												@endif
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
@include('layouts.footer')
<!-- Modal -->
<div class="modal fade" id="exportAttendanceEventMemberSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAttendanceEventMemberSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="all-AttendanceEventMember-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						{{-- <input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="evnet_name" checked> --}}
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="event_name" checked>
						<span>{{ __('languages.event.Event Name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="event_type" checked>
						<span>{{ __('languages.event.Event Type') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="event_date" checked>
						<span>{{ __('languages.Attendance.Event Date') }}</span>
					</div>
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.Attendance.Member_Name') }}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="english_name" checked>
						<span>{{ __('languages.member.English_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="chinese_name" checked>
						<span>{{ __('languages.member.Chinese_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="training_hour" checked>
						<span>{{ __('languages.training_hours') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="activity_hour" checked>
						<span>{{ __('languages.activity_hours') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceEventMemberFields[]" class="AttendanceEventMember-field-checkbox" value="service_hour" checked>
						<span>{{ __('languages.service_hours') }}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAttendanceEventMember()">{{ __('languages.export') }} {{ __('languages.Attendance.attendance_event_member') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportAttendanceEventMemberFieldColumnList = ['event_name','event_type','event_date','english_name','chinese_name','training_hour','activity_hour','service_hour'];
var AttendaceMemberIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-attendance-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#attendanceReporttable")
			.DataTable()
			.table("#attendanceReporttable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-attendance-chkbox").prop('checked', true);
				var attendanceMemberId = row.closest('tr').find(".select-attendance-chkbox").val();
				if (AttendaceMemberIds.indexOf(attendanceMemberId) !== -1) {
					// Current value is exists in array
				} else {
					AttendaceMemberIds.push(attendanceMemberId);
				}
			});
		} else {
			$("#attendanceReporttable")
			.DataTable()
			.table("#attendanceReporttable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-attendance-chkbox").prop('checked', false);
			});
			AttendaceMemberIds = [];
		}
	});

	$(document).on("click", ".select-attendance-chkbox", function (){
		if($('.select-attendance-chkbox').length === $('.select-attendance-chkbox:checked').length){
			$(".select-all-attendance-chkbox").prop('checked',true);
		}else{
			$(".select-all-attendance-chkbox").prop('checked',false);
		}
		attendanceMemberId = $(this).val();
		if ($(this).is(":checked")) {
			if (AttendaceMemberIds.indexOf(attendanceMemberId) !== -1) {
				// Current value is exists in array
			} else {
				AttendaceMemberIds.push(attendanceMemberId);
			}
		} else {
			AttendaceMemberIds = $.grep(AttendaceMemberIds, function(value) {
				return value != attendanceMemberId;
			});
		}
	});

	$(document).on("click", ".export_attendance_event_member", function () {
		$("#exportAttendanceEventMemberSelectField").modal('show');
	});

	$(document).on("click", ".all-AttendanceEventMember-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".AttendanceEventMember-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var attendanceTransactionColumnList = $(this).val();
				if (ExportAttendanceEventMemberFieldColumnList.indexOf(attendanceTransactionColumnList) !== -1) {
					// Current value is exists in array
				} else {
					ExportAttendanceEventMemberFieldColumnList.push(attendanceTransactionColumnList);
				}
			});
		} else {
			$(".AttendanceEventMember-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportAttendanceEventMemberFieldColumnList = [];
		}
	});

	$(document).on("click", ".AttendanceEventMember-field-checkbox", function (){
		if($('.AttendanceEventMember-field-checkbox').length === $('.AttendanceEventMember-field-checkbox:checked').length){
			$(".all-AttendanceEventMember-field-checkbox").prop('checked',true);
		}else{
			$(".all-AttendanceEventMember-field-checkbox").prop('checked',false);
		}
		var attendanceTransactionColumnList = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportAttendanceEventMemberFieldColumnList.indexOf(attendanceTransactionColumnList) !== -1) {
				// Current value is exists in array
			} else {
				ExportAttendanceEventMemberFieldColumnList.push(attendanceTransactionColumnList);
			}
		} else {
			ExportAttendanceEventMemberFieldColumnList = $.grep(ExportAttendanceEventMemberFieldColumnList, function(value) {
				return value != attendanceTransactionColumnList;
			});
		}
	});
});

function exportAttendanceEventMember(){
	if($('.AttendanceEventMember-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#attendanceReporttable,#attendanceSerachReporttable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/attendance-event-member",
			data: {
				'columnList' : ExportAttendanceEventMemberFieldColumnList,
				'filter_date' : $('#filter_date_event').val(),
				'member_name' : $('#member_name').val(),
				'event_name' : $('#event_name').val(),
				'event_type' : $('#event_type').val(),
				'attendaceMemberIds' : AttendaceMemberIds
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
					downloadLink.download = "AttendaceEventMember.csv";

					document.body.appendChild(downloadLink);
					downloadLink.click();
					document.body.removeChild(downloadLink);
				}
			},
		});
	}
}
</script>
@endsection