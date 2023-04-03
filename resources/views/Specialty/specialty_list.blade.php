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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Specialty.Specialty') }}</h3>
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
						<a href="{{ route('specialty.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.Specialty.Add_Specialty') }}</a>
					</div>
				</div>
			</div>
			
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<a href="{{route('import-speciality')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.Specialty.Specialty') }}</a>
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-speciality mb-0"> {{ __('languages.export') }} {{ __('languages.Specialty.Specialty') }}</a>
					<a href="{{asset('uploads\sample_files\speciality.csv')}}">
						<button class="btn"><i class="bx bxs-download"></i>{{__('languages.download_sample_file')}}</button>
					</a>
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
												<th>
													<input type="checkbox" name="allSpecialityIDs[]" class="select-all-Speciality-chkbox" value="all">
												</th>
												<th>{{ __('languages.Sr_No') }}</th>
												<th>{{ __('languages.Specialty.Specialty') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($Specialtys))
											@php
											$specialty = 'specialty_'.app()->getLocale();
											@endphp
												@foreach($Specialtys as $val)
													<tr>
														<th>
															<input type="checkbox" name="SpecialityIDs[]" class="select-Speciality-chkbox" value="{{$val['id']}}">
														</th>
														<td>{{$val['id']}}</td>
														<td>{{ $val[$specialty]}}</td>
														@if($val['status'] == "1")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('specialty.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deletespecialty"><i class="bx bx-trash-alt"></i> </a>
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
<script>
	specialistId = [];
	$(document).on("click", ".select-all-Speciality-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Speciality-chkbox").prop('checked', true);
				var specialistid = row.closest('tr').find(".select-Speciality-chkbox").val();
				if (specialistId.indexOf(specialistid) !== -1) {
					// Current value is exists in array
				} else {
					specialistId.push(specialistid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Speciality-chkbox").prop('checked', false);
			});
			specialistId = [];
		}
	});

	$(document).on("click", ".select-Speciality-chkbox", function (){
		if($('.select-Speciality-chkbox').length === $('.select-Speciality-chkbox:checked').length){
			$(".select-all-Speciality-chkbox").prop('checked',true);
		}else{
			$(".select-all-Speciality-chkbox").prop('checked',false);
		}
		specialistid = $(this).val();
		if ($(this).is(":checked")) {
			if (specialistId.indexOf(specialistid) !== -1) {
				// Current value is exists in array
			} else {
				specialistId.push(specialistid);
			}
		} else {
			specialistId = $.grep(specialistId, function(value) {
				return value != specialistid;
			});
		}
	});
</script>
<!-- /footer content -->
@endsection