@include('auth.template.header')
<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
	<div id="cover-spin"></div>
	<!-- BEGIN: Content-->
	<div class="app-content content">
		<div class="content-overlay"></div>
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<!-- forgot password start -->
				<section class="row flexbox-container">
					<div class="col-xl-7 col-md-9 col-10  px-0">
						<div class="card bg-authentication mb-0">
							<div class="row m-0">
								<!-- left section-forgot password -->
								<div class="col-md-6 col-12 px-0">
									<div class="card disable-rounded-right mb-0 p-2">
										<div class="card-header pb-1">
											<div class="card-title">
												<h4 class="text-center mb-2">Forgot Password?</h4>
											</div>
										</div>
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
										<div class="form-group d-flex justify-content-between align-items-center mb-2">
											<div class="text-left">
												<div class="ml-3 ml-md-2 mr-1"><a href="{{ url('login')}}" class="card-link btn btn-outline-primary text-nowrap">Sign
													in</a>
												</div>
											</div>
											<div class="mr-3"><a href="{{ url('register') }}" class="card-link btn btn-outline-primary text-nowrap">Sign
												up</a>
											</div>
										</div>
										<div class="card-content">
											<div class="card-body">
												<div class="text-muted text-center mb-2"><small></small>
												</div>
												<form id="forgotpasswordform" name="forgotpasswordform" method="POST">
													<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
													<div class="form-group mb-2">
														<label class="text-bold-600" for="exampleInputEmailPhone1">Email</label>
														<input type="email" class="form-control" id="emailaddress" name="email" placeholder="Email address">
													</div>
													<div class="form-group mb-2 forgot-password-cls">
														<input type="submit" name="submit" value="Submit" class="btn btn-primary glow w-100 position-relative">
														<i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
													</div>
												</form>
												<div class="text-center mb-2"><a href="{{ url('login') }}"><small class="text-muted">I remembered my password</small></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- right section image -->
								<div class="col-md-6 d-md-block d-none text-center align-self-center">
									<img class="img-fluid" src="{{ asset('app-assets/images/pages/forgot-password.png') }}" alt="branding logo" width="300">
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- forgot password ends -->
			</div>
		</div>
	</div>
</body>
<!-- END: Body-->
@include('auth.template.footer')
</html>