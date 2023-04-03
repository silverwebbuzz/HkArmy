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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.RoleManagement.Add_role') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- users edit start -->
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<div class="tab-content">
								<div class="tab-pane active fade show" id="account" aria-labelledby="account-tab" role="tabpanel">
									<form name="roleForm" id="roleForm" method="POST" action="{{ url('roleManagement') }}">
										<!-- {{ method_field('PUT') }} -->
										<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
										<div class="row">
											<div class="col-12 col-sm-12">
												<div class="form-group">
													<div class="controls">
														<label>{{ __('languages.RoleManagement.Name') }}</label>
														<input type="text" class="form-control" placeholder="{{ __('languages.RoleManagement.Name') }}" value="" name="rolename" id="rolename">
													</div>
												</div>
												<div class="form-group">
													<label>{{ __('languages.RoleManagement.Description') }}</label>
													<fieldset class="form-group">
														<textarea class="form-control" id="desc" name="desc" rows="3" placeholder="{{ __('languages.RoleManagement.Description') }}"></textarea>
													</fieldset>
												</div>
											</div>
											<div class="form-group col-md-12 mb-50">
												<label for="Status">{{ __('languages.RoleManagement.Status') }}</label>
												<div class="form-group">
													<fieldset class="form-group">
													<select class="form-control" id="status" name="status">
														<option value="1">{{ __('languages.Active') }}</option>
														<option value="2">{{ __('languages.Inactive') }}</option>
													</select>
												</fieldset>
												</div>
											</div>
											<div class="col-12">
												<div class="table-responsive">
													<table class="table mt-1">
														<thead>
															<tr>
																<th>{{ __('languages.RoleManagement.Module_permission') }}</th>
																<th>{{ __('languages.RoleManagement.Read') }}</th>
																<th>{{ __('languages.RoleManagement.Write') }}</th>
																<th>{{ __('languages.RoleManagement.Create') }}</th>
																<th>{{ __('languages.RoleManagement.Delete') }}</th>
															</tr>
														</thead>
														<tbody>
															@php
																$displayName = 'display_name_'.app()->getLocale();
															@endphp
															@foreach($modules as $module)
															<tr>
																<td>{{ $module[$displayName] }}</td>
																<td>
																	<div class="checkbox"><input type="checkbox" id="{{ $module['name'] }}_read" name="permissions[]" class="checkbox-input" value="{{ $module['name'] }}_read">
																		<label for="{{ $module['name'] }}_read"></label>
																	</div>
																</td>
																<td>
																	<div class="checkbox"><input type="checkbox" id="{{ $module['name'] }}_write" name="permissions[]" class="checkbox-input" value="{{ $module['name'] }}_write">
																		<label for="{{ $module['name'] }}_write"></label>
																	</div>
																</td>
																<td>
																	<div class="checkbox"><input type="checkbox" id="{{ $module['name'] }}_create" name="permissions[]" class="checkbox-input" value="{{ $module['name'] }}_create">
																		<label for="{{ $module['name'] }}_create"></label>
																	</div>
																</td>
																<td>
																	<div class="checkbox"><input type="checkbox" id="{{ $module['name'] }}_delete" name="permissions[]" class="checkbox-input" value="{{ $module['name'] }}_delete">
																		<label for="{{ $module['name'] }}_delete"></label>
																	</div>
																</td>
															</tr>
															@endforeach
														</tbody>
													</table>
												</div>
											</div>
											<div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
												<input type="submit" name="submit" id="submit" value="{{ __('languages.Submit') }}" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">
											</div>
										</div>
									</form>
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
@endsection