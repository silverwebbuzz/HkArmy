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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.award_member_list.award_member_list') }}</h3>
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
				<form action="" method="get">
				<div class="row border rounded py-2 mb-2">
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control filter_date_event" id= "award_issue_date" name="award_issue_date" value="{{ request()->get('award_issue_date') }}" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1" style="width: 15%;">
						<select class="form-control" id="award_categories" name="award_categories">
							@if(!empty($get_awards_categories))
								{{!!$get_awards_categories!!}}
							@endif
						</select>
					</div>
					<div class="float-right align-items-center ml-1" style="width: 20%;">
						<select class="form-control" name="member_id" id ="member_id">
							<option value="" <?php if(request()->get('member_id') == ''){ echo 'selected';}?>>{{ __('languages.member.Select_Member_Name') }}</option>
							<option value="all" <?php if(request()->get('member_id') == 'all'){ echo 'selected';}?>>{{ __('languages.all_member') }}</option>
							@if($membersList)
								@foreach ($membersList as $member)
									@if(app()->getLocale() == 'en')
									<option value="{{ $member->ID }}" <?php if(request()->get('member_id') == $member->ID){ echo 'selected';}?>>{{ $member->English_name }}</option>
									@else
									<option value="{{ $member->ID }}" <?php if(request()->get('member_id') == $member->ID){ echo 'selected';}?>>{{ $member->Chinese_name }}</option>											
									@endif
								@endforeach								
							@endif
						</select>
					</div>
					<div class="col-md-3 float-right align-items-center ml-1">
						<fieldset class="form-group">
							<input type="text" class="form-control" id="search_text" name="search_text" placeholder="{{ __('languages.search_by_reference_number') }}" value="{{ request()->get('search_text') }}" autocomplete="off">
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="filter">
					</div>
					<div class="float-right align-items-center ml-1">
						<a href="{{url('award-assigned-member-list')}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
					</div>
				</div>
				</form>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<a href="{{route('import-award-assign-member-list')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.award_member') }}</a>
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export_award_member_list mb-0"> {{ __('languages.export') }} {{ __('languages.award_member_list.award_member_list') }}</a>
					<a href="{{asset('uploads\sample_files\awardMember.csv')}}">
						<button class="btn"><i class="bx bxs-download"></i>{{__('languages.download_sample_file')}}</button>
					</a>
				</div>
			</div>
			{{-- Export Button End --}}
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="award-member-data-table" class="table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="awardMemberAllIds[]" id="select-all-awardMember-chkbox" class="select-all-awardMember-chkbox">
												</th>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.award_member_list.award_name') }}</th>
                                                {{-- <th>{{ __('languages.award_member_list.member_name') }}</th> --}}
												<th>{{ __('languages.member.English_name') }}</th>
												<th>{{ __('languages.member.Chinese_name') }}</th>
												<th>{{ __('languages.award_member_list.reference_number') }}</th>
												<th>{{ __('languages.award_member_list.user_name') }}</th>
												<th>{{ __('languages.award_member_list.issue_date') }}</th>
                                                {{-- <th>{{ __('languages.award_member_list.assigned_date') }}</th> --}}
												<th>{{ __('languages.Status') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($AwardsMemberList))
											@php
											$award_name = 'name_'.app()->getLocale();
											@endphp
												@foreach($AwardsMemberList as $val)
													<tr>
														<td>
															<input type="checkbox" name="awardMemberIds[]" id="select-awardMember-chkbox" class="select-awardMember-chkbox"  value="{{$val['id']}}">
														</td>
														<td>{{$val['id']}}</td>
														<td>{{ $val->award->$award_name}}</td>
                                                        {{-- <td>
                                                            @if(app()->getLocale() == 'en')
                                                                {{$val->user->English_name}}
                                                            @else
                                                                {{$val->user->Chinese_name}}
                                                            @endif
                                                        </td> --}}
														<td>{{ $val->user->English_name ?? '' }}</td>
														<td>{{ $val->user->Chinese_name ?? '' }}</td>
														<td>{{$val->reference_number ?? ''}}</td>
														<td>{{$val->user->UserName}}</td>
														<td>{{ Helper::dateConvertDDMMYYY('-','/',$val->issue_date) ?? ''}}</td>
                                                        {{-- <td>{{$val->assigned_date}}</td> --}}
														@if($val->status == "active")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
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
<!-- /footer content -->
<!-- Modal -->
<div class="modal fade" id="exportAwardAssignSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAwardAssignSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportAwardAssignFields[]" class="all-awardAssign-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="award_name" checked>
						<span>{{__('languages.award_member_list.award_name')}}</span>
					</div>
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="member_name" checked>
						<span>{{__('languages.award_member_list.member_name')}}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="english_name" checked>
						<span>{{__('languages.member.English_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="chinese_name" checked>
						<span>{{__('languages.member.Chinese_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="reference_number" checked>
						<span>{{__('languages.award_member_list.reference_number')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="issue_date" checked>
						<span>{{__('languages.award_member_list.issue_date')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAwardAssignFields[]" class="awardAssign-field-checkbox" value="status" checked>
						<span>{{__('languages.Status')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAwardAssignMember()">{{__('languages.export')}} {{__('languages.award_member_list.award_member_list')}}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportAwardAssignFieldColumnList = ['award_name','english_name','chinese_name','reference_number','issue_date','status'];
var awardAssignIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-awardMember-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#award-member-data-table")
			.DataTable()
			.table("#award-member-data-table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-awardMember-chkbox").prop('checked', true);
				var eventid = row.closest('tr').find(".select-awardMember-chkbox").val();
				if (awardAssignIds.indexOf(eventid) !== -1) {
					// Current value is exists in array
				} else {
					awardAssignIds.push(eventid);
				}
			});
		} else {
			$("#award-member-data-table")
			.DataTable()
			.table("#award-member-data-table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-awardMember-chkbox").prop('checked', false);
			});
			awardAssignIds = [];
		}
	});

	$(document).on("click", ".select-awardMember-chkbox", function (){
		if($('.select-awardMember-chkbox').length === $('.select-awardMember-chkbox:checked').length){
			$(".select-all-awardMember-chkbox").prop('checked',true);
		}else{
			$(".select-all-awardMember-chkbox").prop('checked',false);
		}
		awardMember = $(this).val();
		if ($(this).is(":checked")) {
			if (awardAssignIds.indexOf(awardMember) !== -1) {
				// Current value is exists in array
			} else {
				awardAssignIds.push(awardMember);
			}
		} else {
			awardAssignIds = $.grep(awardAssignIds, function(value) {
				return value != awardMember;
			});
		}
	});
	$(document).on("click", ".export_award_member_list", function () {
		$("#exportAwardAssignSelectField").modal('show');
	});

	$(document).on("click", ".all-awardAssign-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".awardAssign-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var eventColumnName = $(this).val();
				if (ExportAwardAssignFieldColumnList.indexOf(eventColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportAwardAssignFieldColumnList.push(eventColumnName);
				}
			});
		} else {
			$(".awardAssign-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportAwardAssignFieldColumnList = [];
		}
	});

	$(document).on("click", ".awardAssign-field-checkbox", function (){
		if($('.awardAssign-field-checkbox').length === $('.awardAssign-field-checkbox:checked').length){
			$(".all-awardAssign-field-checkbox").prop('checked',true);
		}else{
			$(".all-awardAssign-field-checkbox").prop('checked',false);
		}
		var eventColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportAwardAssignFieldColumnList.indexOf(eventColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportAwardAssignFieldColumnList.push(eventColumnName);
			}
		} else {
			ExportAwardAssignFieldColumnList = $.grep(ExportAwardAssignFieldColumnList, function(value) {
				return value != eventColumnName;
			});
		}
	});
});

function exportAwardAssignMember(){
	if($('.awardAssign-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#award-member-data-table").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/award-assign-member-list",
			data: {
				'filter_date_event' : $('#award_issue_date').val(),
				'award_categories' : $('#award_categories').val(),
				'member_id' : $('#member_id').val(),
				'reference_number' : $('#search_text').val(),
				'columnList' : ExportAwardAssignFieldColumnList,
				'awardAssignIds' : awardAssignIds
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
					downloadLink.download = "AwardAssignMember.csv";
					exportHistroy('AwardAssignMember', url, fileData);

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
