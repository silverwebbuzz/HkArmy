@extends('layouts.app')

@section('content')
<!-- top navigation -->
@include('layouts.header')
<!-- /top navigation -->
@include('layouts.sidebar')

<style>
	.products-suffix-details{
		display: flex;
		padding: 0px;	
	}
	.products-suffix-details .col-md-5{
		padding-right: 0px;
	}
	.products-suffix-details button{
		width: 75%;
	}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-12 mb-2 mt-1">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h4 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Product.Add_Product') }}</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="validation">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form method="POST" action="{{ url('product') }}" id="productForm1" name="productForm" class="productForm" enctype='multipart/form-data'>
										<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
										
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_name">{{__('languages.product_type')}}</label>
													<fieldset class="form-group">
														<select class="form-control" id="product_type" name="product_type">
															<option value="">{{__('languages.select_product_type')}}</option>
															<option value="1">{{__('languages.single_product')}}</option>
															<option value="2">{{__('languages.combo_product')}}</option>
														</select>
														<small class="text-danger"></small>
													</fieldset>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_name">{{ __('languages.Product.Product_name') }}</label>
													<input type="text" class="form-control required" id="product_name" name="product_name" placeholder="{{ __('languages.Product.Product_name') }}" value="{{ old('product_name') }}">
													<small class="text-danger">{{ $errors->first('product_name') }}</small>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_sku">{{ __('languages.Product.product_prifix') }}</label>
													<input type="text" class="form-control required" id="product_sku" name="product_sku" placeholder="{{ __('languages.Product.Product_sku') }}" value="{{$GenerateProductSku}}" readonly>
													<small class="text-danger">{{ $errors->first('product_sku') }}</small>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="location">{{ __('languages.Product.Categories') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="uniformType" name="uniformType">
															<option value="">{{ __('languages.Product.Select_categories') }}</option>
															@if(!empty($Categories))
																@foreach($Categories as $cat)
																	@php
																	$columnName = 'name_'.app()->getLocale();
																	@endphp
																	<option value="{{$cat->id}}" @if(old('uniformType') == $cat->id) selected @endif>{{$cat->$columnName}}</option>
																@endforeach
															@endif
														</select>
														<small class="text-danger">{{ $errors->first('uniformType') }}</small>
													</fieldset>
													
												</div>
											</div>
										</div>

										<!-- Single Product option suffix section Start -->
										<div id="single-product-suffix-section" style="display:none;">
											<div class="row">
												<div class="col-md-6"></div>
												<div class="col-md-6 products-suffix-details append-product-sku-html">
													<div class="col-md-5">
														<div class="form-group">
															<label for="product_name">{{ __('languages.Product.option_code') }}</label>
															<input type="text" class="form-control required" id="product_suffix" name="product_suffix[]" placeholder="{{ __('languages.Product.option_code') }}" value="">
															<small class="text-danger">{{ $errors->first('product_name') }}</small>
														</div>
													</div>
													<div class="col-md-5">
														<div class="form-group">
															<label for="product_name">{{ __('languages.Product.option_name') }}</label>
															<input type="text" class="form-control required" id="product_suffix_name" name="product_suffix_name[]" placeholder="{{ __('languages.Product.option_name') }}" value="">
															<small class="text-danger">{{ $errors->first('product_name') }}</small>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<button type="button" class="btn btn-primary btn-sm deleteChildProduct">X</button>
														</div>
													</div>
												</div>
											</div>
											<div class="append-product-suffix"></div>
											<div class="row">
												<div class="col-md-6">
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<button type="button" class="btn btn-primary btn-sm" id="add_product_suffix">{{ __('languages.Product.add_more_suffix') }}</button>
													</div>
												</div>
											</div>
										</div>
										<!-- Single Product option suffix section End -->

										<!-- Combo Product option suffix section Start -->
										<div id="combo-product-suffix-section" style="display:none;">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="location">{{__('languages.product')}}</label>
														<fieldset class="form-group">
														<select id="combo_product_list" name="combo_product_ids[]" multiple>
															@if(!empty($ProductList))
															@foreach($ProductList as $product)
															<option value="{{$product['id']}}">{{$product['product_name']}}</option>
															@endforeach
															@endif
														</select>
													</fieldset>
													</div>
												</div>
											</div>
											<div id="append_combo_product_suffix"></div>
										</div>
										<!-- Combo Product option suffix section End -->

										<div class="row">
											<!-- <div class="col-md-6">
												<div class="form-group">
													<label for="product_amount">{{ __('languages.Product.Amount') }}</label>
													<input type="text" class="form-control required" id="product_amount" name="product_amount" placeholder="{{ __('languages.Product.Amount') }}" value="{{ old('product_amount') }}">
													<small class="text-danger">{{ $errors->first('product_amount') }}</small>
												</div>
											</div> -->
											<div class="col-md-6">
												<div class="form-group">
													<label for="users-list-role">{{ __('languages.event.Post Type') }}</label>
													<fieldset class="form-group">
														<select class="form-control productPostType" id="postType" name="post_type">
															<option value="">{{ __('languages.event.Select_post_type') }} </option>
															<option value="1"> {{ __('languages.event.Money') }}</option>
															<option value="2">{{ __('languages.event.Tokens') }}</option>
															<option value="3">{{ __('languages.event.Money_Tokens') }}</option>
														</select>
													</fieldset>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="location">{{ __('languages.Status') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="status" name="status">
															<option value="1">{{ __('languages.Active') }}</option>
															<option value="2">{{ __('languages.Inactive') }}</option>
														</select>
														<small class="text-danger">{{ $errors->first('status') }}</small>
													</fieldset>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div id="eventMoney" class="form-group col-md-3 mb-50 col-lg-3" style="display: none;">
												<div class="addmore-eventmoney-section">
													<label class="text-bold-600" for="event_money">{{ __('languages.Product.product_money') }}</label>
													<div class="main-event-drop">
														<input type="text" class="form-control" id="event_money" name="event_money[]" placeholder="{{ __('languages.Product.product_money') }}" value="">
														<a class="removeMoney deletePostType btn btn-primary btn-sm" >X</a>
													</div>
												</div>												
												<button type="button" class="btn btn-sm btn-primary addMoreProductCostType" data-id="1">{{ __('languages.Product.add_product_money') }}</button>
											</div>

											<div id="eventToken" class="form-group col-md-3 mb-50 col-lg-3" style="display: none;">
												<div class="addmore-eventtoken-section">
													<label class="text-bold-600" for="event_money">{{ __('languages.Product.product_token') }}</label>
													<div class="main-event-drop">
														<input type="text" class="form-control" id="event_token" name="event_token[]" placeholder="{{ __('languages.Product.product_token') }}" value=""> 
														<a class="removeToken deletePostType btn btn-primary btn-sm" >X</a>
													</div>
												</div>
												<button type="button" class="btn btn-primary btn-sm addMoreProductCostType" data-id="2">{{ __('languages.Product.add_product_token') }}</button>
											</div>

											<div class="form-group col-md-6 mb-50 col-lg-6" id="eventMoneyToken" style="display: none;">
												<div class="event-token-money-section">
													<div class="add-evens-cls1 main-money-token">
														<div class="form-group col-md-5 mb-50">
															<label class="text-bold-600" for="event_money_token">{{ __('languages.Product.add_product_money_token') }}</label>
															<input type="text" class="form-control" id="event_plus_money" name="event_money_token[0][money]" placeholder="{{ __('languages.Product.product_money') }}" value="">
														</div>
														<div class="form-group col-md-5 mb-50">
															<label class="text-bold-600" for="event_money_token"></label>
															<input type="text" class="form-control" id="event_plus_token" name="event_money_token[0][token]" placeholder="{{ __('languages.Product.product_token') }}" value="">
														</div>
														<div class="form-group col-md-2 money-token-btn">
															<label class="text-bold-600" for="event_money_token"></label>
															<a class="removeMoneyToken deletePostType btn btn-primary btn-sm" data-posttype="Money_Token">X</a>
														</div>
													</div>
												</div>
												<button type="button" class="btn btn-primary addMoreProductCostType MoneyTokenAdd-btn ml-1" data-id="3" style="display: none;" lastid="1">{{ __('languages.Product.add_product_money_token') }}</button>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<label for="location">{{ __('languages.Product.Description') }}</label>
												<fieldset class="form-group">
													<textarea class="form-control" name="description" id="description" rows="3" placeholder="{{ __('languages.Product.Description') }}">{{ old('description') }}</textarea>
													<small class="text-danger">{{ $errors->first('description') }}</small>
												</fieldset>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													<label for="location">{{ __('languages.Product.Upload_image') }}</label>
													<fieldset class="form-group">
														<input type="file" name="product_image[]" class="form-control" id="product_image" multiple>
													</fieldset>
												</div>
											</div>
										</div>
										

										{{--
										<div class="col-12 add_new_product">
											<div class="checkbox">
												<input type="checkbox" name="add_more_product" class="checkbox-input addMoreProduct" id="checkbox1" value="1">
												<label for="checkbox1">{{ __('languages.Product.various_product') }}</label>
											</div>
										</div>


										<div class="row addMoreProductTbl" style="display: none;">
											<div class="col-md-12">
												<div class="table-responsive">
													<table class="table">
														<thead>
															<tr class="item-row">
																<th>{{ __('languages.Product.Product_name') }}</th>
																<th>{{ __('languages.Product.Product_sku') }}</th>
																<th>{{ __('languages.Product.Amount') }}</th>
															</tr>
														</thead>
														<tbody>
															<thead id="add_item"></thead>
															<tr>
																<td colspan="3" class="p-0">
																	<a href="#" class="btn btn-default add_item_btn"><i class="bx bx-plus-circle"></i> Add Product</a>
																</td>
															</tr>
															<!--Add Product modal Start-->
															<tr id="products_list_inv" style="display: none;">
																<td colspan="4" class="p-0">
																<!--<div class="inv-product br-10 dshadow">
																		<div id="load_product" class="pro-scroll">
																			<a href="#" class="cancel-inv">×</a>
																			@if(!empty($Products))
																			@foreach($Products as $val)
																			<div data-id="{{ $val['id'] }}" class="row product-item" id="inv_item_{{ $val['id'] }}">
																				<div class="col-6">
																					<p class="mb-0">{{ $val['product_name'] }}</p>
																					<p class="mb-0 text-muted"> </p>
																				</div>
																				<div class="col-6 text-right">
																					<span class="currency_wrapper">₹</span>{{ $val['product_amount'] }}       
																				</div>
																			</div>
																			@endforeach
																			@endif
																		</div>
																	</div> -->
																	<div class="card product-add-more-card">
																		<div class="card-content">
																			<div class="card-body">
																				<div id="load_product" class="pro-scroll">
																					<a href="#" class="cancel-inv" style="
																					float: right;
																					display: block;
																					width: 100%;
																					text-align: right;
																					">×</a>
																					@if(!empty($Products))
																					@foreach($Products as $val)
																					<div data-id="{{ $val['id'] }}" class="row product-item" id="inv_item_{{ $val['id'] }}">
																						<div class="col-6">
																							<p class="mb-0">{{ $val['product_name'] }}</p>
																							<p class="mb-0 text-muted"> </p>
																						</div>
																						<div class="col-6 text-right">
																							<span class="currency_wrapper">₹</span>{{ $val['product_amount'] }}   
																						</div>
																					</div>
																					@endforeach
																					@endif
																				</div>
																			</div>
																		</div>
																	</div>
																</td>
															</tr>
															<!--Add Product modal End-->
															<!-- Sub total and grand total start -->
															<tr class="product-table">
																<td></td>
																<td class="text-right"><strong>Sub Total</strong></td>
																<td>
																	<span class="currency_wrapper">₹</span>
																	<span id="subtotal">00.00</span>
																	<input type="hidden" class="sub_amount" name="sub_amount" value="00.00">
																	<!-- <input type="text" class="total_amount" name="total_amount" value="0"> -->
																</td>
															</tr>
															<!-- Sub total and grand total end -->
														</tbody>
													</table>
												</div>
											</div>
										</div>--}}
										<button type="submit" class="btn btn-primary mr-1 mb-1">{{ __('languages.Submit') }}</button>
										<button type="reset" class="btn btn-light-secondary mr-1 mb-1">{{ __('languages.Reset') }}</button>
									</form>
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
<script>
	$(document).ready(function() {       
	$('#combo_product_list').multiselect({		
		nonSelectedText: SELECT_PRODUCT			
	});
});
</script>
@endsection