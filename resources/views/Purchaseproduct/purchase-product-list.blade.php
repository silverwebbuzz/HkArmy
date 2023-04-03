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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.PurchaseProductList.Purchase_Product_List') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
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
					<form>
						<div class="row border rounded py-2 mb-2">
							<div class="col-12 col-sm-6 col-lg-3">
								<label for="users-list-verified">{{ __('languages.PurchaseProduct.Memeber') }}</label>
								<fieldset class="form-group">
									<select class="form-control" id="member" name="member">
										<option value="">{{ __('languages.PurchaseProduct.Select_member') }}</option>
										@if(!empty($users))
											@foreach($users as $val)
												@if(!empty($val['UserName']))
													<option value="{{ $val['ID'] }}">{{ $val['UserName'] }}</option>
												@else
													<option value="{{ $val['ID'] }}">{{ $val['Chinese_name'] }} & {{ $val['English_name'] }}</option>
												@endif
											@endforeach
										@endif
									</select>
								</fieldset>
							</div>
							<div class="col-12 col-sm-6 col-lg-3">
								<label for="users-list-role">{{ __('languages.Product.Product') }}</label>
								<fieldset class="form-group">
									<select class="form-control" id="productname" name="productname">
										<option value="">{{ __('languages.PurchaseProduct.Select_product') }}</option>
										@if(!empty($products))
											@foreach($products as $product)
												<option value="{{ $product['product_name'] }}">{{ $product['product_name'] }}</option>
											@endforeach
										@endif
									</select>
								</fieldset>
							</div>
							<div class="col-12 col-sm-6 col-lg-2">
								<div class="form-group mb-50">
									<label class="text-bold-600" for="product_sku">{{ __('languages.Product.Product_sku') }}</label>
									<input type="text" class="form-control" name="product_sku" id="product_sku" placeholder="{{ __('languages.Product.Product_sku') }}">
								</div>
							</div>
							<!-- <div class="col-12 col-sm-6 col-lg-3">
								<label for="users-list-role">Uniform Type</label>
								<fieldset class="form-group">
									<select class="form-control" id="users-list-role">
										<option value="">Any</option>
										<option value="User">Uniform Type 1</option>
										<option value="Staff">Uniform Type 2</option>
									</select>
								</fieldset>
							</div> -->
							<!-- <div class="col-12 col-sm-6 col-lg-3">
								<label for="users-list-status">Transaction Type</label>
								<fieldset class="form-group">
									<select class="form-control" id="users-list-status">
										<option value="">Any</option>
										<option value="Active">Transaction Type 1</option>
										<option value="Close">Transaction Type 1</option>
									</select>
								</fieldset>
							</div> -->
							<div class="col-12 col-sm-6 col-lg-2 d-flex align-items-center">
								<button type="button" class="btn btn-primary btn-block glow mb-0 purchase-product-submit">{{ __('languages.Submit') }}</button>
							</div>
							<div class="col-12 col-sm-6 col-lg-2 d-flex align-items-center">
								<button type="reset" class="btn btn-primary btn-block glow mb-0 purchase-product-clear">{{ __('languages.Clear') }}</button>
							</div>
							<div class="col-12 col-sm-6 col-lg-3 my-1 d-flex align-items-center">
								<a href="{{ route('purchase-product.create') }}" type="reset" class="btn btn-primary btn-block glow mb-0"><i class="bx bxs-cart"></i> {{ __('languages.PurchaseProductList.Purchase_product') }}</a>
							</div>
						</div>
					</form>
				</div>
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive search-purchase-cls">						
									<table id="purchase-productTable" class="table purchase-product-cls">
										<thead>
											<tr>
												<th>{{ __('languages.member.Member_Name') }}</th>
												<th>{{ __('languages.member.Member_Number') }}</th>
												<th>{{ __('languages.Product.Product_name') }}</th>
												<th>{{ __('languages.Product.Product_sku') }}</th>
												<th>{{ __('languages.Product.Product_qty') }}</th>
												<th>{{ __('languages.Product.Amount') }}</th>
												<th>{{ __('languages.Product.Date') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($purchaseProducts))
												@foreach($purchaseProducts as $val)
													@if(!empty($val['order_items']))
														@foreach($val['order_items'] as $row)
														<tr>
															@if(!empty($val['get_users']['UserName']))
																<td>{{ $val['get_users']['UserName'] }}</td>
															@else
																<td>{{ $val['get_users']['Chinese_name'] }} & {{ $val['get_users']['English_name'] }}</td>
															@endif
															<td>C{{ $val['get_users']['MemberCode'] }}</td>
															<td>{{ $row['product_name'] }}</td>
															<td>{{ $row['product_sku'] }}</td>
															<td>{{ $row['product_qty'] }}</td>
															<td>${{ $row['product_amount'] }}</td>
															<td>{{ date('d/m/Y',strtotime($val['order_date'])) }}</td>
															<td>
																<a href="{{ route('purchase-product.edit',$row['id']) }}"><i class="bx bx-edit-alt"></i></a>
																<a href="javascript:void(0);" data-id="{{ $row['id'] }}" class="deletepurchaseproduct"><i class="bx bx-trash-alt"></i> </a>
															</td>
														</tr>
														@endforeach
													@endif
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
<!-- /footer content -->
@endsection