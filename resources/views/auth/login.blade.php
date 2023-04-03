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
				<!-- login page start -->
				<section id="auth-login" class="row flexbox-container">
					<div class="col-xl-8 col-11">
						<div class="card bg-authentication mb-0">
							<div class="row m-0">
								<div class="col-md-6 col-12 px-0">
									<div class="card disable-rounded-right mb-0 p-2 h-100 d-flex">
										<div class="card-header pb-1">
											<div class="card-title">
												<h4 class="text-center mb-2">Login</h4>
											</div>
										</div>
										<div class="row loginwith-cls">
											<div class="col-md-6">
												<h4><a href="javascript:void(0);" class="btn btn-primary logincls">Login</a></h4>
											</div>
											<div class="col-md-6">
												<h4><a href="javascript:void(0);" class="btn btn-primary qrlogin-cls">QR With Login</a></h4>
											</div>
										</div>
										<div class="card-content logindiv">
											<div class="card-body">
												<form id="loginform" method="POST">
													<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
													<div class="form-group mb-50">
														<label class="text-bold-600" for="Email">Email address</label>
														<input type="email" class="form-control" id="emailaddress" name="email" placeholder="Email address"></div>
														<div class="form-group">
															<label class="text-bold-600" for="Password">Password</label>
															<input type="password" class="form-control" id="password" name="password" placeholder="Password">
														</div>
														<div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
															<div class="text-left">
																<div class="checkbox checkbox-sm">
																	<input type="checkbox" class="form-check-input" id="exampleCheck1">
																	<label class="checkboxsmall" for="exampleCheck1"><small>Keep me logged in</small></label>
																</div>
															</div>
															<div class="text-right"><a href="{{ url('forgetPassword') }}" class="card-link"><small>Forgot Password?</small></a></div>
														</div>
														<div class="form-group mb-2 forgot-password-cls">
															<input type="submit" name="login" value="Login" class="btn btn-primary glow w-100 position-relative">
															<i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
														</div>
													</form>
													<!-- <div class="text-center">
														<small class="mr-25">Don't have an account?</small>
														<a href="{{ route('register') }}"><small>Sign up</small></a>
													</div> -->
												</div>
											</div>
											<div class="qrlogindiv" style="display: none;">
												<div class="container">
													<div class="row">
														<div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
														<!-- <div id="reader" style="display: inline-block;"></div>
														<div class="empty"></div>
														<div id="scanned-result"></div> -->
														<div id="qr-reader" style="display: inline-block;">
															
														</div>
														<div id="qr-reader-results">

														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
									<div class="card-content">
										<img class="img-fluid" src="{{ asset('app-assets/images/pages/login.png') }}" alt="branding logo">
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</body>
@include('auth.template.footer')
<!-- <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script> -->

<script src="{{ asset('assets/js/newQR.js') }}"></script>

<script type="384012db0c04f93982830514-text/javascript">
	function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete"
        	|| document.readyState === "interactive") {
            // call on next available tick
        setTimeout(fn, 1);
    } else {
    	document.addEventListener("DOMContentLoaded", fn);
    }
}

function onScanSuccess(qrCodeMessage) { /** decoded message */ }
var html5QrcodeScanner = new Html5QrcodeScanner(
	"qr-reader", { fps: 10, qrbox: 250 });
html5QrcodeScanner.render(onScanSuccess);

docReady(function () {
	var resultContainer = document.getElementById('qr-reader-results');
	var lastResult, countResults = 0;
	function onScanSuccess(qrCodeMessage) {
		html5QrcodeScanner.clear();
		var parts = qrCodeMessage.split("/");
		var user_id = parts[0];
		var email = parts[1];
		$("#cover-spin").show();
		$.ajax({
			type: "POST",
			url : BASE_URL+"/check-qr-login",
			data: {
				"_token": $('#csrf-token').val(),
				'user_id' : user_id,
				'email' : email,
			},
			success : function(response) {
				$("#cover-spin").hide();
				var data = JSON.parse(JSON.stringify(response));
				html5QrcodeScanner.clear();
				if(data.status){
					toastr.success(data.message);
					window.location = BASE_URL+data.redirecturl;
				}else{
					toastr.error(data.message);
					setTimeout(function(){
						window.location = BASE_URL+data.redirecturl;
					}, 1000);
				}
			}
		});
	}

	var html5QrcodeScanner = new Html5QrcodeScanner(
		"qr-reader", { fps: 10, qrbox: 250 });
	html5QrcodeScanner.render(onScanSuccess);
});
</script>
<!-- <script src="https://ajax.cloudflare.com/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="384012db0c04f93982830514-|49" defer=""></script> -->
<script src="{{ asset('assets/js/rocket-loader.min.js') }}" data-cf-settings="384012db0c04f93982830514-|49" defer=""></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".logincls").click(function(){
			$(".logindiv").show("");
			$(".qrlogindiv").hide("");
		});
		$(".qrlogin-cls").click(function(){
			$(".qrlogindiv").show("");
			$(".logindiv").hide("");
		});
	});
	
</script>
</html>