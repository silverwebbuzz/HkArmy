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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Remarks.Remarks') }}</h3>
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
						<a href="{{ route('remarks.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.Remarks.Add_Remarks') }}</a>
					</div>
				</div>
			</div>
			
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<a href="{{route('import-remarks')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.Remarks.Remarks') }}</a>
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-remark mb-0"> {{ __('languages.export') }} {{ __('languages.Remarks.Remarks') }}</a>
					<a href="{{asset('uploads\sample_files\remark.csv')}}">
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
													<input type="checkbox" name="allRemarkIDs[]" class="select-all-Remark-chkbox" value="all">
												</th>
												<th>{{ __('languages.Sr_No') }}</th>
												<th>{{ __('languages.Remarks.Remarks') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($Remarks))
											@php
											$Remark = 'remarks_'.app()->getLocale();
											@endphp
												@foreach($Remarks as $val)
													<tr>
														<td>
															<input type="checkbox" name="RemarkIDs[]" class="select-Remark-chkbox" value="{{$val['id']}}">
														</td>
														<td>{{$val['id']}}</td>
														<td>{{ $val[$Remark]}}</td>
														@if($val['status'] == "1")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('remarks.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deleteremarks"><i class="bx bx-trash-alt"></i> </a>
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
	RemarkId = [];
	$(document).on("click", ".select-all-Remark-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Remark-chkbox").prop('checked', true);
				var remarkid = row.closest('tr').find(".select-Remark-chkbox").val();
				if (RemarkId.indexOf(remarkid) !== -1) {
					// Current value is exists in array
				} else {
					RemarkId.push(remarkid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Remark-chkbox").prop('checked', false);
			});
			RemarkId = [];
		}
	});

	$(document).on("click", ".select-Remark-chkbox", function (){
		if($('.select-Remark-chkbox').length === $('.select-Remark-chkbox:checked').length){
			$(".select-all-Remark-chkbox").prop('checked',true);
		}else{
			$(".select-all-Remark-chkbox").prop('checked',false);
		}
		remarkid = $(this).val();
		if ($(this).is(":checked")) {
			if (RemarkId.indexOf(remarkid) !== -1) {
				// Current value is exists in array
			} else {
				RemarkId.push(remarkid);
			}
		} else {
			RemarkId = $.grep(RemarkId, function(value) {
				return value != remarkid;
			});
		}
	});
</script>
<!-- /footer content -->
@endsection