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
						<h3 class="content-header-title float-left pr-1 mb-0">Service Hour Package </h3>
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
							<a href="{{ route('service-hour-package.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i>Add Service Hour Package</a>
						</div>
					</div>
				</div>
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="packagetable" class="table">
										<thead>
											<tr>
												<th>No.</th>
												<th>Package Name</th>
												<th>Hours</th>
												<th>Status</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											@foreach($hourpackages as $val)
												<tr>
													<td>{{ $loop->iteration }}</td>
													<td>{{ ucfirst($val['package_name']) }}</td>
													<td>{{ $val['hours'] }}</td>
													@if($val['status'] == '1')
													<td><span class="badge badge-light-success">Active</span></td>
													@else
													<td><span class="badge badge-light-danger">Inactive</span></td>
													@endif
													<td>
														<a href="{{ route('service-hour-package.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
													</td>
												</tr>
											@endforeach
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