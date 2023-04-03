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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.ProductList.ProductList') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="decks" class="product-card">
				<div class="row match-height">
					<div class="col-12 col-sm-12 col-md-12">
						<div class="card-deck-wrapper">
							<div class="card-deck">
								@if(!empty($products))
									@foreach($products as $val)
									@php
										$images = explode(",",$val['product_image']);
									@endphp
										<div class="col-md-3 col-sm-6">
											<div class="card product-list-cls">
												<div class="card-content">
													<img class="card-img-top" src="{{ asset($images[0]) }}" alt="Card image cap" height="250px" width="150px" />
													<div class="card-body">
														<h4 class="card-title">{{ $val['product_name'] }}</h4>
														<p class="card-text">{{ $val['product_sku'] }} </p>
														<p class="card-text">${{ $val['product_amount'] }}</p>
														<div class="card-btn  text-center">
															<a href="javascript:void(0);" class="btn btn-primary mt-50 add-to-cart" data-product-id="{{ $val['id'] }}" data-user-id="{{ Session::get('user')['user_id'] }}" data-amount="{{ $val['product_amount'] }}">{{ __('languages.ProductList.Add_to_cart') }}</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									@endforeach
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