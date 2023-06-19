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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.sidebar.Transaction History') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- users list start -->
			<section class="users-list-wrapper">
				<div class="users-list-filter px-1">
<!-- <div class="row border rounded py-2 mb-2">
<div class="float-right align-items-center ml-1">
<a href="{{ route('team.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.Team.Add_Team') }}</a>
</div>
</div> -->
</div>
{{-- Export Button Start --}}
<div class="row mb-2">
	<div class="float-right align-items-center ml-1">
		<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-transactions mb-0"> {{ __('languages.export') }} {{ __('languages.transaction') }}</a>
	</div>
</div>
{{-- Export Button End --}}
<div class="users-list-table">
	<div class="card">
		<div class="card-content">
			<div class="card-body">
				<div class="table-responsive">
					<table id="qualificationstable" class="table">
						<thead>
							<tr>
								<th>
									<input type="checkbox" name="allmemberUsedTokenIDs[]" class="select-all-memberUsedToken-chkbox" value="all">
								</th>
								<th>{{ __('languages.Sr_No') }}</th>
								{{-- <th>{{ __('languages.member.Member_Name') }}</th> --}}
								<th>{{ __('languages.member.English_name') }}</th>
								<th>{{ __('languages.member.Chinese_name') }}</th>
								<th>{{ __('languages.event.Event Name') }}</th>
								<th>{{ __('languages.member.Tokens') }}</th>
								<th>{{ __('languages.member.Money') }}</th>
								<th>{{ __('languages.event.Date') }}</th>
							</tr>
						</thead>
						<tbody>
							@if(!empty($transactionHistory))
							@foreach($transactionHistory as $val)

							{{-- @if(app()->getLocale() == 'ch')
							@php
							$user_name = $val['users']['Chinese_name'];
							@endphp
							@else
							@php
							$user_name = $val['users']['English_name'];
							@endphp
							@endif --}}
							<tr>
								<td>
									<input type="checkbox" name="memberUsedTokenIDs[]" class="select-memberUsedToken-chkbox" value="{{$val['id']}}">
								</td>
								<td>{{$val['id']}}</td>
								<td>{{ $val['users']['English_name'] ?? '' }}</td>
								<td>{{ $val['users']['Chinese_name'] ?? '' }}</td>
								<td>@if(!empty($val['event']['event_name'])){{ $val['event']['event_name'] }}@else - @endif</td>
								<td>@if(!empty($val['token'])){{ $val['token'] }}@else - @endif</td>
								<td>@if(!empty($val['money'])){{ $val['money'] }}@else - @endif</td>
								<th>{{ Helper::dateConvertDDMMYYY('-','/',$val['created_at'])}}</th>
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

<!-- footer content -->
@include('layouts.footer')
<!-- Modal -->
<div class="modal fade" id="exportAttendanceTransactionSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAttendanceTransactionSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="all-AttendanceTransaction-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="member_name" checked>
						<span>{{__('languages.member.Member_Name')}}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="english_name" checked>
						<span>{{__('languages.member.English_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="chinese_name" checked>
						<span>{{__('languages.member.Chinese_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="event_name" checked>
						<span>{{__('languages.event.Event Name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="tokens" checked>
						<span>{{__('languages.member.Tokens')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="money" checked>
						<span>{{__('languages.member.Money')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAttendanceTransactionFields[]" class="AttendanceTransaction-field-checkbox" value="date" checked>
						<span>{{__('languages.event.Date')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAttendanceTransactions()">{{ __('languages.export') }} {{ __('languages.transaction') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportAttendanceTransactionFieldColumnList = ['english_name','chinese_name','event_name','tokens','money','date'];
var TransactionIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-memberUsedToken-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-memberUsedToken-chkbox").prop('checked', true);
				var transactionid = row.closest('tr').find(".select-memberUsedToken-chkbox").val();
				if (TransactionIds.indexOf(transactionid) !== -1) {
					// Current value is exists in array
				} else {
					TransactionIds.push(transactionid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-memberUsedToken-chkbox").prop('checked', false);
			});
			TransactionIds = [];
		}
	});

	$(document).on("click", ".select-memberUsedToken-chkbox", function (){
		if($('.select-memberUsedToken-chkbox').length === $('.select-memberUsedToken-chkbox:checked').length){
			$(".select-all-memberUsedToken-chkbox").prop('checked',true);
		}else{
			$(".select-all-memberUsedToken-chkbox").prop('checked',false);
		}
		transactionid = $(this).val();
		if ($(this).is(":checked")) {
			if (TransactionIds.indexOf(transactionid) !== -1) {
				// Current value is exists in array
			} else {
				TransactionIds.push(transactionid);
			}
		} else {
			TransactionIds = $.grep(TransactionIds, function(value) {
				return value != transactionid;
			});
		}
	});

	$(document).on("click", ".export-transactions", function () {
		$("#exportAttendanceTransactionSelectField").modal('show');
	});

	$(document).on("click", ".all-AttendanceTransaction-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".AttendanceTransaction-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var attendanceTransactionColumnList = $(this).val();
				if (ExportAttendanceTransactionFieldColumnList.indexOf(attendanceTransactionColumnList) !== -1) {
					// Current value is exists in array
				} else {
					ExportAttendanceTransactionFieldColumnList.push(attendanceTransactionColumnList);
				}
			});
		} else {
			$(".AttendanceTransaction-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportAttendanceTransactionFieldColumnList = [];
		}
	});

	$(document).on("click", ".AttendanceTransaction-field-checkbox", function (){
		if($('.AttendanceTransaction-field-checkbox').length === $('.AttendanceTransaction-field-checkbox:checked').length){
			$(".all-AttendanceTransaction-field-checkbox").prop('checked',true);
		}else{
			$(".all-AttendanceTransaction-field-checkbox").prop('checked',false);
		}
		var attendanceTransactionColumnList = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportAttendanceTransactionFieldColumnList.indexOf(attendanceTransactionColumnList) !== -1) {
				// Current value is exists in array
			} else {
				ExportAttendanceTransactionFieldColumnList.push(attendanceTransactionColumnList);
			}
		} else {
			ExportAttendanceTransactionFieldColumnList = $.grep(ExportAttendanceTransactionFieldColumnList, function(value) {
				return value != attendanceTransactionColumnList;
			});
		}
	});
});

function exportAttendanceTransactions(){
	if($('.AttendanceTransaction-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#qualificationstable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/attendance-transaction",
			data: {
				'columnList' : ExportAttendanceTransactionFieldColumnList,
				'transactionIds' : TransactionIds
			},
			contentType: 'application/json; charset=utf-8',
			success: function (data) {
				var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
				if (!isHTML(data)) {
					var downloadLink = document.createElement("a");
					var fileData = ["\ufeff" + data];

					var blobObject = new Blob(fileData, {
						type: "text/csv;charset=utf-8;",
					});

					var url = URL.createObjectURL(blobObject);
					downloadLink.href = url;
					downloadLink.download = "Roles.csv";
					exportHistroy('Transactions', url, fileData);

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