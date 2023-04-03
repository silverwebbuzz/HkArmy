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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.UserManagement.add_user') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="basic-dropdown">
				<div class="row">
					<div class="col-12">
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
						<div class="card">
							<div class="card-content">
								<form action="{{route('user-management.store')}}" method="POST" id="addUserForm" name="UserForm" class="addUserForm" autocomplete="off">
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
									<div class="card-body">
										<div class="form-row">
                                            <div class="form-group col-md-6 mb-50">
												<label for="users-list-role">{{ __('languages.UserManagement.role') }}</label>
												<fieldset class="form-group">
													<select class="form-control" id="role_type" name="role_type">
														<option value="">{{ __('languages.UserManagement.select_role') }} </option>
														@if(!empty($roleData))
                                                        @foreach($roleData as $role)
                                                            <option value="{{$role->id}}" {{old('role_type') == $role->id ? 'selected' : ''}}>{{$role->role_name}}</option>
                                                        @endforeach
														@endif
													</select>
												</fieldset>
                                                <span class="text-danger">{{ $errors->first('role_type') }}</span>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="userName">{{ __('languages.UserManagement.user_name') }}</label>
												<input type="text" class="form-control" id="userName" name="userName" placeholder="{{ __('languages.UserManagement.user_name') }}" value="{{old('userName')}}">
                                                <span class="text-danger">{{ $errors->first('userName') }}</span>
                                            </div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="englishName">{{ __('languages.UserManagement.english_name') }}</label>
												<input type="text" class="form-control" id="englishName" name="englishName" placeholder="{{ __('languages.UserManagement.english_name') }}" value="{{old('englishName')}}">
                                                <span class="text-danger">{{ $errors->first('englishName') }}</span>
                                            </div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="chineseName">{{ __('languages.UserManagement.chinese_name') }}</label>
												<input type="text" class="form-control" id="chineseName" name="chineseName" placeholder="{{ __('languages.UserManagement.chinese_name') }}" value="{{old('chineseName')}}">
                                                <span class="text-danger">{{ $errors->first('chineseName') }}</span>
                                            </div>
										</div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.UserManagement.gender') }}</label>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-inline-block mt-1 mr-1 mb-1">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="gender" id="male" value="1" {{old('gender') == 1 ? 'checked' : ''}}>
                                                                <label class="custom-control-label" for="male" >{{ __('languages.member.Male') }}</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block my-1 mr-1 mb-1">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="gender" id="female" value="2" {{old('gender') == 2 ? 'checked' : ''}}>
                                                                <label class="custom-control-label" for="female" >{{ __('languages.member.Female') }}</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                </ul>
                                                <div class="gender-error-cls"></div>
                                                <span class="text-danger gender-err">{{ $errors->first('gender') }}</span>
                                            </div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="email">{{ __('languages.UserManagement.email') }}</label>
												<input type="text" class="form-control" id="email" name="email" placeholder="{{ __('languages.UserManagement.email') }}" value="{{old('email')}}">
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            </div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="password">{{ __('languages.UserManagement.password') }}</label>
												<input type="password" class="form-control" id="password" name="password" placeholder="{{ __('languages.UserManagement.password') }}" value="">
												<span class="text-danger password-err">{{ $errors->first('password') }}</span>
											</div>
											<div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="confirm_password">{{ __('languages.UserManagement.confirm_password') }}</label>
												<input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="{{ __('languages.UserManagement.confirm_password') }}" value="">
												<span class="text-danger confirm-password-err">{{ $errors->first('confirm_password') }}</span>
											</div>
										</div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6 mb-50">
												<label class="text-bold-600" for="contact_no">{{ __('languages.UserManagement.contact_no') }}</label>
												<input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="{{ __('languages.UserManagement.contact_no') }}" value="{{old('contact_no')}}" maxlength="8">
                                                <span class="text-danger">{{ $errors->first('contact_no') }}</span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label>{{ __('languages.Status') }}</label>
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="Status" name="Status">
                                                        <option value="1" {{old('Status') == 1 ? 'selected' : ''}}>{{ __('languages.Active') }}</option>
                                                        <option value="2" {{old('Status') == 2 ? 'selected' : ''}}>{{ __('languages.Inactive') }}</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    	
										</div>
										{{-- @if(in_array('user_management_create', Helper::module_permission(Session::get('user')['role_id']))) --}}
										<div class="form-row add-event-cls-bottom">
											<div class="form-group col-md-12 mb-50">
												<button type="submit" class="btn btn-primary glow position-relative" id="submitUserBtn">{{__('languages.Submit')}}</button>
											</div>
										</div>
										{{-- @endif --}}
									</form>
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
@endsection