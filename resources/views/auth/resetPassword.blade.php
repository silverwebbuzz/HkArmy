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
												<h4 class="text-center mb-2">Reset Password</h4>
											</div>
										</div>
										<div class="card-content">
											<div class="card-body">
												<div class="text-muted text-center mb-2"><small></small>
												</div>
												<form id="resetPassword" name="resetPassword" method="POST">
													<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
													<input type="hidden" name="rememberToken"  id="rememberToken" value="{{ $token }}">
													<div class="form-group mb-2">
														<label class="text-bold-600" for="Password">New Password</label>
														<input type="text" class="form-control" id="new_password" name="new_password" placeholder="New Password">
													</div>
													<div class="form-group mb-2">
														<label class="text-bold-600" for="Password">Confirm Password</label>
														<input type="text" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
													</div>
													<div class="form-group mb-2 forgot-password-cls">
														<input type="submit" name="submit" value="Submit" class="btn btn-primary glow w-100 position-relative">
														<i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
													</div>
												</form>
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