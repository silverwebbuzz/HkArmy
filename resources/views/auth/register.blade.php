@include('auth.template.header')
	<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
		<div id="cover-spin"></div>
		<!-- BEGIN: Content-->
		<div class="app-content content scop-sign-up">
			<div class="content-overlay"></div>
			<div class="content-wrapper">
				<div class="content-header row">
				</div>
				<div class="content-body">
					<!-- register section starts -->
					<section class="row flexbox-container">
						<div class="col-xl-8 col-10">
							<div class="card bg-authentication mb-0">
								<div class="row m-0">
									<!-- register section left -->
									<div class="col-md-6 col-12 px-0">
										<div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
											<div class="card-header pb-1">
												<div class="card-title">
													<h4 class="text-center mb-2">Sign Up</h4>
												</div>
											</div>
											<div class="text-center">
												<p> <small> Please enter your details to sign up and be part of our great community</small>
												</p>
											</div>
											<div class="card-content">
												<div class="card-body">
													<form method="POST" id="registerform">
														<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
														<div class="form-group mb-50">
															<label class="text-bold-600" for="exampleInputUsername1">Username</label>
															<input type="text" class="form-control" id="username" name="username" placeholder="Username">
														</div>
														<div class="form-group mb-50">
															<label class="text-bold-600" for="exampleInputUsername1">Email</label>
															<input type="text" class="form-control" id="email" name="email" placeholder="Email Address">
														</div>
														<div class="form-group mb-50">
															<label for="inputfirstname4">Join Date</label>
															<input type="text" class="form-control pickadate join-date-cls" name="join_date" id="join_date">
														</div>
														<div class="form-group mb-50">
															<label for="inputlastname4">Date of Birth</label>
															<input id="dob" name="dob" class="form-control date-of-birth-cls"/>
														</div>
														<div class="form-group mb-50">
															<label for="membercode">Member Code</label>
															<input type="text" class="form-control" id="membercode" name="membercode" placeholder="Member Code" value="{{ $unique_id }}" readonly>
														</div>
														<div class="form-group mb-50">
															<label class="text-bold-600" for="exampleInputEmail1">Address</label>
															<input type="text" class="form-control" id="address" name="address" placeholder=" Address">
														</div>
														<div class="form-row">
															<div class="form-group col-md-6 mb-50">
																<label class="text-bold-600" for="exampleInputPassword1">HKID Number</label>
																<input type="text" class="form-control" id="hkidnumber" name="hkidnumber" placeholder="HKID Number">
															</div>
															<div class="form-group col-md-6 mb-50">
																<label class="text-bold-600" for="exampleInputPassword1">Emergency Contact</label>
																<input type="text" class="form-control" id="emergencycontact" name="emergencycontact" placeholder="Emergency contant">
															</div>
														</div>
														<div class="form-group register-cls">
															<input type="submit" name="submit" class="btn btn-primary glow position-relative w-100" value="SIGN UP">
															<i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
														</div>
													</form>
													<hr>
													<div class="text-center"><small class="mr-25">Already have an account?</small><a href="{{ route('login') }}"><small>Sign in</small> </a></div>
												</div>
											</div>
										</div>
									</div>
									<!-- image section right -->
									<div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
										<img class="img-fluid" src="{{ asset('app-assets/images/pages/register.png') }}" alt="branding logo">
									</div>
								</div>
							</div>
						</div>
					</section>
					<!-- register section endss -->
				</div>
			</div>
		</div>
	@include('auth.template.footer')
	</body>
	<!-- END: Body-->
</html>