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
												<th>{{ __('languages.Sr_No') }}</th>
												<th>{{ __('languages.member.Member_Name') }}</th>
												<th>{{ __('languages.event.Event Name') }}</th>
												<th>{{ __('languages.member.Tokens') }}</th>
												<th>{{ __('languages.Token Management.Remaining Token') }}</th>
												<th>{{ __('languages.Token Management.Expire Date') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($tokenList))
											@foreach($tokenList as $val)

											@if(app()->getLocale() == 'ch')
											@php
											$user_name = $val['users']['Chinese_name'];
											@endphp
											@else
											@php
											$user_name = $val['users']['English_name'];
											@endphp
											@endif
											<tr>
												<td>{{ $loop->iteration}}</td>
												<td>{{ $user_name }}</td>
												<td>@if(!empty($val['event']['event_name'])){{ $val['event']['event_name'] }}@else - @endif</td>
												<td>@if(!empty($val['token'])){{ $val['token'] }}@else - @endif</td>
												<td>@if(!empty($val['remaining_token'])){{ $val['remaining_token'] }}@else - @endif</td>
												<td>{{ date('d-m-Y', strtotime($val['expired_at']))}}</td>
												<td><a href="{{ route('token-management.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a></td>
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