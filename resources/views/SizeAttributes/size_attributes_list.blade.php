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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Size_Attributes.Size_Attributes') }}</h3>
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
						<a href="{{ route('size-attributes.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">
							<i class="bx bx-user-plus"></i>{{ __('languages.Size_Attributes.Add_Size_Attribute') }}
						</a>
					</div>
				</div>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center ml-1">
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-size-attributes mb-0"> {{ __('languages.export') }} {{ __('languages.Size_Attributes.Size_Attributes') }}</a>
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
													<input type="checkbox" name="allSizeIDs[]" class="select-all-Size-chkbox" value="all">
												</th>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.Size_Attributes.Size_Attribute_Name') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($SizeAttributeList))
											@php
											$Attribute_name = 'name_'.app()->getLocale();
											@endphp
												@foreach($SizeAttributeList as $val)
													<tr>
														<td>
															<input type="checkbox" name="SizeIDs[]" class="select-Size-chkbox" value="{{$val['id']}}">
														</td>
														<td>{{$val['id']}}</td>
														<td>{{ $val[$Attribute_name]}}</td>
														@if($val['status'] == "1")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('size-attributes.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deletesizeattribute"><i class="bx bx-trash-alt"></i> </a>
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
	SizeIds = [];
	$(document).on("click", ".select-all-Size-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Size-chkbox").prop('checked', true);
				var sizeids = row.closest('tr').find(".select-Size-chkbox").val();
				if (SizeIds.indexOf(sizeids) !== -1) {
					// Current value is exists in array
				} else {
					SizeIds.push(sizeids);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Size-chkbox").prop('checked', false);
			});
			SizeIds = [];
		}
	});

	$(document).on("click", ".select-Size-chkbox", function (){
		if($('.select-Size-chkbox').length === $('.select-Size-chkbox:checked').length){
			$(".select-all-Size-chkbox").prop('checked',true);
		}else{
			$(".select-all-Size-chkbox").prop('checked',false);
		}
		sizeids = $(this).val();
		if ($(this).is(":checked")) {
			if (SizeIds.indexOf(sizeids) !== -1) {
				// Current value is exists in array
			} else {
				SizeIds.push(sizeids);
			}
		} else {
			SizeIds = $.grep(SizeIds, function(value) {
				return value != sizeids;
			});
		}
	});
</script>
<!-- /footer content -->
@endsection