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
						<h4 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Product.Edit_Product') }}</h4>
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
									<form method="POST" action="{{ url('product',$product['id']) }}" id="editproductForm" name="editproductForm" class="editproductForm" enctype='multipart/form-data'>
										<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
										{{ method_field('PUT') }}
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_name">{{__('languages.product_type')}}</label>
													<fieldset class="form-group">
														<select class="form-control" id="product_type" name="product_type">
															<option value="">{{__('languages.select_product_type')}}</option>
															<option value="1" @if($product['product_type']==1) selected @endif>{{__('languages.single_product')}}</option>
															<option value="2" @if($product['product_type']==2) selected @endif>{{__('languages.combo_product')}}</option>
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
													<input type="text" class="form-control required" id="product_name" name="product_name" placeholder="{{ __('languages.Product.Product_name') }}" value="{{ $product['product_name'] }}">
													<small class="text-danger">{{ $errors->first('product_name') }}</small>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_sku">{{ __('languages.Product.product_prifix') }}</label>
													<input type="text" class="form-control required" id="product_sku" name="product_sku" placeholder="{{ __('languages.Product.Product_sku') }}" value="{{ $product['product_sku'] }}" readonly>
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
																	<option value="{{$cat->id}}" @if($product['uniformType'] == $cat->id) selected @endif>{{$cat->$columnName}}</option>
																@endforeach
															@endif
															<!-- <option value="">{{ __('languages.Product.Select_Uniform_Type') }}</option>
															<option value="1" @if($product['uniformType'] == '1') selected @endif>Uniform Type 1</option>
															<option value="2" @if($product['uniformType'] == '2') selected @endif>Uniform Type 2</option>
															<option value="3" @if($product['uniformType'] == '3') selected @endif>Uniform Type 3</option> -->
														</select>
														<small class="text-danger">{{ $errors->first('uniformType') }}</small>
													</fieldset>
												</div>
											</div>
											<div class="col-md-6"></div>
										</div>

										<!-- Single Product option suffix section Start -->
										<div id="single-product-suffix-section" @if($ChildProducts->isNotEmpty()) style="display:block;" @else style="display:none;" @endif>
											@if($ChildProducts->isNotEmpty())
											@foreach($ChildProducts as $key => $childProduct)
											<div class="row">
												<div class="col-md-6"></div>
												<div class="col-md-6 products-suffix-details append-product-sku-html">
													<div class="col-md-5">
														<div class="form-group">
															<label for="product_name">{{ __('languages.Product.option_code') }}</label>
															<input type="text" class="form-control required" id="product_suffix" name="product_suffix[]" placeholder="{{ __('languages.Product.option_code') }}" value="{{$childProduct['product_suffix']}}">
															<small class="text-danger">{{ $errors->first('product_name') }}</small>
														</div>
													</div>
													<div class="col-md-5">
														<div class="form-group">
															<label for="product_name">{{ __('languages.Product.option_name') }}</label>
															<input type="text" class="form-control required" id="product_suffix_name" name="product_suffix_name[]" placeholder="{{ __('languages.Product.option_name') }}" value="{{$childProduct['product_suffix_name']}}">
															<small class="text-danger">{{ $errors->first('product_name') }}</small>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<button type="button" class="btn btn-primary btn-sm deleteChildProduct" data-productid="{{$product['id']}}" data-id="{{$childProduct->id}}">X</button>
														</div>
													</div>
												</div>
											</div>
											@endforeach
											@endif
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
										<div id="combo-product-suffix-section" @if(!empty($ComboProducts)) style="display:block;" @else style="display:none;" @endif>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="location">{{__('languages.product')}}</label>
														<fieldset class="form-group">
														<select id="combo_product_list" name="combo_product_ids[]" multiple>
															@if(!empty($ProductList))
															@foreach($ProductList as $productData)
															<option value="{{$productData['id']}}" 
															@if(isset($product['combo_product_ids']) && in_array($productData['id'],explode(',',$product['combo_product_ids'])))
															selected
															@endif
															>{{$productData['product_name']}}</option>
															@endforeach
															@endif
														</select>
													</fieldset>
													</div>
												</div>
											</div>
											<div id="append_combo_product_suffix">
												@if(!empty($ComboProducts))
												@foreach($ComboProducts as $key => $ComboProduct)
												<div class="product-suffix-sec">
													<label class="combo-product-name">{{$ComboProduct['product_name']}}</label>
													@foreach($ComboProduct['childProducts'] as $key => $ProductSuffix)
													<div class="row">
														<div class="col-md-12 products-suffix-details append-product-sku-html">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="product_name">{{ __('languages.Product.option_code') }}</label>
																	<input type="text" class="form-control required" id="product_suffix" name="combo_product_suffix[]" value="{{$ProductSuffix['product_suffix']}}" placeholder="{{ __('languages.Product.option_code') }}" readonly>
																	<small class="text-danger">{{ $errors->first('product_name') }}</small>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="product_name">{{ __('languages.Product.option_name') }}</label>
																	<input type="text" class="form-control required" id="product_suffix_name" name="combo_product_suffix_name[]" value="{{$ProductSuffix['product_suffix_name']}}" placeholder="{{ __('languages.Product.option_name') }}" readonly>
																	<small class="text-danger">{{ $errors->first('product_name') }}</small>
																</div>
															</div>
														</div>
														<div class="col-md-6"></div>
													</div>
													@endforeach
												</div>
												@endforeach
												@endif
											</div>
										</div>
										<!-- Combo Product option suffix section End -->


										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="users-list-role">{{ __('languages.event.Post Type') }}</label>
													<fieldset class="form-group">
														<select class="form-control eventPostType" id="postType" name="post_type">
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
															<option value="1" @if($product['status'] == '1') selected @endif>{{ __('languages.Active') }}</option>
															<option value="2" @if($product['status'] == '2') selected @endif>{{ __('languages.Inactive') }}</option>
														</select>
														<small class="text-danger">{{ $errors->first('status') }}</small>
													</fieldset>
												</div>
											</div>
										</div>

										<div class="form-row">
											<!-- Start Event Moeny Html -->
											@php $money = 1 @endphp
											<div id="eventMoney" class="form-group col-md-3 mb-50 col-lg-3">
												<div class="addmore-eventmoney-section">
												@foreach($costType as $post)
												@if($post->cost_type == 1)
													@if($post->cost_value != 0)
													<label class="text-bold-600" for="event_money">{{ __('languages.Product.product_money') }}</label>
													<div class="main-event-drop">
														<input type="text" class="form-control" id="event_money" name="event_money[{{$post->id}}][]" placeholder="{{ __('languages.Product.product_money') }}" value="{{ $post->cost_value }}">
														<a class="btn btn-primary btn-sm removeMoney deleteProductPostType" data-productid="{{$post->product_id}}" data-postid="{{$post->id}}" data-posttype="Money">X</a>
													</div>
													@endif
												@php $money++ @endphp
												@endif
												@endforeach
												</div>
												<button type="button" class="btn btn-primary btn-sm addMoreProductCostType" data-id="1">{{ __('languages.Product.add_product_money') }}</button>
											</div>
											<!-- End Event Moeny Html -->
											
											<!-- Start Event Token Html -->
											@php $token = 1 @endphp
											<div id="eventToken" class="form-group col-md-3 mb-50 col-lg-3">
												<div class="addmore-eventtoken-section">
												@foreach($costType as $post)
													@if($post->cost_type == 2)
													<label class="text-bold-600" for="event_money">{{ __('languages.Product.product_token') }}</label>
													<div class="main-event-drop">
														<input type="text" class="form-control" id="event_token" name="event_token[{{$post->id}}][]" placeholder="{{ __('languages.Product.product_token') }}" value="{{ $post->cost_value }}"> 
														<a class="btn btn-primary btn-sm removeToken deleteProductPostType" data-productid="{{$post->product_id}}" data-postid="{{$post->id}}" data-posttype="Token">X</a>
													</div>
													@php $token++ @endphp
													@endif
												@endforeach
												</div>
												<button type="button" class="btn btn-primary btn-sm addMoreProductCostType" data-id="2">{{ __('languages.Product.add_product_token') }}</button>
											</div>
											<!-- End Event Token Html -->

											<div class="form-group col-md-6 mb-50 col-lg-6 moneyandtoken">
												<div class="event-token-money-section">
													@php $i = 0 @endphp
													@foreach($costType as $post)
													@if($post->cost_type == 3)
													<?php $value = explode("+", $post->cost_value);?>
													<div class="add-evens-cls1 main-money-token">
														<div class="form-group col-md-5 mb-50">
															<input type="text" class="form-control" id="event_plus_money" name="event_money_token[{{$i}}][{{$post->id}}][money]" placeholder="{{ __('languages.Product.product_money') }}" value="{{ $value[0] ?? $value[0] }}">
														</div>
														<div class="form-group col-md-5 mb-50">
															<input type="text" class="form-control" id="event_plus_token" name="event_money_token[{{$i}}][{{$post->id}}][token]" placeholder="{{ __('languages.Product.product_token') }}" value="{{ $value[1] ?? $value[1] }}">
														</div>
														<!-- <div class="form-group col-md-2 money-token-btn"> -->
															<!-- <a class="removeMoneyToken deletePostType btn btn-primary btn-sm">X</a> -->
															<a class="btn btn-primary btn-sm removeMoneyToken deleteProductPostType" data-productid="{{$post->product_id}}" data-postid="{{$post->id}}" data-posttype="Money_Token">X</a>
														<!-- </div> -->
													</div>
													@php $i++ @endphp
													@endif
													@endforeach
												</div>
												<button type="button" class="btn btn-primary addMoreProductCostType MoneyTokenAdd-btn ml-1" data-id="3" lastid="{{$i}}">{{ __('languages.Product.add_product_money_token') }}</button>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<label for="location">{{ __('languages.Product.Description') }}</label>
												<fieldset class="form-group">
													<textarea class="form-control" name="description" id="description" rows="3" placeholder="{{ __('languages.Product.Description') }}">{{ $product['description'] }}</textarea>
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
										<div class="row">
											<div class="col-12">
												<div class="allImages-cls">
													@if(!empty($product['product_image']))
													@php
													$images = explode(',',$product['product_image']);
													@endphp
													@foreach($images as $val)
													<div class="single-image-cls">
														<img src="{{ asset($val) }}" width="150" height="150">
														<a href="javascript:void(0);" class="removeImage" data-image="{{ $val }}" data-id="{{ $product['id'] }}"><i class="bx bx-trash-alt"></i></a>
													</div>
													@endforeach
													@else
													<div class="single-image-cls">
														<img src="{{ asset('app-assets/images/NoImageAvailable.png') }}" width="150" height="150">
													</div>
													@endif
												</div>
											</div>
										</div>

										{{--<div class="col-12 add_new_product">
											<div class="checkbox">
												<input type="checkbox" name="add_more_product" class="checkbox-input addMoreProduct" id="checkbox1" value="1" @if($product['parent_id'] !='0') checked="" @endif>
												<label for="checkbox1">{{ __('languages.Product.various_product') }}</label>
											</div>
										</div>


										<div class="row addMoreProductTbl" @if($product['parent_id'] =='0') style="display: none;" @endif>
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
															<thead id="add_item">
																@if(!empty($product['item']))
																@foreach($product['item'] as $pro_item_value)
																<tr class="item-row">
																	<td>{{ $pro_item_value['product_name'] }}
																		<input type="hidden" class="form-control item" placeholder="Item" type="text" name="items[]" value="{{ $pro_item_value['id'] }}">
																	</td>
																	<td>
																		{{ $pro_item_value['product_sku'] }}
																	</td>
																	<td width="15%">
																		<div class="delete-btn">
																			<span class="currency_wrapper"></span>
																			<input type="hidden" class="form-control item" placeholder="Item" type="text" name="product_add_amount" value="{{ $pro_item_value['product_amount'] }}">
																			<span class="total">{{ $pro_item_value['product_amount'] }}</span>
																			<a href="javascript:void(0);" class="deleteProductItem"><i class="bx bx-trash-alt"></i> </a>
																		</div>
																	</td>
																</tr>
																@endforeach
																@endif
															</thead>
															<tr>
																<td colspan="3" class="p-0">
																	<a href="#" class="btn btn-default add_item_btn"><i class="bx bx-plus-circle"></i> Add Product</a>
																</td>
															</tr>
															<tr id="products_list_inv" style="display: none;">
																<td colspan="4" class="p-0">
																	<div class="card product-add-more-card">
																		<div class="card-content">
																			<div class="card-body">
																				<div id="load_product" class="pro-scroll">
																					<a href="#" class="cancel-inv" style="float: right;display: block;width: 100%;text-align: right;">×</a>
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
															<tr class="product-table">
																<td></td>
																<td class="text-right"><strong>Sub Total</strong></td>
																<td>
																	<span class="currency_wrapper">₹</span>
																	<span id="subtotal">{{ ($product['sub_amount']) ? $product['sub_amount'] : '00:00'}}</span> 
																	<input type="hidden" class="sub_amount" name="sub_amount" value="{{ ($product['sub_amount']) ? $product['sub_amount'] : '00:00'}}">
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										--}}

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