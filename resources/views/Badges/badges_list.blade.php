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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.badges.badges') }}</h3>
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
					<div class="float-right align-items-center ml-1">
						<a href="{{ route('badges.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">
							<i class="bx bx-user-plus"></i>{{ __('languages.badges.add_badges') }}
						</a>
					</div>
				</div>
			</div>
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="qualificationstable" class="table">
										<thead>
											<tr>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.badges.badges_name') }}</th>
												<th>{{ __('languages.badges.badges_type') }}</th>
												<th>{{ __('languages.badges.other_badges_type') }}</th>
												<th>{{ __('languages.badges.badges_image') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($Badges))
											@php
												$BadgesCategoriesName = 'name_'.app()->getLocale();
												$BadgesName = 'name_'.app()->getLocale();
												$OtherBadgesType = 'other_badges_type_'.app()->getLocale();
											@endphp
												@foreach($Badges as $val)
													<tr>
														<td>{{ $loop->iteration}}</td>
														<td>{{ $val->$BadgesName}}</td>
														@if($val->badges_type_id == 0)
														<td>{{ __('languages.badges.other') }}</td>
														@else
														<td>{{ $val->badgecategories[$BadgesCategoriesName] ?? '---'}}</td>
														@endif
														<td>{{$val->$OtherBadgesType ?? '---'}}</td>
														<td>
															<a href="{{asset($val->badges_image)}}" target="_blank">
																<img src="{{asset($val->badges_image)}}" height="50" width="100">
															</a>
														</td>
														@if($val['status'] == "active")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('badges.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deleteBadges"><i class="bx bx-trash-alt"></i> </a>
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
				</div>
			</section>
		</div>
	</div>
</div>

<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection