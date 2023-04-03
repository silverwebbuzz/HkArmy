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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.badge_member_list.badge_member_list') }}</h3>
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
							<input type="text" class="form-control filter_date_event" name="badges_issue_date" value="{{ request()->get('badges_issue_date') }}" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1" style="width: 20%;">
						<select class="form-control" name="member_type" id="member_type">
							<option value="not_mentor_team" <?php if(request()->get('member_type') == 'not_mentor_team'){ echo 'selected';}?>>{{ __('languages.not_mentor_team') }}</option>
							<option value="mentor_team" <?php if(request()->get('member_type') == 'mentor_team'){ echo 'selected';}?>>{{ __('languages.mentor_team') }}</option>
						</select>
					</div>
					<div class="float-right align-items-center ml-1" style="width: 15%;" id="badges_categories_section">
						<select class="form-control" id="badges_categories" name="badges_categories">
							@if(!empty($get_badge_categories))
								{{!!$get_badge_categories!!}}
							@endif
						</select>
					</div>
					<div class="float-right align-items-center ml-1" style="width: 20%;">
						<select class="form-control" name="member_id" id="member_id">
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
						<a href="{{url("badge-assigned-member-list")}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
					</div>
				</div>
				</form>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<a href="{{route('import-badge-assigned-member-list')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.badge_member') }}</a>
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-badge_member_list mb-0"> {{ __('languages.export') }} {{ __('languages.badge_member_list.badge_member_list') }}</a>
					<a href="{{asset('uploads\sample_files\badgeMember.csv')}}">
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
									<table id="badge-member-data-table" class="table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="badgeMemberAllIds[]" id="select-all-badgeMember-chkbox" class="select-all-badgeMember-chkbox">
												</th>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.badge_member_list.badge_name') }}</th>
                                                {{-- <th>{{ __('languages.badge_member_list.member_name') }}</th> --}}
												<th>{{ __('languages.member.English_name') }}</th>
												<th>{{ __('languages.member.Chinese_name') }}</th>
												<th>{{ __('languages.badge_member_list.reference_number') }}</th>
												<th>{{ __('languages.badge_member_list.issue_date') }}</th>
                                                <th>{{ __('languages.badge_member_list.assigned_date') }}</th>
												<th>{{ __('languages.Status') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($BadgeMemberList))
											@php
											$award_name = 'name_'.app()->getLocale();
											@endphp
												@foreach($BadgeMemberList as $val)
													<tr>
														<td>
															<input type="checkbox" name="badgeMemberIds[]" id="select-badgeMember-chkbox" class="select-badgeMember-chkbox"  value="{{$val['id']}}">
														</td>
														<td>{{$val['id']}}</td>
														<td>{{ $val->badge->$award_name}}</td>
                                                        {{-- <td>
                                                            @if(app()->getLocale() == 'en')
                                                                {{$val->user->English_name}}
                                                            @else
                                                                {{$val->user->Chinese_name}}
                                                            @endif
                                                        </td> --}}
														<td>{{ $val->user->English_name ?? ''}}</td>
														<td>{{ $val->user->Chinese_name ?? ''}}</td>
														<td>{{$val->reference_number ?? ''}}</td>
														<td>{{ Helper::dateConvertDDMMYYY('-','/',$val->issue_date) ?? ''}}</td>
                                                        <td>{{ Helper::dateConvertDDMMYYY('-','/',$val->assigned_date) ?? ''}}</td>
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
<!-- Modal -->
<div class="modal fade" id="exportBadgeAssignMemberSelectField" tabindex="-1" role="dialog" aria-labelledby="exportBadgeAssignMemberSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="all-badge-Assign-meber-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="badge_name" checked>
						<span>{{ __('languages.badge_member_list.badge_name') }}</span>
					</div>
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.badge_member_list.member_name') }}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.member.English_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.member.Chinese_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="reference_number" checked>
						<span>{{ __('languages.badge_member_list.reference_number') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="issue_date" checked>
						<span>{{ __('languages.badge_member_list.issue_date') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="assign_date" checked>
						<span>{{ __('languages.badge_member_list.assigned_date') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportBadgeAssignMemberFields[]" class="badge-Assign-meber-field-checkbox" value="status" checked>
						<span>{{ __('languages.Status') }}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportBadgeAssignMember()">{{ __('languages.export') }} {{ __('languages.badge_member_list.badge_member_list') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportBadgeAssignMemberFieldColumnList = ['badge_name','english_name','chinese_name','reference_name','issue_date','assigned_date','status'];
var badgesAssignMemberIds = [];
$(function () {

	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-badgeMember-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#badge-member-data-table")
			.DataTable()
			.table("#badge-member-data-table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-badgeMember-chkbox").prop('checked', true);
				var badgeAssignIds = row.closest('tr').find(".select-badgeMember-chkbox").val();
				if (badgesAssignMemberIds.indexOf(badgeAssignIds) !== -1) {
					// Current value is exists in array
				} else {
					badgesAssignMemberIds.push(badgeAssignIds);
				}
			});
		} else {
			$("#badge-member-data-table")
			.DataTable()
			.table("#badge-member-data-table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-badgeMember-chkbox").prop('checked', false);
			});
			badgesAssignMemberIds = [];
		}
	});

	$(document).on("click", ".select-badgeMember-chkbox", function (){
		if($('.select-badgeMember-chkbox').length === $('.select-badgeMember-chkbox:checked').length){
			$(".select-all-badgeMember-chkbox").prop('checked',true);
		}else{
			$(".select-all-badgeMember-chkbox").prop('checked',false);
		}
		badgeAssignIds = $(this).val();
		if ($(this).is(":checked")) {
			if (badgesAssignMemberIds.indexOf(badgeAssignIds) !== -1) {
				// Current value is exists in array
			} else {
				badgesAssignMemberIds.push(badgeAssignIds);
			}
		} else {
			badgesAssignMemberIds = $.grep(badgesAssignMemberIds, function(value) {
				return value != badgeAssignIds;
			});
		}
	});

	$(document).on("click", ".export-badge_member_list", function () {
		$("#exportBadgeAssignMemberSelectField").modal('show');
	});

	$(document).on("click", ".all-badge-Assign-meber-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".badge-Assign-meber-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var badgeAssignMemberColumnName = $(this).val();
				if (ExportBadgeAssignMemberFieldColumnList.indexOf(badgeAssignMemberColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportBadgeAssignMemberFieldColumnList.push(badgeAssignMemberColumnName);
				}
			});
		} else {
			$(".badge-Assign-meber-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportBadgeAssignMemberFieldColumnList = [];
		}
	});

	$(document).on("click", ".badge-Assign-meber-field-checkbox", function (){
		if($('.badge-Assign-meber-field-checkbox').length === $('.badge-Assign-meber-field-checkbox:checked').length){
			$(".all-badge-Assign-meber-field-checkbox").prop('checked',true);
		}else{
			$(".all-badge-Assign-meber-field-checkbox").prop('checked',false);
		}
		var badgeAssignMemberColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportBadgeAssignMemberFieldColumnList.indexOf(badgeAssignMemberColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportBadgeAssignMemberFieldColumnList.push(badgeAssignMemberColumnName);
			}
		} else {
			ExportBadgeAssignMemberFieldColumnList = $.grep(ExportBadgeAssignMemberFieldColumnList, function(value) {
				return value != badgeAssignMemberColumnName;
			});
		}
	});
});

function exportBadgeAssignMember(){
	if($('.badge-Assign-meber-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#badge-member-data-table").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/badge-assign-member",
			data: {
				'badges_issue_date' : $('.filter_date_event').val(),
				'badges_categories' : $('#badges_categories').val(),
				'search_text' : $('#search_text').val(),
				'member_id' : $('#member_id').val(),
				'columnList' : ExportBadgeAssignMemberFieldColumnList,
				'badgesAssignMemberIds' : badgesAssignMemberIds
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
					downloadLink.download = "BadgesAssignMember.csv";

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