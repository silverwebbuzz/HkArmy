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
		</div>
		<section class="users-view">
			<div class="content-body">
				<div class="row">
					<div class="col-12 col-sm-7">
						<div class="media mb-2">
							<div class="media-body pt-25">
								@if(!empty($attendancesreportdetalis['users']['UserName']))
									<h4 class="media-heading">
										<span class="">{{ $attendancesreportdetalis['users']['UserName'] }}</span>
									</h4>
								@else
									<h4 class="media-heading">
										<span class="">{{ $attendancesreportdetalis['users']['Chinese_name'] }} & {{ $attendancesreportdetalis['users']['English_name'] }}</span>
									</h4>
								@endif
								<span>{{ __('languages.member.Member_Number') }}:</span>
								<span class="">C{{ $attendancesreportdetalis['users']['MemberCode'] }}</span>
							</div>
						</div>
					</div>
					<div class="col-12 col-sm-5 px-0 d-flex justify-content-end align-items-center px-1 mb-2">
						<a href="{{ url('attendance-report') }}" class="btn btn-sm btn-primary"><i id="icon-arrow" class="bx bx-left-arrow-alt"></i>Back</a>
					</div>
				</div>
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<div class="col-12">
								<table class="table table-borderless">
									<tbody>
										<div class="row">
											<div class="col-sm-6">
												<div>
													{{ __('languages.member.Chinese_name') }}: {{ $attendancesreportdetalis['users']['Chinese_name'] }}
												</div>
												<div>
													{{ __('languages.member.English_name') }}: {{ $attendancesreportdetalis['users']['English_name'] }}
												</div>
												<div>
													@php
														$EventType = 'event_type_name_'.app()->getLocale();
													@endphp
													{{ __('languages.event.Event Type') }}: {{ $attendancesreportdetalis['event_type'][$EventType] }}
												</div>
												<div>
													{{ __('languages.event.Event Name') }}: {{ $attendancesreportdetalis['event']['event_name'] }}
												</div>
												<div>
													{{ __('languages.Attendance.In_Time') }}: {{ date('h:i a',strtotime($attendancesreportdetalis['in_time'])) }}
												</div>
											</div>
											<div class="col-sm-6">
												<div>
													{{ __('languages.Attendance.Out_Time') }}: {{ date('h:i a',strtotime($attendancesreportdetalis['out_time'])) }}
												</div>
												<div>
													<strong>{{ __('languages.Attendance.Used Hour') }}: {{ $attendancesreportdetalis['hours'] }}</strong>
												</div>
												<div>
													<strong>{{ __('languages.Attendance.Remaining Hours') }}: {{ $attendancesreportdetalis['remaining_hour'] }}</strong>
												</div>
												<div>
													<strong>{{ __('languages.member.Hour_Point') }}: {{ $attendancesreportdetalis['users']['hour_point'] }}</strong>
												</div>
												<div>
													{{ __('languages.event.Start Date') }}: {{ date('d/m/Y',strtotime($attendancesreportdetalis['event']['startdate'])) }}
												</div>
											</div>
										</div>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- USED HOUR TABLE -->
			<div class="row">
				<div class="col-12 col-sm-7">
					<div class="media mb-2">
						<div class="media-body pt-25">
							<h4 class="media-heading"><span class="users-view-name">{{ __('languages.Attendance.Used Hour') }}</span></h4>
						</div>
					</div>
				</div>
			</div>
			<div class="users-list-table">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<td>{{ __('languages.event.Activity') }}</td>
											<td>{{ __('languages.event.Training') }}</td>
											<td>{{ __('languages.event.Service') }}</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>{{ $attendancesreportdetalis['activity_hour'] }}</td>
											<td>{{ $attendancesreportdetalis['training_hour'] }}</td>
											<td>{{ $attendancesreportdetalis['service_hour'] }}</td>
										</tr>
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
<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection