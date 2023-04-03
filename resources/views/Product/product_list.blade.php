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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Product.Product') }}</h3>
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
						<a href="{{ route('product.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bxl-product-hunt"></i> {{ __('languages.Product.Add_Product') }}</a>
					</div>
				</div>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center ml-1">
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-products mb-0"> {{ __('languages.export') }} {{ __('languages.sidebar.Product') }}</a>
				</div>
			</div>
			{{-- Export Button End --}}
			<div class="users-list-table">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<div class="table-responsive">
								<table id="productTable" class="table">
									<thead>
										<tr>
											<th>
												<input type="checkbox" name="allproductIDs[]" class="select-all-product-chkbox" value="all">
											</th>
											<th>{{ __('languages.Product.Image') }}</th>
											<th>{{__('languages.is_combo_product')}}</th>
											<th>{{ __('languages.Product.Product_name') }}</th>
											<th>{{ __('languages.Product.Product_sku') }}</th>
											<th>{{__('languages.cost_method')}}</th>
											<th>{{ __('languages.Product.Amount') }}</th>
											<th>{{ __('languages.Product.Date') }}</th>
											<th>{{ __('languages.Status') }}</th>
											<th>{{ __('languages.Action') }}</th>
										</tr>
									</thead>
									<tbody>
										@if(!empty($Products))
											@foreach($Products as $val)
											@php
											$product_image = [];
											if(!empty($val['product_image'])){
												$product_image = explode(',',$val['product_image']);
											}
											@endphp
											<tr>
												<td>
													<input type="checkbox" name="productIDs[]" class="select-product-chkbox" value="{{$val['id']}}">
												</td>
												@if(!empty($product_image))
													<td><img src="{{ asset($product_image[0]) }}" width="50" height="50"></td>
												@else
													<td><img src="{{ asset('app-assets/images/NoImageAvailable.png') }}" width="50" height="50" alt="no-image"></td>
												@endif
												<td>
													@if($val['product_type']==2)
														{{__('languages.Yes')}}
													@else
													{{__('languages.No')}}
													@endif
												</td>
												<td>{{ $val['product_name'] }}</td>
												<td>{{ $val['product_sku'] }}</td>
												<td>
													<ul>
													<?php
													if(isset($val['product_cost_type']) && !empty($val['product_cost_type'])){
														foreach($val['product_cost_type'] as $productCostType){
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
												<td>{{ $val['product_amount'] }}</td>
												<td>{{ Helper::dateConvertDDMMYYY('-','/',$val['date']) }}</td>
												@if($val['status'] == '1')
													<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
												@else
													<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
												@endif
												<td>
													<a href="{{ route('product.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
													<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deleteproduct"><i class="bx bx-trash-alt"></i> </a>
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

<!-- Modal -->
<div class="modal fade" id="exportProductSelectField" tabindex="-1" role="dialog" aria-labelledby="exportProductSelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportProductFields[]" class="all-product-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="product_image" checked>
						<span>{{__('languages.Product.Image')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="product_name" checked>
						<span>{{__('languages.Product.Product_name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="product_sku" checked>
						<span>{{__('languages.Product.Product_sku')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="product_cost_method" checked>
						<span>{{__('languages.cost_method')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="amount" checked>
						<span>{{__('languages.Product.Amount')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="product_date" checked>
						<span>{{__('languages.Product.Date')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductFields[]" class="product-field-checkbox" value="status" checked>
						<span>{{__('languages.Status')}}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportProduct()">{{ __('languages.export') }} {{ __('languages.sidebar.Product') }}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportProductFieldColumnList = ['product_image','product_name','product_sku','product_cost_method','amount','product_date','status'];
var ProductIds = [];
$(function () {
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-product-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#productTable")
			.DataTable()
			.table("#productTable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-product-chkbox").prop('checked', true);
				var eventid = row.closest('tr').find(".select-product-chkbox").val();
				if (ProductIds.indexOf(eventid) !== -1) {
					// Current value is exists in array
				} else {
					ProductIds.push(eventid);
				}
			});
		} else {
			$("#productTable")
			.DataTable()
			.table("#productTable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-product-chkbox").prop('checked', false);
			});
			ProductIds = [];
		}
	});

	$(document).on("click", ".select-product-chkbox", function (){
		if($('.select-product-chkbox').length === $('.select-product-chkbox:checked').length){
			$(".select-all-product-chkbox").prop('checked',true);
		}else{
			$(".select-all-product-chkbox").prop('checked',false);
		}
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

	$(document).on("click", ".export-products", function () {
		$("#exportProductSelectField").modal('show');
	});

	$(document).on("click", ".all-product-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".product-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var productColumnName = $(this).val();
				if (ExportProductFieldColumnList.indexOf(productColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportProductFieldColumnList.push(productColumnName);
				}
			});
		} else {
			$(".product-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportProductFieldColumnList = [];
		}
	});

	$(document).on("click", ".product-field-checkbox", function (){
		if($('.product-field-checkbox').length === $('.product-field-checkbox:checked').length){
			$(".all-product-field-checkbox").prop('checked',true);
		}else{
			$(".all-product-field-checkbox").prop('checked',false);
		}
		var productColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportProductFieldColumnList.indexOf(productColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportProductFieldColumnList.push(productColumnName);
			}
		} else {
			ExportProductFieldColumnList = $.grep(ExportProductFieldColumnList, function(value) {
				return value != productColumnName;
			});
		}
	});
});

function exportProduct(){
	if($('.product-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/product",
			data: {
				'columnList' : ExportProductFieldColumnList,
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
					downloadLink.download = "Products.csv";

					document.body.appendChild(downloadLink);
					downloadLink.click();
					document.body.removeChild(downloadLink);
				}
			},
		});
	}
}
</script>
<!-- /footer content -->
@endsection