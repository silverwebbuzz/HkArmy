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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.sidebar.Assign User Report') }}</h3>
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
				</div>
				<div class="row border rounded py-2 mb-2">
					<form action="{{ route('assign-user-report') }}" method="GET">
						<!-- <input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}"> -->
						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group position-relative has-icon-left">
								<input type="text" class="form-control filter_date_enroll" id="filter_date_attendance" name="filter_date_attendance" placeholder="{{ __('languages.Select_date') }}" autocomplete="off" value="@if(isset($_GET['filter_date_attendance']) && !empty($_GET['filter_date_attendance'])) {{ $_GET['filter_date_attendance'] }} @endif">
								<div class="form-control-position">
									<i class="bx bx-calendar-check"></i>
								</div>
							</fieldset>
						</div>
						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group">
								<select class="form-control filter_event_type" name="filter_event_type" id="filter_event_type">
									<option value="">{{ __('languages.event.Select_event_type') }} </option>
									@if(!empty($get_event_type_list))
									@php
									echo $get_event_type_list;
									@endphp
									@endif
								</select>
							</fieldset>
						</div>
						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group">
								<select class="form-control" name="filter_event_id" id="filter_event_id">
									<option value="">{{ __('languages.event.Select_Event') }} </option>
									@if(!empty($EventList1))
									@foreach($EventList1 as $Events) 
									<option value="{{$Events['id']}}"  @if(isset($_GET['filter_event_id']) && $Events['id'] == $_GET['filter_event_id']) selected @endif>{{ $Events['event_name'] }}</option>
									@endforeach
									@endif
								</select>
							</fieldset>
						</div>
						<div class="float-right align-items-center ml-1">
							<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Search') }} " name="search">
						</div>
						<div class="float-right align-items-center ml-1">
							<a href="{{url("assign-user-report")}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
						</div>
					</form>
				</div>
				{{-- Export Button Start --}}
				<div class="row mb-2">
					<div class="float-right align-items-center ml-1">
						<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-assign-user-report mb-0"> {{__('languages.export')}} {{ __('languages.sidebar.Assign User Report') }}</a>
					</div>
				</div>
				{{-- Export Button End --}}

				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="assignUserReport" class="table assignUserReportTbl">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="allAwardAssignMemberIDs[]" class="select-all-award-assign-member-list-chkbox" id="select-all-award-assign-member-list-chkbox" value="all">
												</th>
												<th>{{ __('languages.RoleManagement.Sr_No') }}</th>
												<th>{{ __('languages.event.Event_code') }}</th>
												<th>{{ __('languages.event.Event Name') }}</th>
												<th>{{ __('languages.cost_method') }}</th>
												<th>{{ __('languages.event.Date') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($EventList))
											@foreach($EventList as $event)
											<tr>
												<td>
													<input type="checkbox" name="AwardAssignIDs[]" class="select-award-assign-member-list-chkbox" id= "select-award-assign-member-list-chkbox" value="{{$event['id']}}">
												</td>
												<td>{{ $event['id'] }}</td>
												<td>{{ $event['event_code'] }}</td>
												<td>{{ $event['event_name'] }}</td>
												<td>
													<ul>
													<?php
													if(isset($event['id']) && !empty($event['id'])){
														echo Helper::getEventCostTypeHtml($event['id']);
													}
													// if(isset($event['event_cost_type']) && !empty($event['event_cost_type'])){
													// 	foreach($event['event_cost_type'] as $eventCostType){
													// 		if($eventCostType['post_type'] == 1){
													// 			echo '<li>'.__('languages.member.Money').' : '.$eventCostType['post_value'].'</li>';
													// 		}
													// 		if($eventCostType['post_type'] == 2){
													// 			echo '<li>'.__('languages.member.Tokens').' : '.$eventCostType['post_value'].'</li>';
													// 		}
													// 		if($eventCostType['post_type'] == 3){
													// 			$explodeEventCostType = explode("+",$eventCostType['post_value']);
													// 			echo '<li>'.__('languages.member.Money').' : '.$explodeEventCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeEventCostType[1].'</li>';
													// 		}
													// 	}
													// }
													?>
													</ul>
												</td>
												<td>{{ Helper::dateConvertDDMMYYY('/','-',$event['event_start_date']) }}</td>
												<td>
													<button type="button" class="btn btn-outline-success block deleteAttenderMember" data-toggle="modal" data-backdrop="false" data-target="#backdrop1" modal-event-id="{{ $event['id'] }}" modal-event-name="{{ $event['event_name'] }}" modal-event-code="{{$event['event_code']}}">
														{{ __('languages.member.View Member') }}
													</button>
													<button type="button" class="btn btn-outline-primary block addMember" data-toggle="modal" data-backdrop="false" data-target="#backdrop" modal-event-id="{{ $event['id'] }}" modal-event-name="{{ $event['event_name'] }}">
														{{ __('languages.Add Member') }}
													</button>
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
				</section>
			</div>
		</div>
	</div>

	<!-- Modal -->
<div class="modal fade" id="exportAssignUserReportSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAssignUserReportSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportAssignUserReportFields[]" class="all-AssignUserReport-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="AssignUserReport-field-checkbox" value="event_code" checked>
						<span>{{__('languages.export_fields.events.event_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="AssignUserReport-field-checkbox" value="event_name" checked>
						<span>{{__('languages.export_fields.events.event_type')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="AssignUserReport-field-checkbox" value="cost_method" checked>
						<span>{{__('languages.export_fields.events.event_code')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="AssignUserReport-field-checkbox" value="date" checked>
						<span>{{__('languages.export_fields.events.event_start_date')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAssignUserReport()">{{__('languages.export')}} {{ __('languages.sidebar.Assign User Report') }}</button>
			</div>
		</div>
	</div>
</div>


	<!-- footer content -->
	@include('layouts.footer')
<script>
	var ExportAssignUserReportFieldColumnList = ['event_code','event_name','cost_method','date'];
	var AssignUserReportIds = [];
	$(function () {
		// On click on checkbox eventlist 
		$(document).on("click", ".select-all-award-assign-member-list-chkbox", function (){
			if ($(this).is(":checked")) {
				$("#assignUserReport")
				.DataTable()
				.table("#assignUserReport")
				.rows()
				.every(function (index, element) {
					var row = $(this.node());
					row.closest('tr').find(".select-award-assign-member-list-chkbox").prop('checked', true);
					var eventid = row.closest('tr').find(".select-award-assign-member-list-chkbox").val();
					if (AssignUserReportIds.indexOf(eventid) !== -1) {
						// Current value is exists in array
					} else {
						AssignUserReportIds.push(eventid);
					}
				});
			} else {
				$("#assignUserReport")
				.DataTable()
				.table("#assignUserReport")
				.rows()
				.every(function (index, element) {
					var row = $(this.node());
					row.closest('tr').find(".select-award-assign-member-list-chkbox").prop('checked', false);
				});
				AssignUserReportIds = [];
			}
		});
	
		$(document).on("click", ".select-award-assign-member-list-chkbox", function (){
		if($('.select-award-assign-member-list-chkbox').length === $('.select-award-assign-member-list-chkbox:checked').length){
			$(".select-all-award-assign-member-list-chkbox").prop('checked',true);
		}else{
			$(".select-all-award-assign-member-list-chkbox").prop('checked',false);
		}
		assignUserid = $(this).val();
		if ($(this).is(":checked")) {
			if (AssignUserReportIds.indexOf(assignUserid) !== -1) {
				// Current value is exists in array
			} else {
				AssignUserReportIds.push(assignUserid);
			}
		} else {
			AssignUserReportIds = $.grep(AssignUserReportIds, function(value) {
				return value != assignUserid;
			});
		}
	});

		$(document).on("click", ".export-assign-user-report", function () {
			$("#exportAssignUserReportSelectField").modal('show');
		});
	
		$(document).on("click", ".all-AssignUserReport-field-checkbox", function (){
			if ($(this).is(":checked")) {
				$(".AssignUserReport-field-checkbox").each(function () {
					$(this).prop('checked', true);
					var AssignUserReportColumnName = $(this).val();
					if (ExportEventFieldColumnList.indexOf(AssignUserReportColumnName) !== -1) {
						// Current value is exists in array
					} else {
						ExportEventFieldColumnList.push(AssignUserReportColumnName);
					}
				});
			} else {
				$(".AssignUserReport-field-checkbox").each(function () {
					$(this).prop('checked',false);
				});
				ExportEventFieldColumnList = [];
			}
		});
	
		$(document).on("click", ".AssignUserReport-field-checkbox", function (){
			if($('.AssignUserReport-field-checkbox').length === $('.AssignUserReport-field-checkbox:checked').length){
				$(".all-AssignUserReport-field-checkbox").prop('checked',true);
			}else{
				$(".all-AssignUserReport-field-checkbox").prop('checked',false);
			}
			var assignUserReportColumnName = $(this).val();
			if ($(this).is(":checked")) {
				if (ExportEventFieldColumnList.indexOf(assignUserReportColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportEventFieldColumnList.push(assignUserReportColumnName);
				}
			} else {
				ExportEventFieldColumnList = $.grep(ExportEventFieldColumnList, function(value) {
					return value != assignUserReportColumnName;
				});
			}
		});

		$(document).on("click", ".export-assign-member-report", function () {
			$("#exportViewMemberReportSelectField").modal('show');
		});
	});

	
	function exportAssignUserReport(){
		if($('.AssignUserReport-field-checkbox:checked').length === 0){
			toastr.error('Please select atleast one column for export csv');
		}else if(! $("#assignUserReport").DataTable().data().count()){
			toastr.error('No data available in table');
		}else{
			$.ajax({
				type: "GET",
				url: BASE_URL + "/export/assign-user-report",
				data: {
					'filter_date_attendance' : $('#filter_date_attendance').val(),
					'filter_event_type' : $('#filter_event_type').val(),
					'filter_event' : $('#filter_event_id').val(),
					'columnList' : ExportAssignUserReportFieldColumnList,
					'assignUserReportIds' : AssignUserReportIds
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
						downloadLink.download = "AssignUserReport.csv";
	
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

	<!--Disabled Backdrop Modal -->
	<div class="modal fade text-left addmember_modal" id="backdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel4">{{ __('languages.Add Member') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="bx bx-x"></i>
					</button>
				</div>
				<div class="modal-body">
					<p>
						<span class="modal-event-text">{{ __('languages.event.Event_code') }}:</span><span class="modal-event-code"></span>
						<br>
						<span class="modal-event-text">{{ __('languages.Event name') }}:</span><span class="modal-event-name"></span>
						<div class="form-row addmember_modal_form">
							<div class="form-group">
								<input type="hidden" name="eventModal" value="">
								<label for="users-list-role">{{ __('languages.Member') }}</label>
								<select class="form-control" id="membermodal" name="membermodal">
									<option value="">Select Member</option>
								</select>
							</div>
						</div>


					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-secondary" data-dismiss="modal">
						<i class="bx bx-x d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
					</button>
					<button type="button" class="btn btn-primary ml-1 assignMemberToEvent">
						<i class="bx bx-check d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Save') }}</span>
					</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade text-left addmember_modal" id="backdrop1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel4">{{ __('languages.member.View Member') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="bx bx-x"></i>
					</button>
				</div>
				<div class="modal-body">
					<p>
						<span class="modal-event-text">{{ __('languages.event.Event_code') }}:</span> <span class="modal-event-code"></span>
						<br>
						<span class="modal-event-text">{{ __('languages.Event name') }}:</span><span class="modal-event-name"></span>
						<!-- <button type="button" class="btn btn-outline-primary block addMember viewAddMember" data-toggle="modal" data-backdrop="false" data-target="#backdrop" modal-event-id="" modal-event-name="">
							{{ __('languages.Add Member') }}
						</button> -->
						<input type="hidden" value="" id="hiddeneventId">
						<div class="add_member_modal_form">
							<div class="form-group">
								<input type="hidden" name="eventModal" value="">
								<label for="users-list-role">{{ __('languages.Add Member') }}</label>
								<select class="form-control" id="membermodal1" name="membermodal1">
									<option value="">{{ __('languages.PurchaseProduct.Select_member') }}</option>
								</select>
							</div>
							<button type="button" class="btn btn-primary ml-1 assign_member_event">
								<i class="bx bx-check d-block d-sm-none"></i>
								<span class="d-none d-sm-block">{{ __('languages.Save') }}</span>
							</button>
						</div>
						<div class="table-responsive viewAssignMember">
						</div>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-secondary" data-dismiss="modal">
						<i class="bx bx-x d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="exportViewMemberReportSelectField" tabindex="-1" role="dialog" aria-labelledby="exportViewMemberReportSelectField" data-backdrop="static" aria-hidden="true">
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
							<input type="checkbox" name="exportViewMemberReportFields[]" class="all-viewMemberReport-field-checkbox" value="all" checked>
							<span>{{__('languages.export_fields.all_fields')}}</span>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="member_number" checked>
							<span>{{__('languages.export_fields.view_members.member_number')}}</span>
						</div>
						<!-- <div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="member_name" checked>
							<span>{{__('languages.export_fields.view_members.member_name')}}</span>
						</div> -->
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="english_name" checked>
							<span>{{__('languages.export_fields.view_members.English_name')}}</span>
						</div>
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="chinese_name" checked>
							<span>{{__('languages.export_fields.view_members.Chinese_name')}}</span>
						</div>
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="cost_method" checked>
							<span>{{__('languages.export_fields.view_members.cost_method')}}</span>
						</div>
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="remarks" checked>
							<span>{{__('languages.export_fields.view_members.remarks')}}</span>
						</div>
						<div class="col-md-6">
							<input type="checkbox" name="exportViewMemberReportFields[]" class="viewMemberReport-field-checkbox" value="status" checked>
							<span>{{__('languages.export_fields.view_members.status')}}</span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
					<button type="button" class="btn btn-primary" onClick="exportAssignedViewMemberReport()">{{__('languages.sidebar.Assign User Report')}}</button>
				</div>
			</div>
		</div>
	</div>
<script>
	var ExportViewMemberReportFieldColumnList = ['member_number','english_name','chinese_name','cost_method','remarks','status'];
	var AssignViewMemberReportIds = [];
	$(function () {
		$(document).on("click", ".select-all-view-member-chkbox", function (){
			if ($(this).is(":checked")) {
				$("#assignMemberList")
				.DataTable()
				.table("#assignMemberList")
				.rows()
				.every(function (index, element) {
					var row = $(this.node());
					row.closest('tr').find(".select-view-member-chkbox").prop('checked', true);
					var memberRecordId = row.closest('tr').find(".select-view-member-chkbox").val();
					if (AssignViewMemberReportIds.indexOf(memberRecordId) !== -1) {
						// Current value is exists in array
					} else {
						AssignViewMemberReportIds.push(memberRecordId);
					}
				});
			} else {
				$("#assignMemberList")
				.DataTable()
				.table("#assignMemberList")
				.rows()
				.every(function (index, element) {
					var row = $(this.node());
					row.closest('tr').find(".select-view-member-chkbox").prop('checked', false);
				});
				AssignViewMemberReportIds = [];
			}
		});

		$(document).on("click", ".select-view-member-chkbox", function (){
			memberRecordId = $(this).val();
			if ($(this).is(":checked")) {
				if (AssignViewMemberReportIds.indexOf(memberRecordId) !== -1) {
					// Current value is exists in array
				} else {
					AssignViewMemberReportIds.push(memberRecordId);
				}
			} else {
				AssignViewMemberReportIds = $.grep(AssignViewMemberReportIds, function(value) {
					return value != memberRecordId;
				});
			}
		});

		$(document).on("click", ".all-viewMemberReport-field-checkbox", function (){
			if ($(this).is(":checked")) {
				$(".viewMemberReport-field-checkbox").each(function () {
					$(this).prop('checked', true);
					var viewMemberColumnName = $(this).val();
					if (ExportViewMemberReportFieldColumnList.indexOf(viewMemberColumnName) !== -1) {
						// Current value is exists in array
					} else {
						ExportViewMemberReportFieldColumnList.push(viewMemberColumnName);
					}
				});
			} else {
				$(".viewMemberReport-field-checkbox").each(function () {
					$(this).prop('checked',false);
				});
				ExportViewMemberReportFieldColumnList = [];
			}
		});

		$(document).on("click", ".viewMemberReport-field-checkbox", function (){
			if($('.all-viewMemberReport-field-checkbox').length === $('.viewMemberReport-field-checkbox:checked').length){
				$(".all-viewMemberReport-field-checkbox").prop('checked',true);
			}else{
				$(".all-viewMemberReport-field-checkbox").prop('checked',false);
			}
			var viewMemberColumnName = $(this).val();
			if ($(this).is(":checked")) {
				if (ExportViewMemberReportFieldColumnList.indexOf(viewMemberColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportViewMemberReportFieldColumnList.push(viewMemberColumnName);
				}
			} else {
				ExportViewMemberReportFieldColumnList = $.grep(ExportViewMemberReportFieldColumnList, function(value) {
					return value != viewMemberColumnName;
				});
			}
		});

		$(document).on("click", ".deleteAttenderMember", function () {
        	AssignViewMemberReportIds = [];
		});

		/** 
		 * USE : Remove multiple members to enrollment event order list
		 */
		$(document).on("click", ".remove-assigned-event-members", function () {
			var event_id = $("#hiddeneventId").val();
			if (AssignViewMemberReportIds != "") {
				if (confirm(ARE_YOU_SURE_WANT_TO_CONFIRM_THIS)) {
					$.ajax({
						type: "GET",
						url: BASE_URL + "/delete-enrollment-event-member",
						data: {
							recordIds: AssignViewMemberReportIds,
							event_id: event_id
						},
						success: function (response) {
							$("#cover-spin").css("display", "none");
							var object = JSON.parse(JSON.stringify(response));
							if(object.status){
								toastr.success(object.message);
							}else{
								toastr.error(object.message);
							}
							$(".viewAssignMember").html(object.html);
							$("#assignMemberList").dataTable();
						},
					});
				}else{
					$("#cover-spin").css("display", "none");
					return false;
				}
			}else{
				toastr.error(VALIDATIONS.PLEASE_SELECT_MEMBER);
			}
		});

		/**
		 * USE : Update status of the event enrollment order
		 */
		$(document).on("change", ".select_event_assign_status", function () {
			console.log('AssignViewMemberReportIds',AssignViewMemberReportIds);
			var status = $(this).children("option:selected").val();
			var event_id = $("#hiddeneventId").val();
			if (AssignViewMemberReportIds != "") {
				if (confirm(ARE_YOU_SURE_WANT_TO_CONFIRM_THIS)) {
					$.ajax({
						type: "GET",
						url: BASE_URL + "/selecteventAssignStatusUpdate",
						data: {
							status: status,
							_token: $('meta[name="_token"]').attr("content"),
							id: AssignViewMemberReportIds,
							event_id: event_id,
						},
						success: function (response) {
							$("#cover-spin").css("display", "none");
							var object = JSON.parse(JSON.stringify(response));
							if (object.status) {
								toastr.success(object.message);
							} else {
								toastr.error(object.message);
							}
							$(".viewAssignMember").html(object.html);
							$("#assignMemberList").dataTable();
						},
					});
				} else {
					$("#cover-spin").css("display", "none");
					return false;
				}
			} else {
				toastr.error(VALIDATIONS.PLEASE_SELECT_MEMBER);
			}
		});
	});

	function exportAssignedViewMemberReport(){
		if($('.viewMemberReport-field-checkbox:checked').length === 0){
        	toastr.error('Please select atleast one column for export csv');
		}else{
			$.ajax({
				type: "GET",
				url: BASE_URL + "/export/assignedevents/members",
				data: {
					'columnList' : ExportViewMemberReportFieldColumnList,
					'recordIds' : AssignViewMemberReportIds,
					'mainEventId' : $("#hiddeneventId").val()
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
						downloadLink.download = "AssignedEventsMemberList.csv";

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


	