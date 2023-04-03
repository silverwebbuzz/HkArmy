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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.RoleManagement.Role_Management') }}</h3>
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
					<div class="float-right align-items-center  ml-1">
						<a href="{{ route('roleManagement.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.RoleManagement.Add_role') }}</a>
					</div>
				</div>
			</div>
			
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<a href="{{route('import-roles')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.RoleManagement.roles') }}</a>
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export_roles mb-0"> {{ __('languages.export') }} {{__('languages.RoleManagement.roles')}}</a>
					<a href="{{asset('uploads\sample_files\role.csv')}}">
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
								<table id="rolemanagemnttable" class="table">
									<thead>
										<tr>
											<th>
												<input type="checkbox" name="allroleIDs[]" class="select-all-role-chkbox" value="all">
											</th>
											<th>{{ __('languages.RoleManagement.Sr_No') }}</th>
											<th>{{ __('languages.RoleManagement.Role_name') }}</th>
											<th>{{ __('languages.RoleManagement.Status') }}</th>
											<th>{{ __('languages.Action') }}</th>
										</tr>
									</thead>
									<tbody>
										@foreach($roleList as $val)
											<tr>
												<td>
													<input type="checkbox" name="roleIDs[]" class="select-role-chkbox" value="{{$val['id']}}">
												</td>
												<td>{{$val['id']}}</td>
												<td>{{ ucfirst($val['role_name']) }}</td>
												@if( $val['status'] == '1')
													<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
												@else
													<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
												@endif
												<td>
													<a href="{{ route('roleManagement.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
													<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deletRole"><i class="bx bx-trash-alt"></i> </a>
												</td>
											</tr>
										@endforeach
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


<!-- footer content -->
@include('layouts.footer')

<!-- Modal -->
<div class="modal fade" id="exportRoleSelectField" tabindex="-1" role="dialog" aria-labelledby="exportRoleSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportRoleFields[]" class="all-role-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportRoleFields[]" class="role-field-checkbox" value="role_name" checked>
						<span>{{__('languages.RoleManagement.Role_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportRoleFields[]" class="role-field-checkbox" value="status" checked>
						<span>{{__('languages.Status')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportRoles()">{{__('languages.export')}} {{__('languages.RoleManagement.roles')}}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportRoleFieldColumnList = ['role_name','status'];
var RoleIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-role-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#rolemanagemnttable")
			.DataTable()
			.table("#rolemanagemnttable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-role-chkbox").prop('checked', true);
				var eventid = row.closest('tr').find(".select-role-chkbox").val();
				if (RoleIds.indexOf(eventid) !== -1) {
					// Current value is exists in array
				} else {
					RoleIds.push(eventid);
				}
			});
		} else {
			$("#rolemanagemnttable")
			.DataTable()
			.table("#rolemanagemnttable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-role-chkbox").prop('checked', false);
			});
			RoleIds = [];
		}
	});

	$(document).on("click", ".select-role-chkbox", function (){
		if($('.select-role-chkbox').length === $('.select-role-chkbox:checked').length){
			$(".select-all-role-chkbox").prop('checked',true);
		}else{
			$(".select-all-role-chkbox").prop('checked',false);
		}
		roleid = $(this).val();
		if ($(this).is(":checked")) {
			if (RoleIds.indexOf(roleid) !== -1) {
				// Current value is exists in array
			} else {
				RoleIds.push(roleid);
			}
		} else {
			RoleIds = $.grep(RoleIds, function(value) {
				return value != roleid;
			});
		}
	});
	$(document).on("click", ".export_roles", function () {
		$("#exportRoleSelectField").modal('show');
	});

	$(document).on("click", ".all-role-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".role-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var roleColumnName = $(this).val();
				if (ExportRoleFieldColumnList.indexOf(roleColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportRoleFieldColumnList.push(roleColumnName);
				}
			});
		} else {
			$(".role-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportRoleFieldColumnList = [];
		}
	});

	$(document).on("click", ".role-field-checkbox", function (){
		if($('.role-field-checkbox').length === $('.role-field-checkbox:checked').length){
			$(".all-role-field-checkbox").prop('checked',true);
		}else{
			$(".all-role-field-checkbox").prop('checked',false);
		}
		var roleColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportRoleFieldColumnList.indexOf(roleColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportRoleFieldColumnList.push(roleColumnName);
			}
		} else {
			ExportRoleFieldColumnList = $.grep(ExportRoleFieldColumnList, function(value) {
				return value != roleColumnName;
			});
		}
	});
});

function exportRoles(){
	if($('.role-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/roles",
			data: {
				'columnList' : ExportRoleFieldColumnList,
				'roleIds' : RoleIds
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
					downloadLink.download = "Roles.csv";

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