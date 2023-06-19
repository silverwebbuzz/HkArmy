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
							<div class="table-responsive">
								<table id="logtable" class="table">
									<thead>
										<tr>
											<th>No</th>
											<th>Old Value</th>
											<th>New Value</th>
										</tr>
									</thead>
									<tbody>
										@if($auditlog)
										@php
											$auditlogs  = json_decode($auditlog->Log);
										
										@endphp
										@foreach($auditlogs as $key => $val)
											<tr>
												<td>{{ $loop->iteration}}</td>
												@foreach($val as $key => $row)
													@php
													$keyvalue = str_replace(array('old_value_', 'new_value_','_'), array('', '',' '),$key);
													
													if($keyvalue == 'Remarks'){
														$row = Helper::getremarksData($row);
													}
													if($keyvalue == 'Qualification'){
														$row = Helper::getqualificationData($row);
													}
													if($keyvalue == 'team'){
														$row = Helper::geteliteData($row);
													}
													if($keyvalue == 'elite team'){
														$row = Helper::getSubeliteData($row);
													}
													@endphp											
													<td>{{ ucfirst($keyvalue) }} => {{ json_encode($row) }}</td>
												@endforeach
												@php
												//print_r($row); die;	
												@endphp
											</tr>
										@endforeach
										@endif
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
<!-- /footer content -->
@endsection