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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Cart.Shopping_cart') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section class="page-user-profile">
				<div class="row">
					<div class="col-md-9 col-12">
						<div class="card p-2">
							@php
								$totalamount = 0;
								$totalqty = 0;
							@endphp
							<div class="left-cart-main">
								@if(!empty($cartproduct))
								@php
									$totalqty = count($cartproduct);
								@endphp
									@foreach($cartproduct as $val)
									@php
										$totalamount += $val['totalAmount'];
										$images = explode(",",$val['get_product']['product_image']);
									@endphp
									<div class="removecartProduct" data-cart-id="{{ $val['id'] }}"><i class="cursor-pointer bx bx-x float-right"></i></div>
									<div class="left-cart-sec" >
										<div class="left-img">
											<img src="{{ asset($images[0]) }}" alt="img" class="product-img" height="200px" width="200px">
										</div>
										<div class="left-detail products-cart-list" data-product-id="{{ $val['id'] }}">
											<h3>{{ $val['get_product']['product_name'] }}</h3>
											<small class="small">In stock</small>
											<p class="p1">$<span class="productAmount-cls" data-amount="{{ $val['get_product']['product_amount'] }}">{{ $val['totalAmount'] }}</span></p>
											<div class="value-button qtyminus" id="decrease" value="Decrease Value">-</div>
											<input type="number" id="number" class="qty-cls" value="{{ $val['qty'] }}"/>
											<div class="value-button qtyplus" id="increase" value="Increase Value">+</div>
										</div>
									</div>
									@endforeach
								@else
									<p>Shopping cart is empty!</p>
								@endif
								<hr>
								<div class="back-submit">
									<div class="card-btn  text-left">
										<a href="{{ url('product-list') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i><span class="pl-1">{{ __('languages.Cart.Countinue_to_shopping') }}</span></a>
									</div>
									<div class="Subtotal text-right">
										<p class="p1">{{ __('languages.Cart.Subtotal_item') }}(<span class="totalsubqty">{{ $totalqty }}</span>) : $<span class="totalsubamount-cls">{{ $totalamount }}</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-12 left-border">
						<div class="card">
							<div class="right-cart-sec">
								<h4>{{ __('languages.Cart.Subtotal') }} (<span class="totalqty">{{ $totalqty }}</span> {{ __('languages.Cart.Item') }}):$<span class="totalamount-cls">{{ $totalamount }}</span></h4>
								@if(!empty($cartproduct))
									<div class="card-btn  text-center">
										<a href="javascript:void(0);" class="btn btn-primary mt-50 checkout-cls">{{ __('languages.Cart.Proceed_to_checkout') }}</a>
									</div>
								@endif
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