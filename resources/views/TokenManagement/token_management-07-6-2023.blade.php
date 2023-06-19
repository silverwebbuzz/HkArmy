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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.sidebar.Token Management') }}</h3>
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
				{{-- Export Button Start --}}
				<div class="row mb-2">
					<div class="float-right align-items-center ml-1">
						<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-token mb-0"> {{ __('languages.export') }} {{ __('languages.Token Management.token') }}</a>
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
													<input type="checkbox" name="allTokenIDs[]" class="select-all-Token-chkbox" value="all">
												</th>
												<th>{{ __('languages.Sr_No') }}</th>
												{{-- <th>{{ __('languages.member.Member_Name') }}</th> --}}
												<th>{{ __('languages.member.English_name') }}</th>
												<th>{{ __('languages.member.Chinese_name') }}</th>
												<th>{{ __('languages.event.Event Name') }}</th>
												<th>{{ __('languages.member.Tokens') }}</th>
												<th>{{__('languages.used_token')}}</th>
												<th>{{ __('languages.Token Management.Remaining Token') }}</th>
												<th>{{ __('languages.Token Management.Expire Date') }}</th>
												<th>{{__('languages.Status')}}</th>
												<!-- <th>{{ __('languages.Action') }}</th> -->
											</tr>
										</thead>
										<tbody>
											@if(!empty($tokenList))
											@foreach($tokenList as $val)

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
													<input type="checkbox" name="TokenIDs[]" class="select-Token-chkbox" value="{{$val['id']}}">
												</td>
												<td>{{$val['id']}}</td>
												{{-- <td>{{ $user_name }}</td> --}}
												<td>{{ $val['users']['English_name'] ?? ''}}</td>
												<td>{{ $val['users']['Chinese_name'] ?? ''}}</td>
												<td>@if(!empty($val['event_schedule']['events']['event_name'])){{ $val['event_schedule']['events']['event_name'] }}@else - @endif</td>
												<td>@if(!empty($val['generate_token'])){{ $val['generate_token'] }}@else - @endif</td>
												<td>@if(!empty($val['used_token'])){{ $val['used_token'] }}@else - @endif</td>
												<td>@if(!empty($val['remaining_token'])){{ $val['remaining_token'] }}@else - @endif</td>
												<td>{{ date('d/m/Y', strtotime($val['expire_date']))}}</td>
												{{-- <td>{{ucfirst($val['status'])}}</td> --}}
												@if($val['status'] == "active")
													<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
												@else
													<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
												@endif
												<!-- <td><a href="{{ route('token-management.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a></td> -->
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
<div class="modal fade" id="exportTokenSelectField" tabindex="-1" role="dialog" aria-labelledby="exportTokenSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportTokenFields[]" class="all-token-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="user_name" checked>
						<span>{{__('languages.member.Member_Name')}}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="english_name" checked>
						<span>{{__('languages.member.English_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="chinese_name" checked>
						<span>{{__('languages.member.Chinese_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="event_name" checked>
						<span>{{__('languages.event.Event Name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="generate_token" checked>
						<span>{{__('languages.member.Tokens')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="used_token" checked>
						<span>{{__('languages.used_token')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="remaining_token" checked>
						<span>{{__('languages.Token Management.Remaining Token')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="expire_date" checked>
						<span>{{__('languages.Token Management.Expire Date')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportTokenFields[]" class="token-field-checkbox" value="status" checked>
						<span>{{__('languages.Status')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportTokens()">{{ __('languages.export') }} {{ __('languages.Token Management.token') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportTokenFieldColumnList = ['english_name','chinese_name','event_name','generate_token','used_token','remaining_token','expire_date','status'];
var TokenIds = [];
$(function () {

	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-Token-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Token-chkbox").prop('checked', true);
				var tokenid = row.closest('tr').find(".select-Token-chkbox").val();
				if (TokenIds.indexOf(tokenid) !== -1) {
					// Current value is exists in array
				} else {
					TokenIds.push(tokenid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Token-chkbox").prop('checked', false);
			});
			TokenIds = [];
		}
	});

	$(document).on("click", ".select-Token-chkbox", function (){
		if($('.select-Token-chkbox').length === $('.select-Token-chkbox:checked').length){
			$(".select-all-Token-chkbox").prop('checked',true);
		}else{
			$(".select-all-Token-chkbox").prop('checked',false);
		}
		tokenid = $(this).val();
		if ($(this).is(":checked")) {
			if (TokenIds.indexOf(tokenid) !== -1) {
				// Current value is exists in array
			} else {
				TokenIds.push(tokenid);
			}
		} else {
			TokenIds = $.grep(TokenIds, function(value) {
				return value != tokenid;
			});
		}
	});

	$(document).on("click", ".export-token", function () {
		$("#exportTokenSelectField").modal('show');
	});

	$(document).on("click", ".all-token-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".token-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var tokenColumnName = $(this).val();
				if (ExportTokenFieldColumnList.indexOf(tokenColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportTokenFieldColumnList.push(tokenColumnName);
				}
			});
		} else {
			$(".token-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportTokenFieldColumnList = [];
		}
	});

	$(document).on("click", ".token-field-checkbox", function (){
		if($('.token-field-checkbox').length === $('.token-field-checkbox:checked').length){
			$(".all-token-field-checkbox").prop('checked',true);
		}else{
			$(".all-token-field-checkbox").prop('checked',false);
		}
		var tokenColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportTokenFieldColumnList.indexOf(tokenColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportTokenFieldColumnList.push(tokenColumnName);
			}
		} else {
			ExportTokenFieldColumnList = $.grep(ExportTokenFieldColumnList, function(value) {
				return value != tokenColumnName;
			});
		}
	});
});

function exportTokens(){
	if($('.token-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#qualificationstable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/tokens",
			data: {
				'columnList' : ExportTokenFieldColumnList,
				'tokenIds' : TokenIds
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
					downloadLink.download = "TokenManagement.csv";

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