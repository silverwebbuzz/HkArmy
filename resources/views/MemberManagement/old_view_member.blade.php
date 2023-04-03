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
		<div class="content-body">
			<section class="page-user-profile">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="user-profile-images">
									@if($user_view['image'])
									<img src="{{ asset($user_view['image']) }}" class="user-profile-image rounded" alt="user profile image" height="140" width="140">
									@else
									<img src="{{ asset('assets/image/no-image.jpg') }}" class="user-profile-image rounded" alt="user profile image" height="140" width="140">
									@endif
								</div>
								<div class="user-profile-text">
									<h4 class="mb-0 text-bold-500 profile-text-color">{{ $user_view['English_name'] }} & {{ $user_view['Chinese_name'] }}</h4>
									<small>{{ __('languages.member.Member_Number') }}: C{{ $user_view['MemberCode'] }}</small>
								</div>
								<div class="card-body px-0">
									<ul class="nav user-profile-nav justify-content-center justify-content-md-start nav-tabs border-bottom-0 mb-0" role="tablist">
										<li class="nav-item pb-0">
											<a class="nav-link d-flex px-1 active" id="activity-tab" data-toggle="tab" href="#activity" aria-controls="activity" role="tab" aria-selected="True"><i class="bx bx-user"></i><span class="d-none d-md-block">{{ __('languages.Attendance.Activity') }}</span></a>
										</li>
										<li class="nav-item pb-0 mr-0">
											<a class="nav-link d-flex px-1" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false"><i class="bx bx-copy-alt"></i><span class="d-none d-md-block">{{ __('languages.member.Personal_Information') }}</span></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="tab-content">
									<div class="tab-pane active" id="activity" aria-labelledby="activity-tab" role="tabpanel">
										<div class="card">
											<div class="card-content">
												<div class="card-body">
													<div class="row bg-primary bg-lighten-5 rounded mb-2 mx-25 text-center text-lg-left">
														<div class="col-12 p-2 count-attendance">
															<h6 class="text-primary mb-0">{{ __('languages.Attendance.Number_of_Attendance') }}: <span class="font-large-1 align-middle">
																{{ !empty($attendancecount) ? count($attendancecount) : 0 }}
																</span>
															</h6>
														</div>
														<div class="count-attendance-cls"></div>
														<!-- Don't remove div or class -->
													</div>
													<form name="eventtypeform" id="eventtypeform" class="eventtypeserach" data-user-id="{{ $user_view['ID'] }}">
														<div class="row py-1 mb-1">
															<div class="col-12 col-sm-6 col-lg-3">
																<label for="users-list-role">{{ __('languages.event.Event Type') }}</label>
																<fieldset class="form-group">
																	<select class="form-control" id="eventserachtype" name="event_type">
																		<option value="">{{ __('languages.event.Select_event_type') }} </option>
																		<option value="Training">{{ __('languages.event.Training') }}</option>
																		<option value="Activity">{{ __('languages.event.Activity') }}</option>
																		<optgroup label="{{ __('languages.event.Service') }}">
																			<option value="honour">{{ __('languages.event.Guard_of_honour') }}</option>
																			<option value="community">{{ __('languages.event.Community') }}</option>
																			<option value="Headquatters">{{ __('languages.event.Headquatters') }}</option>
																			<option value="administration">{{ __('languages.event.Administration') }}</option>
																			<option value="other">{{ __('languages.event.Other') }}</option>
																		</optgroup>
																	</select>
																</fieldset>
															</div>
														</div>
													</form>
													@if(!empty($attendance))
													@php
													$color_array = array('timeline-icon-primary','timeline-icon-danger','timeline-icon-info','timeline-icon-warning');
													$size_of_array = sizeof($color_array);
													@endphp
													<ul class="widget-timeline activity-cls">
														@foreach($attendance as $key => $val)
														@php
														$n = rand(0,$size_of_array-1);
														$class = $color_array[$n%3];
														@endphp
														<li class="timeline-items {{ $class }} active">
															<h6 class="timeline-title">{{ $val['event']['event_name'] }}</h6>
															<div class="timeline-content">
																@if($val['event_type'] == 'Training')
																<p class="timeline-text"><strong>{{ __('languages.event.Training') }}</strong></p>
																@elseif($val['event_type'] == 'Activity')
																<p class="timeline-text"><strong>{{ __('languages.event.Activity') }}</strong></p>
																@elseif($val['event_type'] == 'honour')
																<p class="timeline-text"><strong>{{ __('languages.event.Guard_of_honour') }}</strong></p>
																@elseif($val['event_type'] == 'community')
																<p class="timeline-text"><strong>{{ __('languages.event.community') }}</strong></p>
																@elseif($val['event_type'] == 'Headquatters')
																<p class="timeline-text"><strong>{{ __('languages.event.Headquatters') }}</strong></p>
																@elseif($val['event_type'] == 'administration')
																<p class="timeline-text"><strong>{{ __('languages.event.Administration') }}</strong></p>
																@elseif($val['event_type'] == 'other')
																<p class="timeline-text"><strong>{{ __('languages.event.Other') }}</strong></p>
																@else
																<p class="timeline-text"></p>
																@endif
																<div class="col-md-2">
																	<p class="timeline-text">{{ __('languages.Attendance.In_Time') }} : {{ $val['in_time'] }}</p>
																</div>
																<div class="col-md-2">
																	<p class="timeline-text">{{ __('languages.Attendance.Out_Time') }} : {{ $val['out_time'] }}</p>
																</div>
															</div>
															<div class="timeline-content">
																<p class="timeline-text"><strong>{{ __('languages.Attendance.Total Hour') }} : {{ $val['users']['hour_point'] }}</strong></p>
																<div class="col-md-2">
																	<p class="timeline-text"><strong>{{ __('languages.Attendance.Used Hour') }} : {{ $val['hours'] }}</strong></p>
																</div>
																<div class="col-md-3">
																	<p class="timeline-text"><strong>{{ __('languages.Attendance.Remaining Hours') }} : {{ $val['remaining_hour'] }}</strong></p>
																</div>
															</div>
														</li>
														@endforeach
													</ul>
													<div class="activity-serach-cls"></div>
													<!-- Dont't remove div and class -->
													<div class="load-more-activity-serach-cls"></div>
													<!-- Dont't remove div and class -->
													<div class="text-center">
														<button class="btn btn-primary load-more-viewattendance" last_id ="{{ $val['id'] }}">{{ __('languages.Attendance.View_all_activity') }}</button>
													</div>
													@else
													<ul class="widget-timeline">
														<li>{{ __('languages.Attendance.No_activity_found') }}</li>
													</ul>
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="profile" aria-labelledby="profile-tab" role="tabpanel">
										<div class="card">
											<div class="card-content">
												<div class="card-body">
													<div class="row">
														<div class="col-6">
															<h5 class="card-title mb-1"><i class="bx bx-info-circle"></i> {{ __('languages.member.Personal_Information') }}</h5>
															<table class="table table-borderless">
																<tbody>
																	<tr>
																		<td>{{ __('languages.member.Chinese_name') }}:</td>
																		<td class="users-view-username">{{ $user_view['Chinese_name'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.English_name') }}:</td>
																		<td class="users-view-username">{{ $user_view['English_name'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Member_Number') }}:</td>
																		<td class="users-view-name">C{{ $user_view['MemberCode'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.team') }}:</td>
																		<td class="users-view-verified">{{ $user_view['elite']['elite_'.app()->getLocale()]}}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Rank') }}:</td>
																		<td class="users-view-verified">{{ $user_view['rank']['subelite_'.app()->getLocale()]}}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.date_of_birth') }}:</td>
																		<td>{{ date('d F, Y',strtotime($user_view['DOB'])) }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Gender') }}:</td>
																		@if($user_view['Gender'] == 1)
																			<td>{{ __('languages.member.Male') }}</td>
																		@else
																			<td>{{ __('languages.member.Female') }}</td>
																		@endif
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Contact_number') }}:</td>
																		<td>{{ $user_view['Contact_number'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Health_declaration') }}:</td>
																		@if($user_view['Health_declaration'] == '1')
																			<td>{{ __('languages.Yes') }} - {{ $user_view['Health_declaration_text'] }}</td>
																		@else
																			<td>{{ __('languages.No') }}</td>
																		@endif
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Emergency_Contact_Name') }}:</td>
																		<td class="users-view-username">{{ $user_view['Emergency_contact_name'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Emergency_Number') }}:</td>
																		<td class="users-view-name">{{ $user_view['EmergencyContact'] }}</td>
																	</tr>
																	<tr>
																		<td>{{ __('languages.member.Relationship') }}:</td>
																		<td class="users-view-verified">
																			@if($user_view['Relationship'] == '1')
																			{{ __('languages.member.Father_Son') }}
																			@elseif($user_view['Relationship'] == '2')
																			{{ __('languages.member.Mother_Son') }}
																			@elseif($user_view['Relationship'] == '3')
																			{{ __('languages.member.Father_Daugther') }}
																			@elseif($user_view['Relationship'] == '4')
																			{{ __('languages.member.Mother_Daugther') }}
																			@elseif($user_view['Relationship'] == '5')
																			{{ __('languages.member.Brother_sister') }}
																			@elseif($user_view['Relationship'] == '6')
																			{{ __('languages.member.other') }}
																			@endif
																		</td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div class="col-6">
															<h5 class="card-title">{{ __('languages.member.basic_information') }}</h5>
															<ul class="list-unstyled">
																<li><i class="cursor-pointer bx bx-map mb-1 mr-50"></i>{{ $user_view['English_address'] }}</li>
																<li><i class="cursor-pointer bx bx-phone-call mb-1 mr-50"></i>{{ $user_view['Contact_number'] }}</li>
																<li><i class="cursor-pointer bx bx-time mb-1 mr-50"></i>{{ date('d F, Y',strtotime($user_view['JoinDate'])) }}</li>
																<li><i class="cursor-pointer bx bx-envelope mb-1 mr-50"></i>{{ $user_view['email'] }}</li>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
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
@include('layouts.footer')
<!-- /footer content -->
@endsection