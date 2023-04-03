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
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h4 class="content-header-title float-left pr-1 mb-0">{{ __('languages.PurchaseProduct.PurchaseProduct_add') }}</h4>
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
									<form method="POST" action="{{ url('purchase-product') }}" id="purchaseproductForm" name="purchaseproductForm" class="purchaseproductForm">
									<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="firstName3">{{ __('languages.Product.Product') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="product" name="product">
															<option value="">{{ __('languages.PurchaseProduct.Select_product') }}</option>
															@if(!empty($products))
																@foreach($products as $product)
																	<option value="{{ $product['id'] }}" @if(old('product') == $product['id']) selected @endif>{{ $product['product_name'] }}</option>
																@endforeach
															@endif
														</select>
														<small class="text-danger">{{ $errors->first('product') }}</small>
													</fieldset>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="lastName3">{{ __('languages.PurchaseProduct.Memeber') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="member" name="member">
															<option value="">{{ __('languages.PurchaseProduct.Select_member') }}</option>
																@if(!empty($users))
																@foreach($users as $val)
																	@if(!empty($val['UserName']))
																		<option value="{{ $val['ID'] }}" @if(old('member') == $val['ID']) selected @endif>{{ $val['UserName'] }}</option>
																	@else
																		<option value="{{ $val['ID'] }}" @if(old('member') == $val['ID']) selected @endif>{{ $val['Chinese_name'] }} & {{ $val['English_name'] }}</option>
																	@endif
																@endforeach
															@endif
														</select>
														<small class="text-danger">{{ $errors->first('member') }}</small>
													</fieldset>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="location">{{ __('languages.Product.Uniform_Type') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="product_uniform_type" name="product_uniform_type">
															<option value="">{{ __('languages.Product.Select_Uniform_Type') }}</option>
															<option value="1" @if(old('product_uniform_type') == '1') selected @endif>Uniform Type 1</option>
															<option value="2" @if(old('product_uniform_type') == '2') selected @endif>Uniform Type 2</option>
															<option value="3" @if(old('product_uniform_type') == '3') selected @endif>Uniform Type 3</option>
														</select>
														<small class="text-danger">{{ $errors->first('product_uniform_type') }}</small>
													</fieldset>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="location">{{ __('languages.Product.Size') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="product_size" name="product_size">
															<option value="">{{ __('languages.Product.Select_Size') }}</option>
															<option value="s" @if(old('product_size') == 's') selected @endif>S</option>
															<option value="m" @if(old('product_size') == 'm') selected @endif>M</option>
															<option value="l" @if(old('product_size') == 'l') selected @endif>L</option>
															<option value="xl" @if(old('product_size') == 'xl') selected @endif>XL</option>
															<option value="xxl" @if(old('product_size') == 'xxl') selected @endif>XXL</option>
														</select>
														<small class="text-danger">{{ $errors->first('product_size') }}</small>
													</fieldset>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="location">{{ __('languages.PurchaseProduct.Transaction_Type') }}</label>
													<fieldset class="form-group">
														<select class="form-control" id="transaction_type" name="transaction_type">
															<option value="">{{ __('languages.PurchaseProduct.Select_Transaction_Type') }}</option>
															<option value="activity_hours">Activity Hours</option>
															<option value="purchase">Purchase</option>
														</select>
													</fieldset>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="product_amount">{{ __('languages.Product.Amount') }}</label>
													<input type="text" class="form-control" id="product_amount" name="product_amount" placeholder="{{ __('languages.Product.Amount') }}">
													<small class="text-danger">{{ $errors->first('product_amount') }}</small>
												</div>
											</div>
										</div>
										<input type="submit" class="btn btn-primary mr-1 mb-1" value="{{ __('languages.Submit') }}"/>
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
@endsection