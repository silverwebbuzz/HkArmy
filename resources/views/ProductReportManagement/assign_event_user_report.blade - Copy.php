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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.sidebar.Assign User Report') }}</h3>
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
				<!-- <div class="row border rounded py-2 mb-2">
					<form action="{{ route('assign-user-report') }}" method="GET">
						

						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group position-relative has-icon-left">
								<input type="text" class="form-control filter_date_enroll" id="filter_date_enroll" name="filter_date_attendance" placeholder="{{ __('languages.Select_date') }}" autocomplete="off" value="@if(isset($_GET['filter_date_attendance']) && !empty($_GET['filter_date_attendance'])) {{ $_GET['filter_date_attendance'] }} @endif">
								<div class="form-control-position">
									<i class="bx bx-calendar-check"></i>
								</div>
							</fieldset>
						</div>

						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group">
								<select class="form-control filter_event_type" name="filter_event_type">
									<option value="">{{ __('languages.event.Select_event_type') }} </option>
									@if(!empty($get_event_type_list))
									@php
									echo $get_event_type_list;
									@endphp
									@endif
								</select>
							</fieldset>
						</div>

						<div class="float-left align-items-center ml-1">
							<fieldset class="form-group">
								<select class="form-control" name="filter_event_id">
									<option value="">{{ __('languages.event.Select_Event') }} </option>
									@if(!empty($EventList1))
									@foreach($EventList1 as $Events)
									<option value="{{$Events['id']}}"  @if(isset($_GET['filter_event_id']) && $Events['id'] == $_GET['filter_event_id']) selected @endif>{{ $Events['event_name'] }}</option>
									@endforeach
									@endif
								</select>
							</fieldset>
						</div>

						<div class="float-right align-items-center ml-1">
							<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Search') }} " name="search">
						</div>
					</form>
				</div> -->
				{{-- Export Button Start --}}
				<div class="row mb-2">
					<div class="float-right align-items-center ml-1">
						<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-product-assign-user-report mb-0"> {{__('languages.export')}} {{ __('languages.sidebar.Enrollment_Product') }}</a>
					</div>
				</div>
				{{-- Export Button End --}}

				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="assignUserReport" class="table assignUserReportTbl">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="allproductIDs[]" class="select-all-assign-user-report-chkbox" value="all">
												</th>
												<th>{{ __('languages.RoleManagement.Sr_No') }}</th>
												<th>{{ __('languages.Product.product_code') }}</th>
												<th>{{ __('languages.Product.Product_name') }}</th>
												<th>{{ __('languages.Product.option_code') }} + {{ __('languages.Product.option_name') }}</th>
												<th>{{ __('languages.cost_method') }}</th>
												<th>{{ __('languages.Product.Date') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($ProductList))
											@foreach($ProductList as $product)
											<tr>
												<td>
													<input type="checkbox" name="productIDs[]" class="select-assign-user-report-chkbox" value="{{$product['id']}}">
												</td>
												<td>{{ $product['id'] }}</td>
												<td>{{ $product['product_sku'] ?? '' }}</td>
												<td>{{ $product['product_name'] }}</td>
												<td>
													@if(!empty($product['child_products']))
													<ul>
													@foreach($product['child_products'] as $childProduct)
													<li>{{$childProduct['product_suffix']}}+{{$childProduct['product_suffix_name']}}</li>
													@endforeach
													</ul>
													@endif
												</td>
												<!-- <td>{{ Helper::getProductToken($product['id']) }}</td> -->
												<td>
													<ul>
													<?php
													if(isset($product['product_cost_type']) && !empty($product['product_cost_type'])){
														foreach($product['product_cost_type'] as $productCostType){
															if($productCostType['cost_type'] == 1){
																echo '<li>'.__('languages.member.Money').' : '.$productCostType['cost_value'].'</li>';
															}
															if($productCostType['cost_type'] == 2){
																echo '<li>'.__('languages.member.Tokens').' : '.$productCostType['cost_value'].'</li>';
															}
															if($productCostType['cost_type'] == 3){
																$explodeProductCostType = explode("+",$productCostType['cost_value']);
																echo '<li>'.__('languages.member.Money').' : '.$explodeProductCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeProductCostType[1].'</li>';
															}
														}
													}
													?>
													</ul>
												</td>
												<td>{{ Helper::dateConvertDDMMYYY('-','/',$product['date']) }}</td>
												<td>
													<button type="button" class="btn btn-outline-success block productAssignMember" data-toggle="modal" data-backdrop="false" data-target="#backdrop1" modal-product-id="{{ $product['id'] }}" modal-product-name="{{ $product['product_name'] }}" modal-product-code="{{ $product['product_sku'] }}">{{ __('languages.member.View Member') }}</button>
													<button type="button" class="btn btn-outline-primary block ProductaddMember" data-toggle="modal" data-backdrop="false" data-target="#backdrop" modal-product-id="{{ $product['id'] }}" modal-product-name="{{ $product['product_name'] }}">{{ __('languages.Add Member') }}</button>
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
				</section>
			</div>
		</div>
	</div>


	<!-- footer content -->
	@include('layouts.footer')
	<!-- /footer content -->

	<!--Disabled Backdrop Modal -->
	<div class="modal fade text-left addmember_modal" id="backdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel4">{{ __('languages.Add Member') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="bx bx-x"></i>
					</button>
				</div>
				<div class="modal-body">
					<p>
						<span class="modal-event-text">{{ __('languages.Product.product_code') }} : </span><span class="modal-product-code"></span>
						</br>
						<span class="modal-event-text">{{ __('languages.Product.Product_name') }} : </span><span class="modal-product-name"></span>
						</br>
						<span class="modal-event-text">{{ __('languages.Product.option_code') }} + {{ __('languages.Product.option_name') }} : </span><span class="childProductHtml"></span>
						<div class="form-row addmember_modal_form">
							<div class="form-group">
								<input type="hidden" name="eventModal" value="">
								<label for="users-list-role">{{ __('languages.Member') }}</label>
								<select class="form-control" id="membermodal" name="membermodal">
									<option value="">{{__('languages.member.select_member')}}</option>
								</select>
							</div>
						</div>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-secondary" data-dismiss="modal">
						<i class="bx bx-x d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
					</button>
					<button type="button" class="btn btn-primary ml-1 assignMemberToProduct">
						<i class="bx bx-check d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Save') }}</span>
					</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade text-left addmember_modal" id="backdrop1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel4">{{ __('languages.member.View Member') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i class="bx bx-x"></i>
					</button>
				</div>
				<div class="modal-body">
					<p>
						<span class="modal-event-text">{{ __('languages.Product.product_code') }} : </span><span class="modal-product-code"></span>
						</br>
						<span class="modal-event-text">{{ __('languages.Product.Product_name') }} : </span><span class="modal-product-name"></span>
						</br>
						<span class="modal-event-text">{{ __('languages.Product.option_code') }} + {{ __('languages.Product.option_name') }} : </span><span class="childProductHtml"></span>
						<input type="hidden" value="" id="hiddeneventId">
						<div class="add_member_modal_form">
							<div class="form-group">
								<input type="hidden" name="eventModal" value="">
								<label for="users-list-role">{{ __('languages.Add Member') }}</label>
								<select class="form-control" id="membermodal1" name="membermodal1">
									<option value="">{{__('languages.member.select_member')}}</option>
								</select>
							</div>
							<button type="button" class="btn btn-primary ml-1 product_assign_member_event">
								<i class="bx bx-check d-block d-sm-none"></i>
								<span class="d-none d-sm-block">{{ __('languages.Save') }}</span>
							</button>
						</div>
						<div class="table-responsive viewAssignMember"></div>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-secondary" data-dismiss="modal">
						<i class="bx bx-x d-block d-sm-none"></i>
						<span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
<div class="modal fade" id="exportAssignUserReportSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAssignUserReportSelectField" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">{{__('languages.export_fields.select_export_fields')}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="all-assign_user_report-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="assign_user_report-field-checkbox" value="product_code" checked>
						<span>{{__('languages.Product.product_code')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="assign_user_report-field-checkbox" value="product_name" checked>
						<span>{{__('languages.Product.Product_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="assign_user_report-field-checkbox" value="option_code_and_option_name" checked>
						<span>{{ __('languages.Product.option_code') }} + {{ __('languages.Product.option_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="assign_user_report-field-checkbox" value="cost_method" checked>
						<span>{{__('languages.cost_method')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportAssignUserReportFields[]" class="assign_user_report-field-checkbox" value="date" checked>
						<span>{{__('languages.Product.Date')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAssignUserReport()">{{__('languages.export')}} {{ __('languages.sidebar.Enrollment_Product') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportAssignUserReportFieldColumnList = ['product_code','product_name','option_code_and_option_name','cost_method','date'];
var ProductIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-assign-user-report-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#assignUserReport")
			.DataTable()
			.table("#assignUserReport")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-assign-user-report-chkbox").prop('checked', true);
				var productid = row.closest('tr').find(".select-assign-user-report-chkbox").val();
				if (ProductIds.indexOf(productid) !== -1) {
					// Current value is exists in array
				} else {
					ProductIds.push(productid);
				}
			});
		} else {
			$("#assignUserReport")
			.DataTable()
			.table("#assignUserReport")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-assign-user-report-chkbox").prop('checked', false);
			});
			ProductIds = [];
		}
	});

	$(document).on("click", ".select-assign-user-report-chkbox", function (){
		productid = $(this).val();
		if ($(this).is(":checked")) {
			if (ProductIds.indexOf(productid) !== -1) {
				// Current value is exists in array
			} else {
				ProductIds.push(productid);
			}
		} else {
			ProductIds = $.grep(ProductIds, function(value) {
				return value != productid;
			});
		}
	});

	$(document).on("click", ".export-product-assign-user-report", function () {
		$("#exportAssignUserReportSelectField").modal('show');
	});

	$(document).on("click", ".all-assign_user_report-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".assign_user_report-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var eventColumnName = $(this).val();
				if (ExportAssignUserReportFieldColumnList.indexOf(eventColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportAssignUserReportFieldColumnList.push(eventColumnName);
				}
			});
		} else {
			$(".assign_user_report-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportAssignUserReportFieldColumnList = [];
		}
	});

	$(document).on("click", ".assign_user_report-field-checkbox", function (){
		if($('.assign_user_report-field-checkbox').length === $('.assign_user_report-field-checkbox:checked').length){
			$(".all-assign_user_report-field-checkbox").prop('checked',true);
		}else{
			$(".all-assign_user_report-field-checkbox").prop('checked',false);
		}
		var productColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportAssignUserReportFieldColumnList.indexOf(productColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportAssignUserReportFieldColumnList.push(productColumnName);
			}
		} else {
			ExportAssignUserReportFieldColumnList = $.grep(ExportAssignUserReportFieldColumnList, function(value) {
				return value != productColumnName;
			});
		}
	});
});

function exportAssignUserReport(){
	if($('.assign_user_report-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/enrollment_product",
			data: {
				'columnList' : ExportAssignUserReportFieldColumnList,
				'productIds' : ProductIds
			},
			contentType: 'application/json; charset=utf-8',
			success: function (data) {
				//return false;
				var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
				if (!isHTML(data)) {
					var downloadLink = document.createElement("a");
					var fileData = ["\ufeff" + data];

					var blobObject = new Blob(fileData, {
						type: "text/csv;charset=utf-8;",
					});

					var url = URL.createObjectURL(blobObject);
					downloadLink.href = url;
					downloadLink.download = "EnrollmentProduct.csv";

					document.body.appendChild(downloadLink);
					downloadLink.click();
					document.body.removeChild(downloadLink);
				}
			},
		});
	}
}
</script>
	@endsection


	