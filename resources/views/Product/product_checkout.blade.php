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
						<h3 class="content-header-title float-left pr-1 mb-0">Checkout Details</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section class="simple-validation">
				<form class="form-horizontal" name="orderForm" id="orderForm" method="post">
					<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
					<div class="row">
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">Billing Details</h4>
								</div>
								<div class="card-content">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="controls">
														<label for="first-name">First Name</label>
														<input type="text" name="firstname" class="form-control" placeholder="First Name">
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<div class="controls">
														<label for="last-namel">Last Name</label>
														<input type="text" name="lastname" class="form-control" placeholder="Last Name">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical"> Email address </label>
													<input type="email" id="email" class="form-control" name="email" placeholder="Email">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Phone Number</label>
													<input type="text" id="phname " class="form-control" name="phname" placeholder="Phone number">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">CompanY Name(Optional)</label>
													<input type="text" id="compnay_name" class="form-control" name="compnay_name" placeholder="Company Name">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Street Address</label>
													<input type="text" id="street_address" class="form-control" name="street_address" placeholder="House number and street name">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Apartment, suite, unit etc. (optional) </label>
													<input type="text" id="aname" class="form-control" name="aname" placeholder="Apartment, suite, unit etc. (optional) " >
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Town / City </label>
													<input type="text" id="city" class="form-control" name="city" placeholder="Town/ City">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Country</label>
													<input type="text" id="country" class="form-control" name="country" placeholder="Country">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Postcode</label>
													<input type="text" id="postcode" class="form-control" name="postcode" placeholder="Postcode">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card shapping-address-sec">
								<h3 id="ship-to-different-address">
									<label for="ship-to-different-address-checkbox" class="checkbox">Ship to a different address?</label>
									<input id="ship-to-different-address-checkbox" class="input-checkbox ship_address" type="checkbox" name="ship_to_different_address" value="1">
								</h3>
								<div class="card-content shapping-address" style="display: none;">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="controls">
														<label for="first-name">First Name</label>
														<input type="text" name="ship_first_name" class="form-control" placeholder="First Name">
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<div class="controls">
														<label for="last-namel">Last Name</label>
														<input type="text" name="ship_last_name" class="form-control" placeholder="Last name">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical"> Email address </label>
													<input type="email" id="ship_email" class="form-control" name="ship_email" placeholder="Email">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Phone Number</label>
													<input type="text" id="ship_phone_number " class="form-control" name="ship_phone_number" placeholder="Phone number">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">CompanY Name(Optional)</label>
													<input type="text" id="ship_company_name" class="form-control" name="ship_company_name" placeholder="Company Name">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Street Address</label>
													<input type="text" id="street" class="form-control" name="ship_street_address" placeholder="House number and street name">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Apartment, suite, unit etc. (optional) </label>
													<input type="text" id="ship_aprt_name" class="form-control" name="ship_aprt_name" placeholder="Apartment, suite, unit etc. (optional)">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Town / City </label>
													<input type="text" id="ship_city" class="form-control" name="ship_city" placeholder="Town / City">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Country</label>
													<input type="text" id="ship_country" class="form-control" name="ship_country" placeholder="Country">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Postcode</label>
													<input type="text" id="ship_postcode" class="form-control" name="ship_postcode" placeholder="PostCode">
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for="first-name-vertical">Order notes </label>
													<textarea id="ship_order_note" class="form-control" name="ship_order_note" placeholder="Notes about your order, e.g. special notes for delivery." ></textarea> 
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 payment-methode">
							<div class="card order-pay">
								<h4 id="order_review_heading">
								Your order</h3>
								<div id="payment" class="checkout-payment">
									<ul class="payment_methods payment_methods methods">
										<li class="payment_method payment_method_paypal">
											<input id="payment_method_paypal" type="radio" class="input-radio" name="payment_method" value="paypal" checked="checked" data-order_button_text="Proceed to PayPal">
											<label for="payment_method_paypal">
											Pay <img src="{{ asset('app-assets/images/payment-method.png') }}"><a href="javascript:void(0);" class="about_paypal">What is PayPal?</a>  </label>
											<div class="payment_box payment_method_paypal">
												<p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
											</div>
										</li>
									</ul>
									<div class="card-btn  text-right">
										<input type="submit" name="orderplace" id="orderplace" class="btn btn-primary mt-50" value="Place Order">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</section>
		</div>
	</div>
</div>
<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection