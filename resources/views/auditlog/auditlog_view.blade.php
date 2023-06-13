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
					<div class="col-12 col-sm-7">
						<h3 class="content-header-title float-left pr-1 mb-0">View Log</h3>
					</div>
					<div class="col-12 col-sm-5 px-0 d-flex justify-content-end align-items-center px-1 mb-2">
						<a href="{{ url('audit-log') }}" class="btn btn-sm btn-primary"><i id="icon-arrow" class="bx bx-left-arrow-alt"></i>Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
						<div id="content">
                        <ul class="timeline">
							@if($auditlog) 
							@foreach($auditlog as $key => $logs)
							<li class="event" data-date="{{ $key }}">
							@foreach($logs as $val)	
								@if($val['page'] === 'Export')
									<h3>Page : {{ $val['table_name'] }}</h3>
									@php 
										$log  = (array)json_decode($val['Log']);
									@endphp
							   		<p> <b>User : </b> {{ $log['user'] }} </p>
							   		<p class="mb-4"> <b>File : </b> {{ $log['filename'] }} <a href="{{ $log['link'] }}" download="{{ $log['filename'] }}">Download</a></p>
								@elseif($val['page'] === 'Product')
									<h3>Page : {{ $val['table_name'] }}</h3>
									@php 
										$log  = (array)json_decode($val['Log']);
									@endphp
									<p> <b>Product : </b> {{ $log['product'] }} </p>
									<p> <b>Assign Product Order Id : </b> {{ $log['assign_product_order_id'] }} </p>
									<p> <b>Assign To User : </b> {{ $log['assign_to_user'] }} </p>
									<p class="mb-4"> <b>Assign By User : </b> {{ $log['assign_by_user'] }} </p>
								@elseif($val['table_name'] === 'AttendanceManagement')
									<h3>Page : {{ $val['table_name'] }}</h3>
									@php 
										$log  = (array)json_decode($val['Log']);
									@endphp
									<p> <b>Event Name : </b> {{ $log['event_name'] }} </p>
									<p> <b>Event Type : </b> {{ $log['event_type'] }} </p>	
									<p> <b>Date : </b> {{ $log['event_enter_date'] }} </p>
									<p> <b>Time : </b> {{ $log['event_enter_time'] }} </p>
									<p> <b>Member Code : </b> {{ $log['member_code'] }} </p>
									<p> <b>Attending To : </b> {{ $log['attending_to'] }} </p>
									<p class="mb-4"> <b>Attending By : </b> {{ $log['attending _by'] }} </p>
								@elseif($val['table_name'] === 'EventAssign')
									<h3>Page : {{ $val['table_name'] }}</h3>
									@php 
										$log  = (array)json_decode($val['Log']);
									@endphp
									<p> <b>Event Name : </b> {{ $log['Event'] }} </p>
									<p> <b>Event Type : </b> {{ $log['event_type'] }} </p>	
									<p> <b>Assign To User : </b> {{ $log['assign_to_user'] }} </p>
									<p class="mb-4"> <b>Attending By User : </b> {{ $log['assign_by_user'] }} </p>
								@else
									<h3>Page : {{ $val['page'] }}</h3>
									@php
									$log  = json_decode($val['Log']);
									@endphp
									<div class="table-responsive mb-4">
										<table id="logs" class="table">
											<thead>
												<tr>
													<th>No</th>
													<th>Old Value</th>
													<th>New Value</th>
												</tr>
											</thead>
											<tbody>
									 		@foreach($log as $key => $row)
									 		<tr>
									 			<td>{{ $loop->iteration}}</td>	
										 		@foreach($row as $key => $col)
													@php
													$keyvalue = str_replace(array('old_value_', 'new_value_','_'), array('', '',' '),$key);
													if(is_array($col)) {
														$col = implode(" ", $col);
													}
													if($keyvalue == 'Remarks'){
														$col = Helper::getremarksData($col);
													}
													if($keyvalue == 'Qualification'){
														$col = Helper::getqualificationData($col);
													}
													if($keyvalue == 'team'){
														$col = Helper::geteliteData($col);
													}
													if($keyvalue == 'elite team'){
														$col = Helper::getSubeliteData($col);
													}
													@endphp											
													<td>{{ ucfirst($keyvalue) }} => {{ $col }}</td>
												@endforeach			
									 		</tr>		
									 		@endforeach
										</tbody>
										</table>
									</div>
								@endif
						    </li>	
							@endforeach 	
							@endforeach
							@endif
                        </ul>
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