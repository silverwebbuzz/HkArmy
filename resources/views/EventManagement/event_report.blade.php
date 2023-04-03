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
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h4 class="content-header-title float-left pr-1 mb-0">{{ __('languages.event.Event_reports') }}</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section class="users-list-wrapper">
				<div class="users-list-filter px-1">
					<div class="row border rounded py-2 mb-2">
						<div class="float-right align-items-center ml-1 col-md-3">
							<span><b>{{ __('languages.event.Event Name') }}</b> : <span>{{ $events['event_name'] }}</span></span>
						</div>
						<div class="float-right align-items-center ml-1 col-md-2">
							<span><b>{{ __('languages.event.Date') }}</b> : <span>{{ date('d/m/Y',strtotime($events['startdate'])) }}</span></span>
						</div>
						<div class="float-right align-items-center ml-1 col-md-3">
							<span><b>{{ __('languages.event.No_Of_Attendance') }} </b> : <span>{{ count($eventReport) }}</span></span>
						</div>
						<div class="float-right align-items-center ml-1 col-md-3">
							<span><b>{{ __('languages.event.Total_hour') }}</b> : <span>{{ $hourtotal }}</span></span>
						</div>
					</div>
				</div>
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="eventrepotTable" class="table">
										<thead>
											<tr>
												<th>{{ __('languages.event.No') }}</th>
												<th>{{ __('languages.event.Name') }}</th>
												<th>{{ __('languages.event.Result') }}</th>
												<th>{{ __('languages.event.Check_In') }}</th>
												<th>{{ __('languages.event.Check_Out') }}</th>
												<th>{{ __('languages.event.Hours') }}</th>
												<th>{{ __('languages.event.Awards_Badge') }}</th>
											</tr>
										</thead>
										<tbody>
											@if($eventReport)
											@foreach($eventReport as $val)
												<tr>
													<td>{{ $loop->iteration }}</td>
													<td>{{ $val['users']['Chinese_name'] }} & {{ $val['users']['English_name'] }}</td>
													<td>Passed</td>
													<td>{{ $val['in_time'] }}</td>
													<td>{{ $val['out_time'] }}</td>
													<td>{{ $val['hours'] }}</td>
													<td><img src="{{ asset('app-assets/images/MovingOnAwardScoutstoExplorers.png') }}" class="img-fluid" alt="gallery avtar img" height="50px" width="50px"></td>
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