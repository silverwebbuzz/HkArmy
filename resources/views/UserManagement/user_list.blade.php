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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.UserManagement.user_management') }}</h3>
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
                <form name="filtrationUser" id="filtrationUser">
				<div class="row border rounded py-2 mb-2">
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<select class="form-control" id="filter_gender" name="filter_gender">
								<option value="">{{ __('languages.UserManagement.gender') }} </option>
								<option value="1" {{( request()->get('filter_gender') == 1) ? 'selected' : ''}}>{{__('languages.member.Male')}}</option>
                                <option value="2" {{(request()->get('filter_gender')==2) ? 'selected' : ''}}>{{__('languages.member.Female')}}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<select class="form-control" id="user_status" name="user_status">
								<option value="">{{ __('languages.Status') }}</option>
								<option value="1" {{( request()->get('user_status') == 1) ? 'selected' : ''}}>{{__('languages.Active')}}</option>
                                <option value="2" {{( request()->get('user_status') == 2) ? 'selected' : ''}}>{{__('languages.Inactive')}}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1 col-lg-4 col-md-4 col-sm-4">
						<fieldset class="form-group">
							<input type="text" class="form-control" id="search_text" name="search_text" placeholder="{{ __('languages.search_by')}} {{__('languages.UserManagement.email')}},{{__('languages.UserManagement.user_name')}},{{__('languages.UserManagement.contact_no') }}" autocomplete="off">
						</fieldset>
					</div>
					
					<div class="float-right align-items-center ml-1">
						<input type="submit" class="btn btn-primary glow submit" value="{{__('languages.Submit')}} " name="submit">
					</div>
					<div class="float-right align-items-center ml-1">
						<a href="{{url("user-management")}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
					</div>
					{{-- @if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id']))) --}}
					<div class="float-right align-items-center ml-1">
						<a href="{{ route('user-management.create') }}" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.UserManagement.add_user') }}</a>
					</div>
					{{-- @endif --}}
				</div>
                </form>
			</div>
		
			{{-- Export Button Start --}}
			{{-- <div class="row mb-2">
				<div class="import-export-btn ml-1">
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-events mb-0"> {{ __('languages.export') }} {{ __('languages.event.events') }}</a>
				</div>
			</div> --}}
			{{-- Export Button End --}}
			
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive event-search-list-cls">
									<table class="table"> {{-- id="eventtable" --}}
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="userIds[]" class="select-all-users-chkbox" value="all">
												</th>
												<th>{{ __('#Sr.No') }}</th>
												<th>{{ __('languages.UserManagement.role') }}</th>
												<th> @sortablelink('UserName',__('languages.UserManagement.user_name'))</th>
												<th>@sortablelink('English_name',__('languages.UserManagement.english_name'))</th>
												<th>@sortablelink('Chinese_name', __('languages.UserManagement.chinese_name'))</th>
												<th>@sortablelink('email',__('languages.UserManagement.email'))</th>
												<th>@sortablelink('Contact_number',__('languages.UserManagement.contact_no'))</th>
												<th>@sortablelink('Gender',__('languages.UserManagement.gender'))</th>
												<th>@sortablelink('Status', __('languages.UserManagement.status'))</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($userData))
												@foreach($userData as $index => $user)
                                                <tr>
                                                	<td>
                                                		<input type="checkbox" name="userIds[]" class="select-users-chkbox" value="{{$user->ID}}">
                                                	</td>
                                                    <td>{!!$userData->firstItem() +$index!!}</td>
                                                    <td>{{$user->Role->role_name ?? 'N/A'}}</td>
                                                    <td>{{$user->UserName ?? 'N/A'}}</td>
                                                    <td>{{$user->English_name ?? 'N/A'}}</td>
                                                    <td>{{$user->Chinese_name ?? 'N/A'}}</td>
                                                    <td>{{$user->email}}</td>
                                                    <td>{{$user->Contact_number ?? 'N/A'}}</td>
                                                    @if($user->Gender == '1')
                                                        <td>{{ __('languages.member.Male') }}</td>
                                                    @elseif($user->Gender == '')
                                                    <td>{{ __('N/A') }}</td>
                                                    @else
                                                        <td>{{ __('languages.member.Female') }}</td>
                                                     @endif
                                                     @if($user->Status == '1')
                                                        <td>{{ __('languages.Active') }}</td>
                                                    @else   
                                                        <td>{{ __('languages.Inactive') }}</td>
                                                    @endif
                                                    <td>
														<i class="bx bx-lock-alt change-password-lock" data-userid={{$user->ID}}></i>
														{{-- @if (in_array('members_write', $permissions)) --}}
															<a href="{{ route('user-management.edit',$user->ID) }}"><i class="bx bx-edit-alt"></i></a>
														{{-- @endif --}}
														{{-- @if (in_array('members_write', $permissions)) --}}
															{{-- <a href="{{ route('users.show',$user->ID) }}"><i class="bx bx-show-alt"></i></a> --}}
														{{-- @endif --}}
														{{-- @if (in_array('members_delete', $permissions)) --}}
															<a href="javascript:void(0);" data-id="{{$user->ID }}" class="deleteUser"><i class="bx bx-trash-alt"></i></a>
														{{-- @endif --}}
													</td>
                                                </tr>
											    @endforeach
											@endif
										</tbody>
									</table>
										<div class="row">
											<div class="col-md-11 col-lg-11">{{__('languages.showing')}} {{($userData->firstItem()) ? $userData->firstItem() : 0}} {{__('languages.to')}} {{!empty($userData->lastItem()) ? $userData->lastItem() : 0}}
												{{__('languages.of')}}  {{$userData->total()}} {{__('languages.entries')}}
											</div>
											<div calss="col-md-1 col-lg-1">
												<form>
													<select id="pagination">
														<option value="10" @if($items == 10) selected @endif >10</option>
														<option value="20" @if($items == 20) selected @endif >20</option>
														<option value="25" @if($items == 25) selected @endif >25</option>
														<option value="30" @if($items == 30) selected @endif >30</option>
														<option value="40" @if($items == 40) selected @endif >40</option>
														<option value="50" @if($items == 50) selected @endif >50</option>
														<option value="{{$userData->total()}}" @if($items == $userData->total()) selected @endif >{{__('languages.all')}}</option>
													</select> 
												</form>
											</div>
										</div>
										{{$userData->appends($_GET)->links()}}
									</div>
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

<!-- Start Change password Popup -->
<div class="modal" id="changeUserPwd" tabindex="-1" aria-labelledby="changeUserPwd" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="max-width: 50%;">
		<div class="modal-content">
			<form id="changepasswordUserFrom">	
				@csrf()
				<input type="hidden" value="" name="userId" id="changePasswordUserId">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.change_password')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<label class="text-bold-600" for="newPassword">{{__('languages.new_password')}}</label>
							<input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="{{__('languages.new_password')}}" value="" maxlength="8">
							@if($errors->has('newPassword'))<span class="validation_error">{{ $errors->first('newPassword') }}</span>@endif
						</div>
					</div>
					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<label class="text-bold-600" for="confirmPassword">{{__('languages.confirm_password')}}</label>
							<input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="{{__('languages.confirm_password')}}" value="" maxlength="8">
							@if($errors->has('confirmPassword'))<span class="validation_error">{{ $errors->first('confirmPassword') }}</span>@endif
						</div>
					</div>
				</div>
				<div class="modal-footer btn-sec">
					<button type="button" class="btn btn-default close-userChangePassword-popup" data-dismiss="modal">{{__('languages.close')}}</button>
					<button type="submit" class="blue-btn btn btn-primary submit-change-password-form">{{__('languages.Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Change password Popup -->
@include('layouts.footer')
<script>
	var UserIds = [];
	document.getElementById('pagination').onchange = function() { 
		window.location = "{!! $userData->url(1) !!}&items=" + this.value; 
	};
	</script>
<script>
	$(document).on('click','.change-password-lock',function(){
		$userId = $(this).data('userid');
		$("#changePasswordUserId").val($userId);
		$("#changeUserPwd").modal("show");
	});
	$(document).on('click','.close,.close-userChangePassword-popup',function(){
		$("#newPassword").val("");
		$("#confirmPassword").val("");
	});

	$(document).on("click", ".select-all-users-chkbox", function (){
		if ($(this).is(":checked")) {
			$(".table")
			.DataTable()
			.table(".table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-users-chkbox").prop('checked', true);
				var userid = row.closest('tr').find(".select-users-chkbox").val();
				console.log(UserIds);
				if (UserIds.indexOf(userid) !== -1) {
					// Current value is exists in array
				} else {
					UserIds.push(userid);
				}
			});
		} else {
			$(".table")
			.DataTable()
			.table(".table")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-users-chkbox").prop('checked', false);
			});
			UserIds = [];
		}
	});

	$(document).on("click", ".select-users-chkbox", function (){
		if($('.select-users-chkbox').length === $('.select-users-chkbox:checked').length){
			$(".select-all-users-chkbox").prop('checked',true);
		}else{
			$(".select-all-users-chkbox").prop('checked',false);
		}
		var userid = $(this).val();
		if ($(this).is(":checked")) {
			if (UserIds.indexOf(userid) !== -1) {
				// Current value is exists in array
			} else {
				UserIds.push(userid);
			}
		} else {
			UserIds = $.grep(UserIds, function (value) {
				return value != userid;
			});
		}
	});
</script>

@endsection
